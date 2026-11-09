<?php

namespace App\Console\Commands;

use App\Services\Institution\SchoolDirectoryImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:import-school-directory {file=database/data/schools/directory.jsonl}')]
#[Description('Import the exported school directory')]
class ImportSchoolDirectory extends Command
{
    public function handle(SchoolDirectoryImporter $importer): int
    {
        $path = base_path((string) $this->argument('file'));
        if (! is_file($path)) {
            $this->components->error('School directory file was not found.');

            return self::FAILURE;
        }

        $report = $importer->import($path);
        $this->components->info("Created {$report['created']} schools. Skipped {$report['skipped']}.");

        return self::SUCCESS;
    }
}
