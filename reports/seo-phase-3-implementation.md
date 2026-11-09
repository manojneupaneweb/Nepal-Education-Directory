# Phase 3 implementation

Date: 2026-11-09  
Builds on commits `015329a` and `fb27438`. SeoService was extended, not replaced.

## 1. Content audit

Public routes already cover schools, colleges, universities, programs, provinces, districts, municipalities, and one blog listing. Institution pages already hide empty description, program, gallery, and contact blocks. `data/blogs.json` stores a title, excerpt, category, author, and date. It does not store an article body. Several excerpts claim rankings, fees, or national school counts with no source in the file.

Nepali names exist as `name_ne` on places and institutions. There is no translated page set and no `hreflang` tag. English URLs were left unchanged. Empty Nepali pages were not created.

## 2. Database

Published institutions: 22,579. Indexable under the Phase 2 rule: 9,046. Empty municipalities: 156 of 753. Verification: 22,187 source matched and 392 unverified. No published profile is marked institution verified, so no verified badge was added.

## 3. Completeness

See `reports/institution-content-quality.md`.

| Level | Profiles |
| --- | --- |
| 1 Basic | 19,777 |
| 2 Contactable | 1,570 |
| 3 Informative | 339 |
| 4 Detailed | 474 |
| 5 Comprehensive | 419 |

Schools are almost all level 1. Colleges hold nearly all of levels 3 to 5. The largest missing fields are programs or facilities (21,814), logos (22,290), official websites (21,560), and public phone or email (20,017).

## 4. Institution profiles

Visible breadcrumbs now include the district and municipality. The location sidebar links the municipality. A school lists up to six other published schools in the same municipality. Colleges keep the existing similar-college block. About, Programs, Gallery, and Contact shortcuts appear only when that information is stored. No fees, rankings, dates, ratings, or facilities were added.

## 5. Location pages

Province, district, and municipality pages now state the published school and college counts for that place. A province names the district with the most listed schools and colleges. A district names the municipality with the most listed schools and shows each municipality’s counts. An empty municipality stays available and `noindex, follow`.

## 6. Blog

Individual article URLs were not added. The current posts are excerpts, and some factual claims are unsourced. `/blog` remains the only blog URL. A future article needs a body, a source for factual claims, its own slug, metadata, Article and BreadcrumbList JSON-LD, and a published status before it is indexable.

## 7. Internal links

Province to district, district to municipality, municipality to institution, and institution back to district and municipality were already partly present. Municipality links were added on the institution page, and district pages now show school and college counts on each municipality link. Filtered school and college links on a municipality point at the existing directory URLs.

## 8. AI enrichment

No paid API is required and none was connected. A generated factual claim must keep its source URL and the date it was checked. Draft text must stay separate from the published description. `verification_status` remains the human gate: `source_matched` is not a public verified badge, and `institution_verified` is the status that means a person reviewed the record. Manual fields must not be overwritten by a draft. `updated_at` must not change only because an audit ran. The new audit commands are read-only.

## 9. Files

- `app/Services/SEO/InstitutionContentQuality.php`
- `app/Console/Commands/SeoContentAudit.php`
- `app/Console/Commands/SeoLocationAudit.php`
- `app/Console/Commands/SeoContentReport.php`
- `app/Http/Controllers/Public/DirectoryController.php`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Services/SEO/SeoService.php`
- Public province, district, municipality, and institution views
- `resources/views/admin/dashboard.blade.php`
- `tests/Feature/SeoPhase3Test.php`
- `reports/institution-content-quality.md`
- `reports/seo-content-keyword-map.md`

## 10. Database changes

None. No migration and no content rewrite.

## 11. Tests

`php artisan test --compact`: 87 tests, 87 passed, 392 assertions.

`seo:content-audit`, `seo:location-audit`, and `seo:content-report` completed. Published 22,579. Indexable 9,046. Empty municipalities 156 of 753.

## 12. Remaining gaps

- 19,777 published profiles are still name and location only.
- Blog excerpts are not articles and include unsourced claims.
- Production `APP_URL` was not confirmed against `https://shikshyasanjal.com`.
- Rankings, traffic, indexing, and AdSense were not measured.

## 13. Phase 4

1. Confirm the live HTTPS `APP_URL` and one host redirect.
2. Enrich colleges that are still basic, using the institution’s own site and a stored source URL.
3. Write a small number of sourced guides only after the article body and citation exist.
4. Do not generate a page for every keyword variation.
