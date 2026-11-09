<?php

namespace App\Services\SEO;

use App\Models\CollegeStream;
use App\Models\Province;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DirectoryPerformance
{
    public const PROVINCE_COUNTS = 'directory.province-counts';

    public const STREAM_COUNTS = 'directory.stream-counts';

    public static function forgetPublicCounts(): void
    {
        Cache::forget(self::PROVINCE_COUNTS);
        Cache::forget(self::STREAM_COUNTS);
    }

    /**
     * @return Collection<int, Province>
     */
    public static function provinces(): Collection
    {
        /** @var list<array<string, int|string|null>> $rows */
        $rows = Cache::get(self::PROVINCE_COUNTS);
        if (! is_array($rows)) {
            Cache::forget(self::PROVINCE_COUNTS);
            $rows = Cache::remember(self::PROVINCE_COUNTS, 3600, function (): array {
                return Province::query()->active()->withDirectoryCounts()->orderBy('code')->get()
                    ->map(fn (Province $province): array => [
                        'id' => $province->id,
                        'name' => $province->name,
                        'name_ne' => $province->name_ne,
                        'slug' => $province->slug,
                        'code' => $province->code,
                        'schools_count' => (int) $province->schools_count,
                        'colleges_count' => (int) $province->colleges_count,
                        'districts_count' => (int) $province->districts_count,
                        'universities_count' => (int) $province->universities_count,
                    ])->all();
            });
        }

        return collect($rows)->map(function (array $row): Province {
            $province = (new Province)->newFromBuilder([
                'id' => $row['id'],
                'name' => $row['name'],
                'name_ne' => $row['name_ne'],
                'slug' => $row['slug'],
                'code' => $row['code'],
            ]);
            $province->setAttribute('schools_count', $row['schools_count']);
            $province->setAttribute('colleges_count', $row['colleges_count']);
            $province->setAttribute('districts_count', $row['districts_count']);
            $province->setAttribute('universities_count', $row['universities_count']);

            return $province;
        })->values();
    }

    /**
     * @return Collection<int, CollegeStream>
     */
    public static function countedStreams(): Collection
    {
        $streams = CollegeStream::query()->active()->orderBy('sort_order')->get();
        $cached = Cache::get(self::STREAM_COUNTS);
        $counts = is_array($cached) ? $cached : Cache::remember(self::STREAM_COUNTS, 3600, function () use ($streams): array {
            return $streams->mapWithKeys(fn (CollegeStream $stream): array => [$stream->id => $stream->matchingInstitutions()->count()])->all();
        });

        return $streams->each(function (CollegeStream $stream) use ($counts): void {
            $stream->setAttribute('matched_colleges_count', $counts[$stream->id] ?? 0);
        });
    }
}
