<?php

namespace App\Observers;

use App\Models\Institution;
use App\Services\SEO\DirectoryPerformance;
use Illuminate\Support\Facades\Cache;

class InstitutionSitemapObserver
{
    public function saved(Institution $institution): void
    {
        $this->forget();
    }

    public function deleted(Institution $institution): void
    {
        $this->forget();
    }

    public function restored(Institution $institution): void
    {
        $this->forget();
    }

    private function forget(): void
    {
        foreach (['schools', 'colleges', 'locations'] as $section) {
            Cache::forget('seo.sitemap.'.$section);
        }

        DirectoryPerformance::forgetPublicCounts();
    }
}
