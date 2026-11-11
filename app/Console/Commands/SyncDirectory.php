<?php

namespace App\Console\Commands;

use App\Services\Institution\SchoolDirectoryImporter;
use App\Services\Universities\UniversityCatalogImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:sync-directory')]
#[Description('Import universities, schools, and colleges from the committed directory files')]
class SyncDirectory extends Command
{
    public function handle(UniversityCatalogImporter $universities, SchoolDirectoryImporter $institutions): int
    {
        $universityReport = $universities->import();
        $this->components->info("Universities created {$universityReport['created']}, updated {$universityReport['updated']}, skipped {$universityReport['skipped']}.");

        $schools = $institutions->import(base_path('database/data/schools/directory.jsonl'));
        $this->components->info("Schools created {$schools['created']}, skipped {$schools['skipped']}.");

        $colleges = $institutions->import(base_path('database/data/colleges/directory.jsonl'), 'college');
        $this->components->info("Colleges created {$colleges['created']}, skipped {$colleges['skipped']}.");

        return self::SUCCESS;
    }
}
