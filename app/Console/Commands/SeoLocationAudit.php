<?php

namespace App\Console\Commands;

use App\Enums\InstitutionStatus;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Province;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('seo:location-audit')]
#[Description('Count published institutions on province, district, and municipality pages')]
class SeoLocationAudit extends Command
{
    public function handle(): int
    {
        $published = fn ($query) => $query->where('status', InstitutionStatus::Published);
        $summary = [
            'provinces' => Province::query()->withCount(['institutions as published_institutions_count' => $published])->orderBy('name')->get(['id', 'name'])->map(fn (Province $province) => [
                'name' => $province->name,
                'published_institutions' => $province->published_institutions_count,
            ])->all(),
            'districts' => District::query()->withCount(['institutions as published_institutions_count' => $published])->orderBy('name')->get(['id', 'name'])->map(fn (District $district) => [
                'name' => $district->name,
                'published_institutions' => $district->published_institutions_count,
            ])->all(),
            'empty_municipalities' => Municipality::query()->whereDoesntHave('institutions', $published)->count(),
            'municipalities' => Municipality::query()->count(),
        ];
        $path = storage_path('app/seo/location-audit.json');
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->components->info('Location audit written to storage/app/seo/location-audit.json.');
        $this->line($summary['empty_municipalities'].' of '.$summary['municipalities'].' local governments have no published institution.');

        return self::SUCCESS;
    }
}
