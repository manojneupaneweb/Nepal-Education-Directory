<?php

namespace App\Console\Commands;

use App\Models\Institution;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

#[Signature('seo:audit')]
#[Description('Check public SEO metadata, sitemap coverage, and duplicate slugs')]
class SeoAudit extends Command
{
    public function handle(): int
    {
        $kernel = app(Kernel::class);
        $checks = [];
        foreach (['/', '/schools', '/colleges', '/search', '/robots.txt', '/sitemap.xml'] as $path) {
            $response = $kernel->handle(Request::create($path, 'GET'));
            $body = $response->getContent() ?: '';
            $checks[] = [
                'path' => $path,
                'status' => $response->getStatusCode(),
                'has_title' => str_contains($body, '<title>') || $path === '/robots.txt' || $path === '/sitemap.xml',
                'robots' => preg_match('/name="robots" content="([^"]+)"/', $body, $match) ? $match[1] : null,
                'canonical' => preg_match('/rel="canonical" href="([^"]+)"/', $body, $canonical) ? $canonical[1] : null,
            ];
        }

        $report = [
            'checked_at' => now()->toIso8601String(),
            'duplicate_slugs' => Institution::query()->select('slug')->groupBy('slug')->havingRaw('count(*) > 1')->count(),
            'checks' => $checks,
        ];
        $directory = storage_path('app/seo');
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        file_put_contents($directory.'/audit.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->components->info('SEO audit written to storage/app/seo/audit.json');
        foreach ($checks as $check) {
            $this->line($check['status'].' '.$check['path'].' '.($check['robots'] ?? ''));
        }

        return self::SUCCESS;
    }
}
