<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

#[Signature('seo:validate')]
#[Description('Validate the sitemap index and robots.txt')]
class SeoValidate extends Command
{
    public function handle(): int
    {
        $kernel = app(Kernel::class);
        $sitemap = $kernel->handle(Request::create('/sitemap.xml', 'GET'));
        $body = $sitemap->getContent() ?: '';
        $robots = $kernel->handle(Request::create('/robots.txt', 'GET'))->getContent() ?: '';
        $valid = $sitemap->getStatusCode() === 200
            && str_contains($body, '<sitemapindex')
            && str_contains($body, '/sitemaps/schools.xml')
            && ! str_contains($body, '/search')
            && str_contains($robots, 'Disallow: /admin')
            && str_contains($robots, 'Sitemap:');

        $this->components->info($valid ? 'Sitemap index and robots.txt passed validation.' : 'SEO validation failed.');

        return $valid ? self::SUCCESS : self::FAILURE;
    }
}
