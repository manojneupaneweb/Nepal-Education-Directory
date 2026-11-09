<?php

namespace App\Services\Seo;

class SeoMeta
{
    /**
     * @param  list<array{name: string, url: string}>  $breadcrumbs
     * @param  list<array<string, mixed>>  $jsonLd
     */
    public function __construct(
        public string $title,
        public string $description,
        public string $canonical,
        public string $robots = 'index, follow',
        public ?string $image = null,
        public array $breadcrumbs = [],
        public array $jsonLd = [],
    ) {}
}
