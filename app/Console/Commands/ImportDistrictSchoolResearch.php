<?php

namespace App\Console\Commands;

use App\Services\Institution\DistrictSchoolResearchImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:import-district-school-research {directory=data/data-source/schools}')]
#[Description('Import researched district school JSON files into the institution directory')]
class ImportDistrictSchoolResearch extends Command
{
    public function handle(DistrictSchoolResearchImporter $importer): int
    {
        $directory = base_path((string) $this->argument('directory'));
        $report = $importer->importDirectory($directory);

        $this->components->info("Created {$report['created']} schools. Skipped {$report['skipped']} from {$report['files']} files.");

        return self::SUCCESS;
    }
}
