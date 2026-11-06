<?php

namespace App\Console\Commands;

use App\Services\Location\GeographyImporter;
use Illuminate\Console\Command;

class ImportGeographyCommand extends Command
{
    protected $signature = 'geography:import
        {--path= : Directory containing manifest.json and the source CSV files}
        {--dry-run : Parse and roll back without keeping database changes}
        {--validate-only : Check the source files without writing}
        {--report : Write a JSON report under storage/app/geography}';

    protected $description = 'Import Nepal administrative geography from a source-backed dataset directory';

    public function handle(GeographyImporter $importer): int
    {
        $path = $this->option('path') ?: database_path('data/geography/nso-geographical-codes-2017');
        $dryRun = (bool) $this->option('dry-run');
        $validateOnly = (bool) $this->option('validate-only');

        $report = $validateOnly
            ? $importer->validate($path)
            : $importer->import($path, $dryRun);

        $payload = $report->toArray();

        foreach ($payload['levels'] as $level => $counts) {
            $this->line(sprintf(
                '%s: discovered %d, new %d, updated %d, unchanged %d, skipped %d',
                $level,
                $counts['discovered'],
                $counts['created'],
                $counts['updated'],
                $counts['unchanged'],
                $counts['skipped'],
            ));
        }

        $this->line('Validation status: '.$payload['validation_status']);

        foreach ($payload['missing_relationships'] as $message) {
            $this->warn($message);
        }

        foreach ($payload['invalid'] as $message) {
            $this->error($message);
        }

        if ($this->option('report')) {
            $directory = storage_path('app/geography');
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $file = $directory.'/import-'.now()->format('Ymd-His').'.json';
            file_put_contents($file, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info('Report written to '.$file);
        }

        return $payload['invalid'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
