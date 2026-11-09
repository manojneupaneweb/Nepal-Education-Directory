<?php

namespace App\Console\Commands;

use App\Services\Institution\SchoolDirectoryImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:import-college-directory {file=database/data/colleges/directory.jsonl}')]
#[Description('Import the exported college directory')]
class ImportCollegeDirectory extends Command
{
    public function handle(SchoolDirectoryImporter $importer): int
    {
        $path = base_path((string) $this->argument('file'));
        if (! is_file($path)) {
            $this->components->error('College directory file was not found.');

            return self::FAILURE;
        }

        $report = $importer->import($path, 'college');
        $this->components->info("Created {$report['created']} colleges. Skipped {$report['skipped']}.");

        return self::SUCCESS;
    }
}
