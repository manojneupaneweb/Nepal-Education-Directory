<?php

namespace App\Console\Commands;

use App\Services\Institution\DistrictCollegeResearchImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:import-district-college-research {directory=data/data-source/colleges}')]
#[Description('Import researched district college JSON files into the institution directory')]
class ImportDistrictCollegeResearch extends Command
{
    public function handle(DistrictCollegeResearchImporter $importer): int
    {
        $directory = base_path((string) $this->argument('directory'));
        $report = $importer->importDirectory($directory);

        $this->components->info("Created {$report['created']} colleges. Skipped {$report['skipped']} from {$report['files']} files.");

        return self::SUCCESS;
    }
}
