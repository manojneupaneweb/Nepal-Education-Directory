<?php

namespace App\Services\Location;

use App\DTOs\GeographyImportReport;
use App\Models\District;
use App\Models\GeographicDataset;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GeographyImporter
{
    public function __construct(private readonly GeographyNormalizer $normalizer) {}

    public function validate(string $directory): GeographyImportReport
    {
        $parsed = $this->parse($directory);

        return $this->report($parsed, dryRun: true, validateOnly: true, persisted: false);
    }

    public function import(string $directory, bool $dryRun = false): GeographyImportReport
    {
        $parsed = $this->parse($directory);

        DB::beginTransaction();

        try {
            $dataset = GeographicDataset::query()->updateOrCreate(
                ['key' => $parsed['manifest']['key']],
                [
                    'name' => $parsed['manifest']['name'],
                    'organization' => $parsed['manifest']['organization'],
                    'source_url' => $parsed['manifest']['source_url'],
                    'version' => $parsed['manifest']['version'] ?? null,
                    'reference_period' => $parsed['manifest']['reference_period'] ?? null,
                    'retrieved_on' => $parsed['manifest']['retrieved_on'] ?? null,
                    'file_checksum' => $parsed['checksum'],
                    'levels' => $parsed['manifest']['levels'],
                    'validation_status' => $parsed['invalid'] === [] ? 'passed' : 'passed_with_gaps',
                    'notes' => $parsed['manifest']['notes'] ?? null,
                    'imported_at' => now(),
                ],
            );

            $this->upsertProvinces($parsed, $dataset->id);
            $this->upsertDistricts($parsed, $dataset->id);
            $this->upsertMunicipalities($parsed, $dataset->id);
            $this->upsertWards($parsed, $dataset->id);

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return $this->report($parsed, $dryRun, false, true);
    }

    /**
     * @param  array<string, mixed>  $parsed
     */
    private function report(array $parsed, bool $dryRun, bool $validateOnly, bool $persisted): GeographyImportReport
    {
        $status = 'passed';

        if ($parsed['missing_levels'] !== []) {
            $status = 'passed_with_gaps';
        }

        if ($parsed['invalid'] !== [] || $parsed['missing_relationships'] !== []) {
            $status = 'passed_with_gaps';
        }

        if (! $persisted && ! $validateOnly && ! $dryRun) {
            $status = 'failed';
        }

        return new GeographyImportReport(
            datasetKey: $parsed['manifest']['key'],
            dryRun: $dryRun,
            validateOnly: $validateOnly,
            levels: $parsed['levels'],
            invalid: $parsed['invalid'],
            manualReview: $parsed['manual_review'],
            missingRelationships: array_merge($parsed['missing_relationships'], $parsed['missing_levels']),
            validationStatus: $status,
            checksum: $parsed['checksum'],
        );
    }

    /**
     * @param  array<string, mixed>  $parsed
     */
    private function upsertProvinces(array &$parsed, int $datasetId): void
    {
        foreach ($parsed['provinces'] as $row) {
            $this->upsertByCode(Province::query(), $row, $datasetId, $parsed, 'province');
        }
    }

    /**
     * @param  array<string, mixed>  $parsed
     */
    private function upsertDistricts(array &$parsed, int $datasetId): void
    {
        $provinces = Province::query()->whereIn('code', array_column($parsed['districts'], 'province_code'))->pluck('id', 'code');

        foreach ($parsed['districts'] as $row) {
            $provinceId = $provinces[$row['province_code']] ?? null;

            if ($provinceId === null) {
                $parsed['missing_relationships'][] = "District {$row['code']} has no province {$row['province_code']}.";
                $parsed['levels']['district']['skipped']++;

                continue;
            }

            $this->upsertByCode(District::query(), $row, $datasetId, $parsed, 'district', [
                'province_id' => $provinceId,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $parsed
     */
    private function upsertMunicipalities(array &$parsed, int $datasetId): void
    {
        $districts = District::query()->whereIn('code', array_column($parsed['municipalities'], 'district_code'))->pluck('id', 'code');

        foreach ($parsed['municipalities'] as $row) {
            $districtId = $districts[$row['district_code']] ?? null;

            if ($districtId === null) {
                $parsed['missing_relationships'][] = "Municipality {$row['code']} has no district {$row['district_code']}.";
                $parsed['levels']['municipality']['skipped']++;

                continue;
            }

            $this->upsertByCode(Municipality::query(), $row, $datasetId, $parsed, 'municipality', [
                'district_id' => $districtId,
                'municipality_type' => $row['municipality_type'],
            ], districtScope: $districtId);
        }
    }

    /**
     * @param  array<string, mixed>  $parsed
     */
    private function upsertWards(array &$parsed, int $datasetId): void
    {
        if ($parsed['wards'] === []) {
            return;
        }

        $municipalities = Municipality::query()->whereIn('code', array_column($parsed['wards'], 'municipality_code'))->pluck('id', 'code');

        foreach ($parsed['wards'] as $row) {
            $municipalityId = $municipalities[$row['municipality_code']] ?? null;

            if ($municipalityId === null) {
                $parsed['missing_relationships'][] = "Ward {$row['ward_number']} has no municipality {$row['municipality_code']}.";
                $parsed['levels']['ward']['skipped']++;

                continue;
            }

            $existing = Ward::query()
                ->where('municipality_id', $municipalityId)
                ->where('ward_number', $row['ward_number'])
                ->first();

            $attributes = [
                'municipality_id' => $municipalityId,
                'ward_number' => $row['ward_number'],
                'name' => $row['name'],
                'name_ne' => $this->keepExistingNepali($existing?->name_ne, $row['name_ne']),
                'code' => $row['code'],
                'geographic_dataset_id' => $datasetId,
            ];

            $this->persist($existing, $attributes, $parsed, 'ward', function () use ($attributes) {
                Ward::query()->create($attributes + ['is_active' => true]);
            });
        }
    }

    /**
     * @param  Builder<Model>  $query
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $parsed
     * @param  array<string, mixed>  $extra
     */
    private function upsertByCode($query, array $row, int $datasetId, array &$parsed, string $level, array $extra = [], ?int $districtScope = null): void
    {
        $existing = (clone $query)->where('code', $row['code'])->first();
        $slugQuery = (clone $query)->where('slug', $row['slug']);

        if ($districtScope !== null) {
            $slugQuery->where('district_id', $districtScope);
        }

        $slugOwner = $slugQuery->first();

        if ($slugOwner !== null && ($existing === null || $slugOwner->getKey() !== $existing->getKey())) {
            $parsed['invalid'][] = "{$level} {$row['code']} would duplicate slug {$row['slug']}.";
            $parsed['levels'][$level]['skipped']++;

            return;
        }

        $attributes = array_merge($extra, [
            'code' => $row['code'],
            'name' => $row['name'],
            'name_ne' => $this->keepExistingNepali($existing?->name_ne, $row['name_ne']),
            'slug' => $row['slug'],
            'geographic_dataset_id' => $datasetId,
        ]);

        $this->persist($existing, $attributes, $parsed, $level, function () use ($query, $attributes) {
            $query->create($attributes + ['is_active' => true]);
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $parsed
     */
    private function persist(mixed $existing, array $attributes, array &$parsed, string $level, callable $create): void
    {
        if ($existing === null) {
            $create();
            $parsed['levels'][$level]['created']++;

            return;
        }

        $changes = [];

        foreach ($attributes as $key => $value) {
            $current = $existing->getAttribute($key);

            if ($current instanceof \BackedEnum) {
                $current = $current->value;
            }

            if ($value instanceof \BackedEnum) {
                $value = $value->value;
                $attributes[$key] = $value;
            }

            if ((string) $current !== (string) $value) {
                $changes[$key] = $value;
            }
        }

        if ($changes === []) {
            $parsed['levels'][$level]['unchanged']++;

            return;
        }

        $existing->fill($changes);
        $existing->save();
        $parsed['levels'][$level]['updated']++;
    }

    private function keepExistingNepali(?string $existing, ?string $incoming): ?string
    {
        if (($incoming === null || $incoming === '') && $existing !== null && $existing !== '') {
            return $existing;
        }

        return $incoming;
    }

    /**
     * @return array<string, mixed>
     */
    private function parse(string $directory): array
    {
        $manifestPath = rtrim($directory, '/').'/manifest.json';

        if (! is_file($manifestPath)) {
            throw new RuntimeException("Geographic manifest was not found at {$manifestPath}.");
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);

        if (! is_array($manifest) || ! isset($manifest['key'], $manifest['name'], $manifest['organization'], $manifest['source_url'], $manifest['levels'])) {
            throw new RuntimeException('Geographic manifest is missing required source fields.');
        }

        $levels = [
            'province' => $this->emptyCounts(),
            'district' => $this->emptyCounts(),
            'municipality' => $this->emptyCounts(),
            'ward' => $this->emptyCounts(),
        ];
        $invalid = [];
        $manualReview = [];
        $missing = [];
        $missingLevels = [];

        $provinces = $this->readTable($directory, 'provinces.csv');
        $districts = $this->readTable($directory, 'districts.csv');
        $municipalities = $this->readTable($directory, 'local-units.csv');
        $wards = is_file(rtrim($directory, '/').'/wards.csv')
            ? $this->readTable($directory, 'wards.csv')
            : null;

        if ($wards === null) {
            $missingLevels[] = 'No wards.csv file was provided. Ward records were not imported.';
        }

        $provinceRows = [];
        $seenCodes = [];

        foreach ($provinces as $index => $row) {
            $levels['province']['discovered']++;
            $code = $this->code($row['code'] ?? '');
            $name = $this->normalizer->englishName((string) ($row['name_en'] ?? ''));
            $slug = $this->normalizer->slug($name);

            if ($code === '' || $name === '' || $slug === '') {
                $invalid[] = 'Province row '.($index + 2).' is missing a code or English name.';
                $levels['province']['skipped']++;

                continue;
            }

            if (isset($seenCodes['province'][$code])) {
                $invalid[] = "Duplicate province code {$code}.";
                $levels['province']['skipped']++;

                continue;
            }

            $seenCodes['province'][$code] = true;
            $nameNe = $this->normalizer->nepaliName($row['name_ne'] ?? null);
            $currentNames = [
                '1' => ['Koshi Province', 'कोशी प्रदेश'],
                '2' => ['Madhesh Province', 'मधेश प्रदेश'],
                '3' => ['Bagmati Province', 'बागमती प्रदेश'],
                '4' => ['Gandaki Province', 'गण्डकी प्रदेश'],
                '5' => ['Lumbini Province', 'लुम्बिनी प्रदेश'],
                '6' => ['Karnali Province', 'कर्णाली प्रदेश'],
                '7' => ['Sudurpashchim Province', 'सुदूरपश्चिम प्रदेश'],
            ];

            if (isset($currentNames[$code]) && preg_match('/^State \d+$/', $name) === 1) {
                [$name, $nameNe] = $currentNames[$code];
            }

            $provinceRows[] = [
                'code' => $code,
                'name' => $name,
                'name_ne' => $nameNe,
                'slug' => $slug,
            ];
        }

        $districtRows = [];

        foreach ($districts as $index => $row) {
            $levels['district']['discovered']++;
            $code = $this->code($row['code'] ?? '');
            $parent = $this->code($row['province_code'] ?? '');
            $name = $this->normalizer->englishName((string) ($row['name_en'] ?? ''));
            $slug = $this->normalizer->slug($name);

            if ($code === '' || $name === '' || $slug === '' || $parent === '') {
                $invalid[] = 'District row '.($index + 2).' is missing a code, province code, or English name.';
                $levels['district']['skipped']++;

                continue;
            }

            if (! isset($seenCodes['province'][$parent])) {
                $missing[] = "District {$code} references missing province {$parent}.";
                $levels['district']['skipped']++;

                continue;
            }

            if (isset($seenCodes['district'][$code])) {
                $invalid[] = "Duplicate district code {$code}.";
                $levels['district']['skipped']++;

                continue;
            }

            $seenCodes['district'][$code] = true;
            $districtRows[] = [
                'code' => $code,
                'province_code' => $parent,
                'name' => $name,
                'name_ne' => $this->normalizer->nepaliName($row['name_ne'] ?? null),
                'slug' => $slug,
            ];
        }

        $municipalityRows = [];

        foreach ($municipalities as $index => $row) {
            $levels['municipality']['discovered']++;
            $code = $this->code($row['code'] ?? '');
            $parent = $this->code($row['district_code'] ?? '');
            $name = $this->normalizer->englishName((string) ($row['name_en'] ?? ''));
            $slug = $this->normalizer->slug($name);
            $type = $this->normalizer->municipalityType($name);

            if ($code === '' || $name === '' || $slug === '' || $parent === '' || $type === null) {
                $invalid[] = 'Local unit row '.($index + 2)." ({$name}) has no supported municipality type or is missing a parent code.";
                $levels['municipality']['skipped']++;

                continue;
            }

            if (! isset($seenCodes['district'][$parent])) {
                $missing[] = "Local unit {$code} references missing district {$parent}.";
                $levels['municipality']['skipped']++;

                continue;
            }

            if (isset($seenCodes['municipality'][$code])) {
                $invalid[] = "Duplicate local-unit code {$code}.";
                $levels['municipality']['skipped']++;

                continue;
            }

            if (str_contains(mb_strtolower($name, 'UTF-8'), 'metropolitian')) {
                $manualReview[] = "Local unit {$code} uses the source spelling \"{$name}\".";
            }

            $seenCodes['municipality'][$code] = true;
            $municipalityRows[] = [
                'code' => $code,
                'district_code' => $parent,
                'name' => $name,
                'name_ne' => $this->normalizer->nepaliName($row['name_ne'] ?? null),
                'slug' => $slug,
                'municipality_type' => $type,
            ];
        }

        $wardRows = [];

        foreach ($wards ?? [] as $index => $row) {
            $levels['ward']['discovered']++;
            $parent = $this->code($row['municipality_code'] ?? '');
            $number = (int) ($row['ward_number'] ?? 0);
            $name = $this->normalizer->englishName((string) ($row['name_en'] ?? ''));

            if ($parent === '' || $number < 1) {
                $invalid[] = 'Ward row '.($index + 2).' is missing a municipality code or ward number.';
                $levels['ward']['skipped']++;

                continue;
            }

            if (! isset($seenCodes['municipality'][$parent])) {
                $missing[] = "Ward {$number} references missing municipality {$parent}.";
                $levels['ward']['skipped']++;

                continue;
            }

            $pair = $parent.':'.$number;

            if (isset($seenCodes['ward'][$pair])) {
                $invalid[] = "Duplicate ward number {$number} in municipality {$parent}.";
                $levels['ward']['skipped']++;

                continue;
            }

            $seenCodes['ward'][$pair] = true;
            $wardRows[] = [
                'municipality_code' => $parent,
                'ward_number' => $number,
                'name' => $name !== '' ? $name : null,
                'name_ne' => $this->normalizer->nepaliName($row['name_ne'] ?? null),
                'code' => $this->code($row['code'] ?? '') ?: null,
            ];
        }

        return [
            'manifest' => $manifest,
            'checksum' => $this->checksum($directory),
            'levels' => $levels,
            'invalid' => $invalid,
            'manual_review' => $manualReview,
            'missing_relationships' => $missing,
            'missing_levels' => $missingLevels,
            'provinces' => $provinceRows,
            'districts' => $districtRows,
            'municipalities' => $municipalityRows,
            'wards' => $wardRows,
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function readTable(string $directory, string $filename): array
    {
        $path = rtrim($directory, '/').'/'.$filename;
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException("Geographic source file was not found at {$path}.");
        }

        $header = fgetcsv($handle);
        $rows = [];

        if ($header === false) {
            fclose($handle);

            return [];
        }

        while (($data = fgetcsv($handle)) !== false) {
            if ($data === [null] || $data === []) {
                continue;
            }

            $rows[] = array_combine($header, array_pad($data, count($header), ''));
        }

        fclose($handle);

        return $rows;
    }

    private function checksum(string $directory): string
    {
        $directory = rtrim($directory, '/');
        $workbook = $directory.'/1695378470_83.xls';
        $contents = is_file($workbook) ? (string) file_get_contents($workbook) : '';

        foreach (['provinces.csv', 'districts.csv', 'local-units.csv', 'wards.csv'] as $file) {
            if (is_file($directory.'/'.$file)) {
                $contents .= (string) file_get_contents($directory.'/'.$file);
            }
        }

        return hash('sha256', $contents);
    }

    private function code(mixed $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (preg_match('/^\d+\.0$/', $value) === 1) {
            return substr($value, 0, -2);
        }

        return $value;
    }

    /**
     * @return array<string, int>
     */
    private function emptyCounts(): array
    {
        return [
            'discovered' => 0,
            'created' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'skipped' => 0,
        ];
    }
}
