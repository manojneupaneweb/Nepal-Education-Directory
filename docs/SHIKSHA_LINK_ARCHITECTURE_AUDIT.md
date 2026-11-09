# Shiksha Link architecture audit

Date: 2026-11-06. This audit describes the application as it exists. It does not treat planned national coverage as finished.

## Existing architecture

Laravel application on PHP 8.5. Public pages use Blade and Tailwind CSS v4, compiled by Vite. MySQL is the application database. PHPUnit covers geography import, institutions, and the university directory. Admin login exists for institution create, edit, submit, approve, reject, and archive.

Universities are a separate table from `institutions`. Schools and colleges are intended to live on `institutions`, with affiliations and programs linked through pivot tables. That split is already in use and should stay. A university should not be copied into `institutions` just to satisfy a single-table diagram.

## Tables and relationships already present

Geography:

- `provinces`, `districts`, `municipalities` (local governments), `wards`.
- Loaded counts: 7 provinces, 77 districts, 753 municipalities, 0 wards.
- Province display names are the current names (Koshi through Sudurpashchim). Slugs remain `state-1` through `state-7`.

Institutions:

- `institutions`, categories, ownership, contacts, facilities, images, sources, field-level evidence, SEO, and verification tables.
- `institution_affiliations` links a college to a university.
- `institution_programs` links a college to a program.
- Loaded count: 0 institutions and 0 affiliations. The school and college directories have no records yet.

Universities and programs:

- `universities` extended with description, history, vision, mission, contact, logo, and verification fields.
- `university_faculties`, `university_leaders`, `university_sources`.
- `programs` carry `university_id`, faculty, duration, eligibility, source URL, and verification status.
- Loaded counts: 29 universities, 109 faculties, 1,078 university programs.
- There is no separate departments table. Department or school names that came from an official page are stored as faculties.

Public routes that already work:

- `/`, `/schools`, `/universities`, `/universities/{slug}`
- `/university-programs`, `/university-programs/{university}/programs`, `/university-programs/{program}`
- `/provinces`, `/provinces/{slug}`, `/districts/{slug}`, local-government pages
- `/institutions/{slug}`, `/search`, `/sitemap.xml`, `/neb`, `/pabson`
- Admin institution management behind auth

Research today is `php artisan universities:research`. It reads the UGC list, can fill empty identity fields, and stores a research history. It does not call DeepSeek, even though `config/services.php` has a DeepSeek key setting. Scraped program text is stored as partially verified. Official websites are not overwritten once set.

## Problems

- No school or college rows, so school, college, and affiliation pages cannot be complete.
- Ward rows are not loaded, so ward links cannot be assigned.
- Some university programs from Edusanjal repeat a degree that an official page already named in a shorter form. A cleanup pass removed exact normalized duplicates. Near-duplicates can remain when the titles differ.
- Several university about pages (Tribhuvan, Pokhara, Patan Academy, Pokhara Academy, University of Nepal, Gandaki, Vidushi Yogmaya) still lack a multi-paragraph official description because the fetched official page did not publish one.
- Gandaki and Vidushi Yogmaya still have no confirmed official website.
- Leadership, fees, and teacher lists are mostly empty. Those fields must stay empty until a page names them.
- Directory sites in the master brief (CollegeNP, Edusanjal colleges and schools, Colleges Nepal, and the others) have not been fully crawled. Edusanjal’s course index was read (67 pages). University-linked courses were added with the Edusanjal URL as the source. Short courses with no university were not added.

## What not to rebuild

- Do not replace `universities` with a parallel university table.
- Do not store programs as JSON on the university row. Programs are already rows.
- Do not invent colleges, principals, coordinates, or fees to fill empty profiles.
- Do not mark a third-party directory as verified. Edusanjal-sourced programs stay partially verified.
- Do not assign a municipality from the district alone.

## Implementation order from here

1. Keep the current university and program pages. Fill only from official or clearly sourced pages.
2. Import schools from an official bulk list (CEHRD or another government file), with province, district, and local government only when the code matches the geography tables.
3. Import colleges only when a page names the college and its university. Write `institution_affiliations` and `institution_programs` from that evidence.
4. Add wards only from the official ward list, not by guessing 1–N for every municipality.
5. Leave DeepSeek as a structured extractor behind validation. Do not treat its JSON as verified.

The national school and college crawl is not done. Coverage reports that claim it is done would be false.
