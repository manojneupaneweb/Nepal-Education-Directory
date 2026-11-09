<?php

namespace App\Services\Institution;

use App\Enums\AffiliationStatus;
use App\Enums\AffiliationType;
use App\Enums\ContactType;
use App\Enums\InstitutionStatus;
use App\Enums\OwnershipType;
use App\Enums\VerificationStatus;
use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\University;
use Illuminate\Support\Str;

class DistrictCollegeResearchImporter
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
        $collegeTypeId = InstitutionType::query()->where('slug', 'college')->value('id');

        foreach (glob(rtrim($directory, '/').'/*.json') ?: [] as $path) {
            $files++;
            $payload = json_decode((string) file_get_contents($path), true);
            if (! is_array($payload)) {
                continue;
            }

            $districtName = (string) ($payload['research_metadata']['district'] ?? '');
            $district = District::query()->where('name', $districtName)->first();
            if ($district === null || $collegeTypeId === null) {
                $skipped += count($payload['institutions'] ?? []);

                continue;
            }

            foreach ($payload['institutions'] ?? [] as $row) {
                if (! is_array($row)) {
                    $skipped++;

                    continue;
                }

                $result = $this->importRow($row, $district, (int) $collegeTypeId);
                $result ? $created++ : $skipped++;
            }
        }

        return ['created' => $created, 'skipped' => $skipped, 'files' => $files];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function importRow(array $row, District $district, int $collegeTypeId): bool
    {
        $overview = is_array($row['overview'] ?? null) ? $row['overview'] : [];
        $name = trim((string) ($overview['name'] ?? ''));
        if ($name === '' || preg_match('/secondary school|basic school/i', $name) === 1) {
            return false;
        }

        $exists = Institution::query()
            ->where('district_id', $district->id)
            ->whereRaw('lower(name) = ?', [Str::lower($name)])
            ->exists();
        if ($exists) {
            return false;
        }

        $municipality = $this->municipality($district, (string) ($overview['municipality'] ?? ''));
        if ($municipality === null) {
            return false;
        }

        $ownership = OwnershipType::tryFrom(Str::lower((string) ($overview['ownership'] ?? ''))) ?? OwnershipType::Unknown;
        $website = $this->website($overview, $row);

        $institution = Institution::query()->create([
            'institution_type_id' => $collegeTypeId,
            'name' => $name,
            'name_ne' => $this->nullableString($overview['name_ne'] ?? null),
            'slug' => $this->slugger->generate($name),
            'description' => $this->nullableString($overview['description'] ?? null),
            'website_sections' => $this->sections($overview, $row),
            'established_year' => is_numeric($overview['established_year'] ?? null) ? (int) $overview['established_year'] : null,
            'ownership_type' => $ownership,
            'province_id' => $district->province_id,
            'district_id' => $district->id,
            'municipality_id' => $municipality?->id,
            'address' => $this->nullableString($overview['address'] ?? null),
            'official_website' => $website,
            'status' => InstitutionStatus::Published,
            'verification_status' => VerificationStatus::Unverified,
            'published_at' => now(),
        ]);

        $this->storeContacts($institution, $row);
        $this->storeAffiliation($institution, (string) ($overview['university'] ?? ''));

        return true;
    }

    /**
     * @param  array<string, mixed>  $overview
     * @param  array<string, mixed>  $row
     * @return array<string, list<string>>
     */
    private function sections(array $overview, array $row): array
    {
        $about = array_values(array_filter([
            $this->nullableString($overview['description'] ?? null),
            $this->labeled('Mission', $overview['mission'] ?? null),
            $this->labeled('Vision', $overview['vision'] ?? null),
        ]));

        $programs = [];
        foreach ($row['courses'] ?? [] as $course) {
            if (is_array($course) && trim((string) ($course['name'] ?? '')) !== '') {
                $programs[] = trim((string) $course['name']);
            }
        }

        $facilities = [];
        foreach ($row['facilities'] ?? [] as $facility) {
            if (is_string($facility) && trim($facility) !== '') {
                $facilities[] = trim($facility);
            }
        }

        $gallery = [];
        foreach ($row['gallery'] ?? [] as $image) {
            $url = is_array($image) ? (string) ($image['url'] ?? '') : '';
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $gallery[] = $url;
            }
        }

        $videos = [];
        foreach ($row['videos'] ?? [] as $video) {
            $url = is_array($video) ? (string) ($video['url'] ?? '') : '';
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $videos[] = $url;
            }
        }

        return array_filter([
            'about' => $about,
            'programs' => $programs,
            'facilities' => $facilities,
            'gallery' => $gallery,
            'videos' => $videos,
        ]);
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
            if ($value === '') {
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

    private function storeAffiliation(Institution $institution, string $universityName): void
    {
        $universityName = trim($universityName);
        if ($universityName === '') {
            return;
        }

        $university = University::query()->where('name', $universityName)->first();
        if ($university === null) {
            return;
        }

        $institution->affiliations()->create([
            'university_id' => $university->id,
            'affiliation_type' => AffiliationType::Affiliated,
            'status' => AffiliationStatus::Pending,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overview
     * @param  array<string, mixed>  $row
     */
    private function website(array $overview, array $row): ?string
    {
        $contact = is_array($row['contact'] ?? null) ? $row['contact'] : [];
        $candidates = [
            $contact['website'] ?? null,
            ...((array) ($overview['source_urls'] ?? [])),
        ];

        foreach ($candidates as $candidate) {
            $url = trim((string) $candidate);
            if (filter_var($url, FILTER_VALIDATE_URL) && ! str_contains($url, 'edusanjal.com') && ! str_contains($url, 'collegenp.com')) {
                return Str::limit($url, 255, '');
            }
        }

        return null;
    }

    private function nullableString(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }

    private function labeled(string $label, mixed $value): ?string
    {
        $text = $this->nullableString($value);

        return $text === null ? null : $label.': '.$text;
    }
}
