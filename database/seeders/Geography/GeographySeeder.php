<?php

namespace Database\Seeders\Geography;

use App\Services\Location\GeographyImporter;
use Illuminate\Database\Seeder;

class GeographySeeder extends Seeder
{
    public function run(GeographyImporter $importer): void
    {
        $report = $importer->import(database_path('data/geography/nso-geographical-codes-2017'));

        $this->command?->info('Geography validation status: '.$report->validationStatus);
    }
}
