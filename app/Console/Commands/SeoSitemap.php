<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

#[Signature('seo:sitemap')]
#[Description('Clear the cached XML sitemaps so the next request rebuilds them')]
class SeoSitemap extends Command
{
    public function handle(): int
    {
        foreach (['pages', 'schools', 'colleges', 'universities', 'programs', 'locations'] as $name) {
            Cache::forget('seo.sitemap.'.$name);
        }
        $this->components->info('Sitemap cache cleared. The next request rebuilds each section.');

        return self::SUCCESS;
    }
}
