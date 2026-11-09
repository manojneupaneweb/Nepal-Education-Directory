<?php

namespace App\Services\SEO;

use App\Enums\ContactType;
use App\Models\Institution;
use Illuminate\Support\Facades\DB;

class InstitutionContentQuality
{
    /**
     * Highest completeness level for one published institution.
     * Levels are exclusive. A record is not raised by invented text.
     */
    public function level(Institution $institution): int
    {
        $description = mb_strlen(trim((string) $institution->description)) >= (int) config('seo.quality.min_description_length', 80);
        $contact = (bool) ($institution->has_public_contact ?? false);
        $offerings = (bool) ($institution->has_programs ?? false)
            || (bool) ($institution->has_facilities ?? false)
            || $this->hasStoredPrograms($institution);
        $website = str_starts_with(strtolower((string) $institution->official_website), 'http');
        $media = filled($institution->logo_path) || (bool) ($institution->has_images ?? false);

        if ($description && $contact && $offerings && ($website || $media)) {
            return 5;
        }

        if ($contact && ($description || $offerings) && ($website || $media || (bool) ($institution->has_facilities ?? false))) {
            return 4;
        }

        if ($description && $offerings) {
            return 3;
        }

        if ($contact) {
            return 2;
        }

        return 1;
    }

    /**
     * @return array<string, mixed>
     */
    public function summarize(): array
    {
        $levels = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $byType = [];
        $byProvince = [];
        $byDistrict = [];
        $missing = [
            'description' => 0,
            'public_contact' => 0,
            'official_website' => 0,
            'programs_or_facilities' => 0,
            'logo_or_image' => 0,
        ];

        Institution::query()
            ->published()
            ->select(['id', 'institution_type_id', 'province_id', 'district_id', 'description', 'official_website', 'logo_path', 'website_sections'])
            ->with(['type:id,name', 'province:id,name', 'district:id,name'])
            ->withExists(['contacts as has_public_contact' => fn ($query) => $query->whereIn('contact_type', [ContactType::Phone, ContactType::Email])])
            ->withExists(['programs as has_programs'])
            ->withExists(['facilities as has_facilities'])
            ->withExists(['images as has_images'])
            ->lazyById(400)
            ->each(function (Institution $institution) use (&$levels, &$byType, &$byProvince, &$byDistrict, &$missing): void {
                $level = $this->level($institution);
                $levels[$level]++;
                $type = $institution->type?->name ?? 'Unknown';
                $province = $institution->province?->name ?? 'Unknown';
                $district = $institution->district?->name ?? 'Unknown';
                $byType[$type][$level] = ($byType[$type][$level] ?? 0) + 1;
                $byProvince[$province][$level] = ($byProvince[$province][$level] ?? 0) + 1;
                $byDistrict[$district][$level] = ($byDistrict[$district][$level] ?? 0) + 1;

                if (mb_strlen(trim((string) $institution->description)) < (int) config('seo.quality.min_description_length', 80)) {
                    $missing['description']++;
                }
                if (! $institution->has_public_contact) {
                    $missing['public_contact']++;
                }
                if (! str_starts_with(strtolower((string) $institution->official_website), 'http')) {
                    $missing['official_website']++;
                }
                if (! $institution->has_programs && ! $institution->has_facilities && ! $this->hasStoredPrograms($institution)) {
                    $missing['programs_or_facilities']++;
                }
                if (! filled($institution->logo_path) && ! $institution->has_images) {
                    $missing['logo_or_image']++;
                }
            });

        $published = array_sum($levels);

        return [
            'published' => $published,
            'indexable' => Institution::query()->published()->indexable()->count(),
            'levels' => $levels,
            'by_type' => $byType,
            'by_province' => $byProvince,
            'by_district' => $byDistrict,
            'missing' => $missing,
            'verification' => Institution::query()->published()->select('verification_status', DB::raw('count(*) as total'))->groupBy('verification_status')->pluck('total', 'verification_status')->all(),
        ];
    }

    private function hasStoredPrograms(Institution $institution): bool
    {
        $programs = $institution->website_sections['programs'] ?? [];

        return is_array($programs) && collect($programs)->filter(fn ($name) => filled($name))->isNotEmpty();
    }
}
