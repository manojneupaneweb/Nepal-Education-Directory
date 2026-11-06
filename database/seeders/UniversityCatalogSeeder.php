<?php

namespace Database\Seeders;

use App\Services\Universities\UniversityCatalogImporter;
use Illuminate\Database\Seeder;

class UniversityCatalogSeeder extends Seeder
{
    public function run(): void
    {
        app(UniversityCatalogImporter::class)->import();
    }
}
