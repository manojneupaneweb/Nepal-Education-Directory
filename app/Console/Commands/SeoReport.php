<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('seo:report')]
#[Description('Print the latest SEO audit summary')]
class SeoReport extends Command
{
    public function handle(): int
    {
        $path = storage_path('app/seo/audit.json');
        if (! is_file($path)) {
            $this->components->error('No audit report yet. Run php artisan seo:audit first.');

            return self::FAILURE;
        }

        $this->line((string) file_get_contents($path));

        return self::SUCCESS;
    }
}
