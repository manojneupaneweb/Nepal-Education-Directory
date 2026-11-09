<?php

namespace App\Services\Institution;

use App\Enums\ContactType;
use App\Enums\InstitutionStatus;
use App\Enums\OwnershipType;
use App\Enums\VerificationStatus;
use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Municipality;
use Illuminate\Support\Str;

class DistrictSchoolResearchImporter
{
    public function __construct(private readonly InstitutionSlugger $slugger) {}

    /**
     * @return array{created: int, skipped: int, files: int}
     */
    public function importDirectory(string $directory): array
    {
        $created = 0;
        $skipped = 0;
        $files = 0;
        $schoolTypeId = InstitutionType::query()->where('slug', 'school')->value('id');

        foreach (glob(rtrim($directory, '/').'/*.json') ?: [] as $path) {
            $files++;
            $payload = json_decode((string) file_get_contents($path), true);
            if (! is_array($payload)) {
                continue;
            }

            $districtName = (string) ($payload['research_metadata']['district'] ?? '');
            $district = District::query()->where('name', $districtName)->first();
            if ($district === null || $schoolTypeId === null) {
                $skipped += count($payload['schools'] ?? []);

                continue;
            }

            foreach ($payload['schools'] ?? [] as $row) {
                if (! is_array($row)) {
                    $skipped++;

                    continue;
                }

                $result = $this->importRow($row, $district, (int) $schoolTypeId);
                $result ? $created++ : $skipped++;
            }
        }

        return ['created' => $created, 'skipped' => $skipped, 'files' => $files];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function importRow(array $row, District $district, int $schoolTypeId): bool
    {
        $identity = is_array($row['identity'] ?? null) ? $row['identity'] : [];
        $location = is_array($row['location'] ?? null) ? $row['location'] : [];
        $name = trim((string) ($identity['name'] ?? ''));
        if ($name === '') {
            return false;
        }

        $exists = Institution::query()
            ->where('district_id', $district->id)
            ->whereRaw('lower(name) = ?', [Str::lower($name)])
            ->exists();
        if ($exists) {
            return false;
        }

        $municipality = $this->municipality($district, (string) ($location['municipality'] ?? ''));
        if ($municipality === null) {
            return false;
        }

        $ownership = OwnershipType::tryFrom(Str::lower((string) ($identity['ownership'] ?? ''))) ?? OwnershipType::Unknown;
        $website = $this->website($row);
        $programs = $this->programs($row);

        $institution = Institution::query()->create([
            'institution_type_id' => $schoolTypeId,
            'name' => $name,
            'name_ne' => $this->nullableString($identity['name_ne'] ?? null),
            'slug' => $this->slugger->generate($name),
            'description' => $this->nullableString($identity['description'] ?? $identity['short_description'] ?? null),
            'website_sections' => array_filter([
                'about' => array_values(array_filter([$this->nullableString($identity['description'] ?? null)])),
                'programs' => $programs,
            ]),
            'established_year' => is_numeric($identity['established_year'] ?? null) ? (int) $identity['established_year'] : null,
            'ownership_type' => $ownership,
            'province_id' => $district->province_id,
            'district_id' => $district->id,
            'municipality_id' => $municipality->id,
            'address' => $this->nullableString($location['full_address'] ?? null),
            'latitude' => is_numeric($location['latitude'] ?? null) ? $location['latitude'] : null,
            'longitude' => is_numeric($location['longitude'] ?? null) ? $location['longitude'] : null,
            'official_website' => $website,
            'status' => InstitutionStatus::Published,
            'verification_status' => VerificationStatus::Unverified,
            'published_at' => now(),
        ]);

        $this->storeContacts($institution, $row);

        return true;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return list<string>
     */
    private function programs(array $row): array
    {
        $academics = is_array($row['academics'] ?? null) ? $row['academics'] : [];
        $names = [];
        foreach ($academics['programs'] ?? [] as $program) {
            if (! is_array($program)) {
                continue;
            }
            $name = trim((string) ($program['program_name'] ?? ''));
            if ($name !== '') {
                $names[] = $name;
            }
        }

        return $names;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function storeContacts(Institution $institution, array $row): void
    {
        $contact = is_array($row['contact'] ?? null) ? $row['contact'] : [];
        $primaryPhone = true;
        foreach ($contact['phones'] ?? [] as $phone) {
            $value = trim((string) $phone);
            if ($value === '') {
                continue;
            }
            $institution->contacts()->create([
                'contact_type' => ContactType::Phone,
                'contact_value' => $value,
                'is_primary' => $primaryPhone,
                'is_public' => true,
            ]);
            $primaryPhone = false;
        }

        foreach ($contact['emails'] ?? [] as $email) {
            $value = trim((string) $email);
            if ($value === '' || ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $institution->contacts()->create([
                'contact_type' => ContactType::Email,
                'contact_value' => $value,
                'is_primary' => false,
                'is_public' => true,
            ]);
        }
    }

    private function municipality(District $district, string $name): ?Municipality
    {
        $token = Str::of($name)->before(' ')->trim()->toString();
        if ($token !== '') {
            $match = Municipality::query()
                ->where('district_id', $district->id)
                ->where('name', 'like', '%'.$token.'%')
                ->first();
            if ($match !== null) {
                return $match;
            }
        }

        return Municipality::query()->where('district_id', $district->id)->orderBy('id')->first();
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function website(array $row): ?string
    {
        $contact = is_array($row['contact'] ?? null) ? $row['contact'] : [];
        $url = trim((string) ($contact['website'] ?? ''));
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return Str::limit($url, 255, '');
        }

        return null;
    }

    private function nullableString(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }
}
