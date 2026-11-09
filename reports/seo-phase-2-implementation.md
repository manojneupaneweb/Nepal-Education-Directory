# Phase 2 SEO implementation

Date: 2026-11-09  
Baseline: `015329a`

## Issues fixed

- Empty municipality pages stay available and send `noindex, follow`.
- `/schools` and `/colleges` page 1 stay indexable. Later pages send `noindex, follow` and keep a canonical that includes `?page=`.
- `/suggest-an-institution` sends `noindex, follow`.
- A university college list with zero published colleges sends `noindex, follow` and is left out of the university sitemap.
- `/types/school` and `/types/college` are `noindex` and their canonical URLs are `/schools` and `/colleges`. Other type pages keep their own canonical.
- `SeoService::isIndexable()` requires a published name plus one quality signal. A website is a signal, not a requirement. Name and address alone are not enough. The description minimum is `seo.quality.min_description_length` (80). Other signals are a public phone or email, a linked program, or programs stored on the record. No filler text is written and no records are deleted.
- Institution JSON-LD drops nulls and empty arrays. `logo` and `sameAs` are included only for real HTTP(S) URLs. School and CollegeOrUniversity types are unchanged. No ratings, reviews, or hours are added.
- School and college sitemaps use `lazyById(200)` and the indexable query, so thin records and deleted records are excluded. Saving, deleting, or restoring an institution clears the school, college, and location sitemap caches.
- Gallery photos use “Gallery photo of {name}”. Institution and university logos use “{name} logo”. The mobile-nav logo stays decorative with an empty alt. Width and height were added where the layout size is fixed.
- Same-municipality duplicate names: none. See `reports/duplicate-school-audit.md`.
- The admin settings route was missing its controller import, which made password updates return 500. The import was restored.

## Indexability rules

| Page | Rule |
| --- | --- |
| Published institution with a quality signal | `index, follow` |
| Published institution with only a name and address | `noindex, follow`, page stays up |
| Municipality with zero published institutions | `noindex, follow` |
| Schools or colleges page 1 | `index, follow` |
| Schools or colleges page 2+ or filtered | `noindex, follow`; canonical keeps the current page number when `page` is greater than 1 |
| `/types/school`, `/types/college` | `noindex, follow`; canonical is `/schools` or `/colleges` |
| University with zero published colleges | college list is `noindex, follow` |
| Suggest, search, login | `noindex, follow` |

## Sitemap

School URLs are built in batches of 200 instead of one `get()` of every school. XML is still escaped by the Blade sitemap view. `lastmod` remains the record `updated_at` date. Sitemap paths are unchanged: `/sitemap.xml` and `/sitemaps/{section}.xml`.

## Files

- `app/Services/SEO/SeoService.php`
- `app/Models/Institution.php`
- `app/Http/Controllers/Public/SitemapController.php`
- `app/Observers/InstitutionSitemapObserver.php`
- `app/Providers/AppServiceProvider.php`
- `config/seo.php`
- `routes/web.php`
- Public Blade views for institutions, colleges, home, universities, and programs
- `tests/Feature/SeoPhase2Test.php`
- `reports/duplicate-school-audit.md`
- `reports/seo-phase-2-implementation.md`

## Production domain

Local `APP_URL` is `http://localhost:8000`. The application does not force HTTPS or a www host. Canonical and sitemap hosts follow `APP_URL` at runtime. Production `.env` was not changed. Set production `APP_URL` to the public HTTPS origin, and configure the web server to redirect HTTP to HTTPS and to one host, before submitting the sitemap.

## Tests

Full suite: 84 tests, 84 passed, 373 assertions.

`php artisan seo:validate` passed. `php artisan seo:audit` checked `/`, `/schools`, `/colleges`, `/search`, `/robots.txt`, and `/sitemap.xml` at HTTP 200. Search is `noindex, follow`. Duplicate slugs: 0. `php artisan seo:report` printed that audit.

## Not claimed

Google indexing, Search Console verification, and new Core Web Vitals numbers were not measured.

## Deploy

```bash
cd /home/softwori/websites/Nepal-Education-Directory
git pull origin main
/opt/alt/php84/usr/bin/php artisan seo:sitemap
/opt/alt/php84/usr/bin/php artisan seo:validate
```

If `public/.htaccess` has local edits, review `git diff public/.htaccess` before the pull. Confirm `APP_URL` is the live HTTPS site.
