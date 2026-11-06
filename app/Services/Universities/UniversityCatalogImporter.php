<?php

namespace App\Services\Universities;

use App\Enums\ResearchVerificationStatus;
use App\Enums\SourceCategory;
use App\Models\District;
use App\Models\University;
use App\Models\UniversitySource;
use Illuminate\Support\Carbon;

class UniversityCatalogImporter
{
    /**
     * @return array{created: int, updated: int, skipped: int}
     */
    public function import(): array
    {
        $rows = json_decode((string) file_get_contents(base_path('data/ugc_universities.json')), true);
        $websites = require base_path('data/university_verified_websites.php');
        $counts = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($rows as $row) {
            $slug = (string) ($row['slug'] ?? '');
            if ($slug === '' || $slug === 'foreign-university-affiliated-higher-education-institutions') {
                $counts['skipped']++;

                continue;
            }

            $existing = University::query()->where('slug', $slug)->first();
            $payload = $this->payload($row, $websites[$slug] ?? null);

            if ($existing) {
                $existing->fill($this->withoutOverwritingVerified($existing, $payload));
                $existing->save();
                $university = $existing;
                $counts['updated']++;
            } else {
                $university = University::query()->create($payload);
                $counts['created']++;
            }

            $this->ensureSource(
                $university,
                SourceCategory::UgcOfficial,
                'https://ugcnepal.edu.np/pages/university-7/',
                'UGC Nepal university list',
            );

            if ($university->official_website) {
                $this->ensureSource(
                    $university,
                    SourceCategory::OfficialWebsite,
                    $university->official_website,
                    $university->name.' official website',
                );
            }
        }

        return $counts;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function payload(array $row, ?string $website): array
    {
        $location = trim((string) ($row['location'] ?? ''));
        $district = $location !== ''
            ? District::query()->whereRaw('lower(name) = ?', [mb_strtolower($location)])->first()
            : null;
        $category = (string) ($row['category'] ?? '');
        $ownership = str_contains(mb_strtolower($category), 'provincial') ? 'provincial' : 'federal';
        $name = (string) $row['name'];

        return [
            'name' => $name,
            'slug' => $row['slug'],
            'university_type' => $category,
            'ugc_category' => $category,
            'ownership' => $ownership,
            'established_year_bs' => $row['established_bs'] !== '' ? (string) $row['established_bs'] : null,
            'location_label' => $location !== '' ? $location : null,
            'district_id' => $district?->id,
            'province_id' => $district?->province_id,
            'official_website' => $website,
            'logo_path' => $row['logo'] ?? null,
            'ugc_total_colleges' => $this->number($row['total_colleges'] ?? null),
            'ugc_constituent_colleges' => $this->number($row['constituent_colleges'] ?? null),
            'ugc_affiliated_colleges' => $this->number($row['affiliated_colleges'] ?? null),
            'ugc_community_colleges' => $this->number($row['community_colleges'] ?? null),
            'description' => $name.' is listed by the University Grants Commission Nepal as a '.$category
                .($location !== '' ? ' associated with '.$location : '').'.',
            'recognition_status' => 'Listed by the University Grants Commission Nepal.',
            'seo_title' => $name.' — Nepal university profile',
            'meta_description' => 'Profile of '.$name.', a '.$category.' listed by UGC Nepal'
                .($location !== '' ? ' in '.$location : '').'.',
            'verification_status' => $website
                ? ResearchVerificationStatus::PartiallyVerified
                : ResearchVerificationStatus::Unverified,
            'is_aggregate' => false,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function withoutOverwritingVerified(University $university, array $payload): array
    {
        if ($university->verification_status === ResearchVerificationStatus::Verified) {
            return [];
        }

        foreach ($payload as $field => $value) {
            if ($university->{$field} !== null && $university->{$field} !== '' && $field !== 'verification_status') {
                unset($payload[$field]);
            }
        }

        return $payload;
    }

    private function ensureSource(University $university, SourceCategory $category, string $url, string $title): void
    {
        UniversitySource::query()->firstOrCreate(
            [
                'university_id' => $university->id,
                'url' => $url,
            ],
            [
                'category' => $category,
                'title' => $title,
                'retrieved_at' => Carbon::now(),
            ],
        );
    }

    private function number(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === 'N/A') {
            return null;
        }

        $normalized = str_replace(',', '', (string) $value);

        return is_numeric($normalized) ? (int) $normalized : null;
    }
}
