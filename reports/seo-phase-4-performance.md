# Phase 4 performance

Date: 2026-11-09  
Environment: local Laravel 13.40.0, MySQL, production Vite build already in `public/build`. These are not production measurements and they are not Lighthouse scores.

## Baseline, before this change

Measured by handling a local HTTP request inside the application. The same request also counted SQL queries.

| Page | Status | Queries | Time |
| --- | --- | --- | --- |
| `/` | 200 | 66 | 514 ms |
| `/schools` | 200 | 18 | 272 ms |
| `/colleges` | 200 | 18 | 99 ms |
| `/search` | 200 | 7 | 78 ms |
| One school profile | 200 | 13 | 20 ms |
| One college profile | 200 | 19 | 22 ms |
| One province | 200 | 19 | 56 ms |
| One district | 200 | 19 | 22 ms |
| One municipality | 200 | 11 | 4 ms |

The homepage spent most of that time on 24 `LIKE` counts, one per college stream, scanning college names and descriptions. The school list filtered `status` and `institution_type_id`, then sorted by `name`. MySQL intersected two indexes and sorted the rows. CSS is 88 KB and JavaScript is 2 KB in the current production build. The HTML does not load Source Sans 3. No third-party script was found in the homepage head.

Lighthouse was not run. Chrome is installed, and the Lighthouse CLI is not. The live site was not requested. Earlier Lighthouse numbers in the Phase 2 audit are not this phase’s baseline. Core Web Vitals were not measured.

## Changes

- College-stream counts and province directory counts are cached for one hour as plain arrays. Saving, deleting, or restoring an institution clears those keys and the existing sitemap keys.
- School and college pages use the paginator total instead of a second full count.
- Migration `2026_11_09_225647_add_type_status_name_index_to_institutions_table` adds `institutions_type_status_name_index` on `(institution_type_id, status, name)`. After it, the school list uses that index. `deleted_at` is still filtered outside the index.
- `php artisan performance:audit` writes `storage/app/seo/performance.json`.

Routes, colors, layout, robots rules, and indexability were not changed.

## After, same local method

Cold cache, then a second pass with the cache warm.

| Page | Before | Cold | Warm |
| --- | --- | --- | --- |
| `/` | 66 queries, 514 ms | 73 queries, 472 ms, including 6 cache-table queries | 17 queries, 62 ms |
| `/schools` | 18 queries, 272 ms | 21 queries, 149 ms, province cache empty | 16 queries; five isolated repeats were 111, 111, 112, 113, and 113 ms |
| `/colleges` | 18 queries, 99 ms | 17 queries, 54 ms | 17 queries, 48 ms |
| `/search` | 7 queries, 78 ms | 7 queries, 78 ms | 7 queries, 73 ms |
| School profile | 13 queries, 20 ms | 13 queries, 11 ms | 13 queries, 10 ms |
| College profile | 19 queries, 22 ms | 14 queries, 6 ms | 14 queries, 6 ms |
| Province | 19 queries, 56 ms | 19 queries, 31 ms | 19 queries, 31 ms |
| District | 19 queries, 22 ms | 19 queries, 10 ms | 19 queries, 10 ms |
| Municipality | 11 queries, 4 ms | 11 queries, 4 ms | 11 queries, 4 ms |

The cold homepage query count rose because the database cache adds its own reads and writes, and the first count still includes the stream scans. Six of the 73 queries touched the cache table. A later isolated warm homepage was 17 queries and 62 ms.

The earlier 153 ms “warm” school time was one sample taken immediately after a cold homepage in the same process. Five isolated repeats after the province cache was warm were 111, 111, 112, 113, and 113 ms. That 153 ms figure is not a regression.

## SEO checks

`php artisan test --compact`: 88 tests, 88 passed, 410 assertions.

`seo:validate` passed. `seo:audit` after the cache fix: homepage, schools, colleges, search, robots, and the sitemap returned HTTP 200. Search remains `noindex, follow`.

## Deploy

Run the new migration on the server. Do not change `.env` for this phase. Cache driver stays `database`. Redis was not added.

```bash
/opt/alt/php84/usr/bin/php artisan migrate --force
```

## Not done

- No mobile or desktop Lighthouse, INP, CLS, LCP, FCP, TBT, or Speed Index.
- Province and district pages still run their own count queries. They were already under 60 ms locally.
- The Vite config can still emit Source Sans files on the next build. The current HTML does not request them.
- Production TTFB was not measured.
