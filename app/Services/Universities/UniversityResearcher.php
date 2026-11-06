<?php

namespace App\Services\Universities;

use App\Enums\ResearchVerificationStatus;
use App\Enums\SourceCategory;
use App\Models\Program;
use App\Models\University;
use App\Models\UniversityResearchHistory;
use App\Models\UniversitySource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UniversityResearcher
{
    public function research(University $university, bool $applyEmpty = false): UniversityResearchHistory
    {
        $history = UniversityResearchHistory::query()->create([
            'university_id' => $university->id,
            'status' => 'running',
            'approval_status' => 'pending',
            'started_at' => now(),
        ]);

        if (! $university->official_website) {
            return $this->finish($history, 'failed', 'No verified official website is stored for this university.');
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'ShikshaLinkResearch/1.0'])
                ->get($university->official_website);
        } catch (\Throwable $exception) {
            return $this->finish($history, 'failed', $exception->getMessage());
        }

        if (! $response->successful()) {
            return $this->finish($history, 'failed', 'Official website returned HTTP '.$response->status().'.');
        }

        $html = $response->body();
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? '');
        $proposal = $this->extract($university, $text, $response->effectiveUri()?->__toString() ?? $university->official_website);

        UniversitySource::query()->updateOrCreate(
            ['university_id' => $university->id, 'url' => $university->official_website],
            [
                'category' => SourceCategory::OfficialWebsite,
                'title' => $proposal['page_title'] ?? $university->name,
                'retrieved_at' => now(),
                'notes' => 'Homepage fetched during research.',
            ],
        );

        $history->update([
            'status' => 'completed',
            'proposed_changes' => $proposal,
            'summary' => 'Homepage fetched. Proposed fields are limited to text found on the official website.',
            'finished_at' => now(),
        ]);

        $university->forceFill(['researched_at' => now()])->save();

        if ($applyEmpty) {
            $this->apply($history, onlyEmpty: true);
        }

        return $history->refresh();
    }

    public function apply(UniversityResearchHistory $history, bool $onlyEmpty = true): void
    {
        $university = $history->university;
        $changes = $history->proposed_changes ?? [];
        $fields = $changes['fields'] ?? [];

        foreach ($fields as $field => $value) {
            if (! is_string($value) || $value === '') {
                continue;
            }

            if (! in_array($field, ['email', 'phone', 'description'], true)) {
                continue;
            }

            if ($onlyEmpty && filled($university->{$field})) {
                continue;
            }

            $university->{$field} = $value;
        }

        if ($university->isDirty()) {
            $university->verification_status = ResearchVerificationStatus::PartiallyVerified;
            $university->save();
        }

        foreach ($changes['programs'] ?? [] as $program) {
            if (! is_array($program) || empty($program['name']) || empty($program['source_url'])) {
                continue;
            }

            $slug = Str::slug($university->slug.'-'.$program['name']);
            Program::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'university_id' => $university->id,
                    'name' => $program['name'],
                    'description' => $program['description'] ?? null,
                    'source_url' => $program['source_url'],
                    'verification_status' => ResearchVerificationStatus::PartiallyVerified,
                    'program_status' => 'listed',
                    'seo_title' => $program['name'].' at '.$university->name,
                    'meta_description' => $program['name'].' listed on the official website of '.$university->name.'.',
                ],
            );
        }

        $history->update([
            'approval_status' => 'approved',
            'reviewed_at' => now(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function extract(University $university, string $text, string $finalUrl): array
    {
        $email = null;
        $phone = null;

        if (preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text, $emailMatch)) {
            $email = $emailMatch[0];
        }

        if (preg_match('/(?:\+977[\s\-]?)?(?:0\d{1,2}[\s\-]?)?\d{7,8}/', $text, $phoneMatch)) {
            $phone = trim($phoneMatch[0]);
        }

        $excerpt = Str::limit($text, 420, '');

        return [
            'page_title' => $university->name,
            'final_url' => $finalUrl,
            'fields' => array_filter([
                'email' => $email,
                'phone' => $phone,
                'description' => $excerpt !== '' ? $excerpt : null,
            ]),
            'programs' => [],
            'conflicts' => [],
        ];
    }

    private function finish(UniversityResearchHistory $history, string $status, string $reason): UniversityResearchHistory
    {
        $history->update([
            'status' => $status,
            'approval_status' => 'pending',
            'failure_reason' => $reason,
            'finished_at' => now(),
        ]);

        return $history;
    }
}
