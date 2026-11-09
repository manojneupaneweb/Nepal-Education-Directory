<?php

namespace App\Console\Commands;

use App\Models\District;
use App\Models\Institution;
use App\Models\Municipality;
use App\Models\Province;
use App\Services\SEO\DirectoryPerformance;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('performance:audit')]
#[Description('Measure local public-page query counts without changing records')]
class PerformanceAudit extends Command
{
    public function handle(): int
    {
        $school = Institution::query()->published()->whereHas('type', fn ($query) => $query->where('slug', 'school'))->value('slug');
        $college = Institution::query()->published()->whereHas('type', fn ($query) => $query->where('slug', 'college'))->value('slug');
        $province = Province::query()->where('is_active', true)->value('slug');
        $district = District::query()->where('is_active', true)->value('slug');
        $municipality = Municipality::query()->where('is_active', true)->whereHas('institutions', fn ($query) => $query->published())->with('district')->first();

        $paths = array_filter([
            '/',
            '/schools',
            '/colleges',
            '/search',
            $school ? '/institutions/'.$school : null,
            $college ? '/institutions/'.$college : null,
            $province ? '/provinces/'.$province : null,
            $district ? '/districts/'.$district : null,
            $municipality ? '/districts/'.$municipality->district->slug.'/local-governments/'.$municipality->slug : null,
        ]);

        $rows = [];
        foreach ($paths as $path) {
            $countsCached = is_array(Cache::get(DirectoryPerformance::PROVINCE_COUNTS));
            DB::flushQueryLog();
            DB::enableQueryLog();
            $start = microtime(true);
            $response = app()->handle(Request::create($path, 'GET'));
            $queries = DB::getQueryLog();
            DB::disableQueryLog();
            $row = [
                'path' => $path,
                'status' => $response->getStatusCode(),
                'queries' => count($queries),
                'milliseconds' => (int) round((microtime(true) - $start) * 1000),
                'bytes' => strlen($response->getContent()),
                'province_counts_cached_before' => $countsCached,
            ];
            $rows[] = $row;
            $this->line($path.' '.$row['status'].' queries='.$row['queries'].' ms='.$row['milliseconds'].' province_cache='.($countsCached ? 'hit' : 'miss'));
        }

        $path = storage_path('app/seo/performance.json');
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode([
            'measured_at' => now()->toIso8601String(),
            'environment' => 'local application handle, not a browser and not production. Later pages can reuse directory counts cached by earlier pages. Query totals include cache-table reads and writes.',
            'pages' => $rows,
        ], JSON_PRETTY_PRINT));
        $this->components->info('Performance audit written to storage/app/seo/performance.json.');

        return self::SUCCESS;
    }
}
