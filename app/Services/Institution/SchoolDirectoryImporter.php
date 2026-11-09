<?php

namespace App\Services\Institution;

use App\Enums\InstitutionStatus;
use App\Enums\OwnershipType;
use App\Enums\VerificationStatus;
use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Municipality;
use Illuminate\Support\Str;

class SchoolDirectoryImporter
{
    public function __construct(private readonly InstitutionSlugger $slugger) {}

    /**
     * @return array{created: int, skipped: int}
     */
    public function import(string $path): array
    {
        $created = 0;
        $skipped = 0;
        $schoolTypeId = InstitutionType::query()->where('slug', 'school')->value('id');
        $handle = fopen($path, 'rb');
        if ($handle === false || $schoolTypeId === null) {
            return ['created' => 0, 'skipped' => 0];
        }

        while (($line = fgets($handle)) !== false) {
            $row = json_decode($line, true);
            if (! is_array($row)) {
                $skipped++;

                continue;
            }

            $imported = $this->importRow($row, (int) $schoolTypeId);
            $imported ? $created++ : $skipped++;
        }

        fclose($handle);

        return ['created' => $created, 'skipped' => $skipped];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function importRow(array $row, int $schoolTypeId): bool
    {
        $name = trim((string) ($row['name'] ?? ''));
        $districtName = trim((string) ($row['district'] ?? ''));
        $municipalityName = trim((string) ($row['municipality'] ?? ''));
        if ($name === '' || $districtName === '' || $municipalityName === '') {
            return false;
        }

        $district = District::query()->where('name', $districtName)->first();
        $municipality = $district === null ? null : Municipality::query()
            ->where('district_id', $district->id)
            ->where('name', $municipalityName)
            ->first();
        if ($district === null || $municipality === null) {
            return false;
        }

        $exists = Institution::query()
            ->where('district_id', $district->id)
            ->whereRaw('lower(name) = ?', [Str::lower($name)])
            ->exists();
        if ($exists) {
            return false;
        }

        $ownership = OwnershipType::tryFrom((string) ($row['ownership'] ?? '')) ?? OwnershipType::Unknown;
        $slug = trim((string) ($row['slug'] ?? ''));
        if ($slug === '' || Institution::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $this->slugger->generate($name);
        }

        Institution::query()->create([
            'institution_type_id' => $schoolTypeId,
            'name' => $name,
            'name_ne' => $this->nullable($row['name_ne'] ?? null),
            'slug' => $slug,
            'description' => $this->nullable($row['description'] ?? null),
            'established_year' => is_numeric($row['established_year'] ?? null) ? (int) $row['established_year'] : null,
            'ownership_type' => $ownership,
            'province_id' => $district->province_id,
            'district_id' => $district->id,
            'municipality_id' => $municipality->id,
            'address' => $this->nullable($row['address'] ?? null),
            'official_website' => $this->nullable($row['website'] ?? null),
            'status' => InstitutionStatus::Published,
            'verification_status' => VerificationStatus::Unverified,
            'published_at' => now(),
        ]);

        return true;
    }

    private function nullable(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }
}
