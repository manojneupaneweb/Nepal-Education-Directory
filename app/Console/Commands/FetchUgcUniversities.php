<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchUgcUniversities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ugc:fetch-universities {--force : Force refetch even if file exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all universities and their college stats from UGC Nepal (https://ugcnepal.edu.np/pages/university-7/)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $url = 'https://ugcnepal.edu.np/pages/university-7/';
        $this->info("Fetching universities from {$url}...");

        try {
            $response = Http::withoutVerifying()
                ->timeout(20)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->get($url);

            if (! $response->successful()) {
                $this->error("Failed to fetch page. HTTP status: {$response->status()}");

                return self::FAILURE;
            }

            $html = $response->body();
        } catch (\Throwable $e) {
            $this->error('Error fetching page: '.$e->getMessage());

            return self::FAILURE;
        }

        preg_match_all('/<table[^>]*>(.*?)<\/table>/is', $html, $matches);
        $tables = $matches[1] ?? [];

        if (empty($tables)) {
            $this->error('No tables found in page content.');

            return self::FAILURE;
        }

        $parseTable = function (string $tableHtml): array {
            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $tableHtml, $rowMatches);
            $result = [];
            foreach ($rowMatches[1] ?? [] as $tr) {
                preg_match_all('/<t[dh][^>]*>(.*?)<\/t[dh]>/is', $tr, $cellMatches);
                $cols = array_map(function ($c) {
                    $c = strip_tags($c);
                    $c = str_replace(["\r", "\n", "\t"], ' ', $c);

                    return trim(preg_replace('/\s+/', ' ', $c));
                }, $cellMatches[1] ?? []);

                if (count($cols) >= 2) {
                    $result[$cols[0]] = $cols[1];
                }
            }

            return $result;
        };

        $tableData = array_map($parseTable, $tables);

        // Table 0 is summary list of universities
        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $tables[0], $t0Rows);
        $currentCat = 'Federal University';
        $universities = [];

        $mapping = [
            '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10,
            '11' => 11, '12' => 12, '16' => 13, '17' => 14, '18' => 15, '19' => 16, '20' => 17, '22' => 18,
            '23' => 19, '24' => 20, '25' => 21, '26' => 22, '27' => 23, '28' => 24, '29' => 25, '30' => 26,
        ];

        foreach ($t0Rows[1] ?? [] as $tr) {
            preg_match_all('/<t[dh][^>]*>(.*?)<\/t[dh]>/is', $tr, $cellMatches);
            $cols = array_map(function ($c) {
                $c = strip_tags($c);
                $c = str_replace(["\r", "\n", "\t"], ' ', $c);

                return trim(preg_replace('/\s+/', ' ', $c));
            }, $cellMatches[1] ?? []);

            if (empty($cols)) {
                continue;
            }

            if (count($cols) === 1) {
                if (! empty($cols[0])) {
                    $currentCat = $cols[0];
                }

                continue;
            }

            if ($cols[0] === 'S.N' || empty($cols[0])) {
                continue;
            }

            $sn = $cols[0];
            $name = $cols[1];
            $establishedBs = $cols[2] ?? '';
            $location = $cols[3] ?? '';

            $stats = isset($mapping[$sn], $tableData[$mapping[$sn]]) ? $tableData[$mapping[$sn]] : [];

            $totalCampuses = $stats['Total Campuses'] ?? $stats['No. of Constituent Campuses'] ?? null;
            $constituent = $stats['No. of Constituent Campuses'] ?? $stats['No. of Constituent and Joint Campuses'] ?? $stats['Constituent Campus'] ?? null;
            $affiliated = $stats['No. of Affiliated Campuses'] ?? $stats['No. of Private Campuses'] ?? $stats['Affiliate Campus'] ?? null;
            $community = $stats['No. of Community Campuses'] ?? null;

            $universities[] = [
                'sn' => is_numeric($sn) ? (int) $sn : $sn,
                'name' => $name,
                'slug' => Str::slug($name),
                'established_bs' => $establishedBs,
                'location' => $location,
                'category' => $currentCat,
                'total_colleges' => $totalCampuses ?: 'N/A',
                'constituent_colleges' => $constituent,
                'affiliated_colleges' => $affiliated,
                'community_colleges' => $community,
                'enrollment' => $stats['Enrollment'] ?? null,
                'teachers' => $stats['Total No. of Teachers'] ?? $stats['Teachers'] ?? null,
                'student_teacher_ratio' => $stats['Student /Teacher Ratio'] ?? null,
            ];
        }

        $dir = base_path('data');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filePath = base_path('data/ugc_universities.json');
        File::put($filePath, json_encode($universities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info('Successfully parsed and saved '.count($universities)." universities to {$filePath}");

        return self::SUCCESS;
    }
}
