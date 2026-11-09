<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('seo:content-report')]
#[Description('Print the latest content and location audit summaries')]
class SeoContentReport extends Command
{
    public function handle(): int
    {
        $content = storage_path('app/seo/content-audit.json');
        $locations = storage_path('app/seo/location-audit.json');
        if (! is_file($content) || ! is_file($locations)) {
            $this->components->error('Run seo:content-audit and seo:location-audit first.');

            return self::FAILURE;
        }

        $summary = json_decode((string) file_get_contents($content), true);
        $places = json_decode((string) file_get_contents($locations), true);
        $this->line('Published institutions: '.$summary['published']);
        $this->line('Indexable institutions: '.$summary['indexable']);
        foreach ($summary['levels'] as $level => $count) {
            $this->line('Level '.$level.': '.$count);
        }
        $this->line('Empty municipalities: '.$places['empty_municipalities'].' of '.$places['municipalities']);

        return self::SUCCESS;
    }
}
