<?php

namespace App\Console\Commands;

use App\Enums\FieldReviewStatus;
use App\Models\Institution;
use App\Models\InstitutionFieldEvidence;
use App\Models\InstitutionSource;
use App\Models\InstitutionType;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('institutions:quality-gaps')]
#[Description('Count missing contact, website, description, and same-place duplicate names')]
class InstitutionQualityGaps extends Command
{
    public function handle(): int
    {
        $schoolId = InstitutionType::query()->where('slug', 'school')->value('id');
        $published = Institution::query()->published();
        $schools = (clone $published)->when($schoolId, fn ($query) => $query->where('institution_type_id', $schoolId));
        $minimum = (int) config('seo.quality.min_description_length', 80);

        $duplicates = Institution::query()
            ->published()
            ->select('municipality_id', DB::raw('lower(trim(name)) as normalized_name'), DB::raw('count(*) as total'))
            ->groupBy('municipality_id', DB::raw('lower(trim(name))'))
            ->having('total', '>', 1)
            ->get();

        $collegeId = InstitutionType::query()->where('slug', 'college')->value('id');
        $colleges = (clone $published)->when($collegeId, fn ($query) => $query->where('institution_type_id', $collegeId));
        $missingPhone = fn ($query) => $query->where('official_website', 'like', 'http%')->whereDoesntHave('contacts', fn ($contacts) => $contacts->where('contact_type', 'phone')->where('contact_value', '!=', ''));
        $missingEmail = fn ($query) => $query->where('official_website', 'like', 'http%')->whereDoesntHave('contacts', fn ($contacts) => $contacts->where('contact_type', 'email')->where('contact_value', '!=', ''));

        $summary = [
            'published' => (clone $published)->count(),
            'schools' => (clone $schools)->count(),
            'colleges' => (clone $colleges)->count(),
            'schools_with_website_missing_phone' => $missingPhone(clone $schools)->count(),
            'schools_with_website_missing_email' => $missingEmail(clone $schools)->count(),
            'colleges_with_website_missing_phone' => $missingPhone(clone $colleges)->count(),
            'colleges_with_website_missing_email' => $missingEmail(clone $colleges)->count(),
            'schools_missing_public_contact' => (clone $schools)->whereDoesntHave('contacts', fn ($query) => $query->whereIn('contact_type', ['phone', 'email'])->where('contact_value', '!=', ''))->count(),
            'schools_missing_website' => (clone $schools)->where(function ($query): void {
                $query->whereNull('official_website')->orWhere('official_website', 'not like', 'http%');
            })->count(),
            'schools_missing_description' => (clone $schools)->whereRaw('char_length(trim(coalesce(description, ?))) < ?', ['', $minimum])->count(),
            'same_municipality_name_groups' => $duplicates->count(),
        ];

        $path = storage_path('app/seo/quality-gaps.json');
        File::ensureDirectoryExists(dirname($path));
        $summary['evidence'] = [
            'proposals' => InstitutionFieldEvidence::query()->count(),
            'pending' => InstitutionFieldEvidence::query()->where('review_status', FieldReviewStatus::Pending)->count(),
            'accepted' => InstitutionFieldEvidence::query()->where('review_status', FieldReviewStatus::Accepted)->count(),
            'rejected' => InstitutionFieldEvidence::query()->where('review_status', FieldReviewStatus::Rejected)->count(),
            'source_pages' => InstitutionSource::query()->count(),
            'institutions_reviewed' => InstitutionFieldEvidence::query()->distinct('institution_id')->count('institution_id'),
            'accepted_by_field' => InstitutionFieldEvidence::query()->where('review_status', FieldReviewStatus::Accepted)->select('field_name', DB::raw('count(*) as total'))->groupBy('field_name')->pluck('total', 'field_name')->all(),
        ];
        File::put($path, json_encode($summary, JSON_PRETTY_PRINT));
        $this->writeReport($summary);
        foreach ($summary as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $this->line($key.': '.$value);
        }
        $this->components->info('Quality gaps written to storage/app/seo/quality-gaps.json.');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function writeReport(array $summary): void
    {
        $evidence = $summary['evidence'];
        $accepted = $evidence['accepted_by_field'] === [] ? 'None' : collect($evidence['accepted_by_field'])->map(fn ($total, $field) => $field.' '.$total)->implode(', ');
        $markdown = <<<MD
# Phase 5 source review

Date: {$this->date()}

These counts come from `php artisan institutions:quality-gaps`. No website was crawled and no institution field was filled by the command.

## Queue

Priority is the order a reviewer should open records that already have an official website. It is not a calculated score.

1. Published schools with a website and no public phone: {$summary['schools_with_website_missing_phone']}
2. Published schools with a website and no public email: {$summary['schools_with_website_missing_email']}
3. Published schools with a description under 80 characters: {$summary['schools_missing_description']}
4. Published colleges with a website and no public phone: {$summary['colleges_with_website_missing_phone']}
5. Published colleges with a website and no public email: {$summary['colleges_with_website_missing_email']}

Schools without a website: {$summary['schools_missing_website']}. Those stay out of this review queue.

## Evidence

| Count | Value |
| --- | --- |
| Institutions with at least one proposal | {$evidence['institutions_reviewed']} |
| Source pages stored | {$evidence['source_pages']} |
| Proposals | {$evidence['proposals']} |
| Pending | {$evidence['pending']} |
| Accepted | {$evidence['accepted']} |
| Rejected | {$evidence['rejected']} |
| Accepted by field | {$accepted} |

A proposal that finds the destination already filled stays pending. That outcome is not a separate counter; it remains in the pending total.

## Directory

Published institutions: {$summary['published']}. Schools: {$summary['schools']}. Colleges: {$summary['colleges']}. Same municipality and normalized name groups: {$summary['same_municipality_name_groups']}.

MD;
        File::ensureDirectoryExists(base_path('reports'));
        File::put(base_path('reports/phase-5-source-review.md'), $markdown);
        $this->components->info('Source review report written to reports/phase-5-source-review.md.');
    }

    private function date(): string
    {
        return now()->toDateString();
    }
}
