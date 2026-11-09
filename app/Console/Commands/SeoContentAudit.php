<?php

namespace App\Console\Commands;

use App\Services\SEO\InstitutionContentQuality;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('seo:content-audit')]
#[Description('Count institution profile completeness without changing records')]
class SeoContentAudit extends Command
{
    public function handle(InstitutionContentQuality $quality): int
    {
        $summary = $quality->summarize();
        $path = storage_path('app/seo/content-audit.json');
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->components->info('Content audit written to storage/app/seo/content-audit.json.');
        $this->line('Published '.$summary['published'].'. Indexable '.$summary['indexable'].'.');

        return self::SUCCESS;
    }
}
