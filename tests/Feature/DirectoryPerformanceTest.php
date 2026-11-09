<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Services\SEO\DirectoryPerformance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DirectoryPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_and_school_totals_stay_available_and_institution_saves_clear_the_count_cache(): void
    {
        $institution = Institution::factory()->create();

        $this->seedDirectoryCache();
        $institution->update(['name' => $institution->name.' updated']);
        $this->assertDirectoryCacheCleared();

        $this->seedDirectoryCache();
        $institution->delete();
        $this->assertDirectoryCacheCleared();

        $this->seedDirectoryCache();
        $institution->restore();
        $this->assertDirectoryCacheCleared();

        $this->get(route('home'))->assertOk();
        $this->get(route('schools.index'))->assertOk();
        $this->get(route('colleges.index'))->assertOk();
    }

    private function seedDirectoryCache(): void
    {
        Cache::put(DirectoryPerformance::STREAM_COUNTS, [1 => 4], 3600);
        Cache::put(DirectoryPerformance::PROVINCE_COUNTS, [], 3600);
        Cache::put('seo.sitemap.schools', '<xml>', 3600);
        Cache::put('seo.sitemap.colleges', '<xml>', 3600);
        Cache::put('seo.sitemap.locations', '<xml>', 3600);
    }

    private function assertDirectoryCacheCleared(): void
    {
        $this->assertFalse(Cache::has(DirectoryPerformance::STREAM_COUNTS));
        $this->assertFalse(Cache::has(DirectoryPerformance::PROVINCE_COUNTS));
        $this->assertFalse(Cache::has('seo.sitemap.schools'));
        $this->assertFalse(Cache::has('seo.sitemap.colleges'));
        $this->assertFalse(Cache::has('seo.sitemap.locations'));
    }
}
