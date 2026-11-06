<?php

namespace App\Console\Commands;

use App\Jobs\ResearchUniversity;
use App\Models\University;
use App\Services\Universities\UniversityCatalogImporter;
use App\Services\Universities\UniversityResearcher;
use Illuminate\Console\Command;

class ResearchUniversities extends Command
{
    protected $signature = 'universities:research {slug? : Research one university} {--import : Import the UGC list first} {--apply-empty : Fill blank contact fields from the official homepage} {--queue : Dispatch queue jobs}';

    protected $description = 'Import and research Nepal universities one record at a time';

    public function handle(UniversityCatalogImporter $importer, UniversityResearcher $researcher): int
    {
        if ($this->option('import')) {
            $counts = $importer->import();
            $this->info("Imported universities. Created {$counts['created']}, updated {$counts['updated']}, skipped {$counts['skipped']}.");
        }

        $query = University::query()->where('is_aggregate', false)->orderBy('id');
        if ($slug = $this->argument('slug')) {
            $query->where('slug', $slug);
        }

        $universities = $query->get();
        if ($universities->isEmpty()) {
            $this->error('No matching universities. Run with --import first.');

            return self::FAILURE;
        }

        foreach ($universities as $university) {
            $this->line($university->slug);
            if ($this->option('queue')) {
                ResearchUniversity::dispatch($university->id, (bool) $this->option('apply-empty'));

                continue;
            }

            $history = $researcher->research($university, (bool) $this->option('apply-empty'));
            $this->line('  '.$history->status.($history->failure_reason ? ': '.$history->failure_reason : ''));
        }

        return self::SUCCESS;
    }
}
