# ShikshaKhoj

ShikshaKhoj is an education discovery directory for Nepal. It will organize publicly available information about schools, early childhood centers, colleges, universities, technical and vocational institutions, and training centers.

The product goal is a useful, searchable directory that can earn organic search traffic and, later, advertising revenue. This repository is the application foundation: Laravel structure, MySQL configuration, public and admin shells, and room for the domains below.

## Objectives

- Publish a trustworthy directory of educational institutions across Nepal.
- Support nationwide expansion without a new architecture for each province or institution type.
- Leave clear places for AI-assisted collection, human verification, and SEO.
- Keep secrets in the environment and keep administrative routes authenticated.

## Technology stack

- PHP 8.3 or newer
- Laravel 13
- Blade templates
- Tailwind CSS 4 and Vite
- MySQL 8 or newer
- Vanilla JavaScript, with Alpine.js only if a later screen needs it
- Laravel queues for later background work
- DeepSeek API for a later collection milestone

The application is a modular monolith. It does not use a separate frontend framework or extra services.

## Requirements

- PHP 8.3+ with extensions commonly required by Laravel (`ctype`, `curl`, `dom`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`)
- Composer 2
- Node.js and npm
- MySQL 8+

## Installation

```bash
git clone <repository-url> shikshakhoj
cd shikshakhoj
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

## Environment

Edit `.env` before migrating:

- `APP_NAME` — display name, default `ShikshaKhoj`
- `APP_ENV` — `local` for development, `production` on a server
- `APP_DEBUG` — `true` only on a local machine. The example file defaults this to `false`
- `APP_URL` — public site URL
- `APP_TIMEZONE` — default `Asia/Kathmandu`
- `DB_*` — MySQL host, database, username, and password
- `DEEPSEEK_API_KEY` — leave empty until the collection milestone

Session cookies are HTTP-only and encrypted. On an HTTPS deployment set `SESSION_SECURE_COOKIE=true`.

Do not commit `.env`.

## Database

Create an empty MySQL database, then point `DB_DATABASE` at it.

```bash
mysql -e "CREATE DATABASE shikshakhoj CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

`php artisan migrate` applies new migrations only. Do not run `migrate:fresh` against a database that already holds directory data.

Automated tests use a separate database, `shikshakhoj_test`, configured in `phpunit.xml`. Create it before running the suite:

```bash
mysql -e "CREATE DATABASE shikshakhoj_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan test
```

The schema covers geography, institutions, contacts, programs, affiliations, facilities, media, sources, collection history, field evidence, verification, and SEO metadata. Tables are `utf8mb4`. Geography is not seeded in this milestone. Factories create records named `Synthetic …` for tests only. Do not run `db:seed` to populate institutions.

See [docs/DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md) for tables, keys, and status values.

Load the NSO geographic codes after migrating:

```bash
php artisan geography:import --validate-only --report
php artisan geography:import
```

The import is described in [docs/geography/NEPAL_GEOGRAPHIC_DATA.md](docs/geography/NEPAL_GEOGRAPHIC_DATA.md). It does not include wards or current province names, because those are not in the selected source file.

## Development

```bash
php artisan serve
npm run dev
php artisan test
vendor/bin/pint
```

`php artisan serve` starts the site at `http://127.0.0.1:8000`. Use `npm run dev` while changing CSS or JavaScript. `npm run build` writes production assets to `public/build`.

## Project structure

```text
app/
    Http/Controllers/Public    Public pages
    Http/Controllers/Admin     Authenticated administration
    Services/                  Institution, location, academic, SEO, verification, collection
    AI/                        Future DeepSeek contracts, providers, and extractors
    Jobs/                      Future queued work
    Policies/                  Future authorization
routes/web.php                 Public site and /admin
routes/api.php                 Stateless API prefix
resources/views/layouts        Public and admin shells
resources/views/public         Homepage and search placeholder
resources/views/admin          Dashboard placeholder
docs/ARCHITECTURE.md           Domain responsibilities
docs/DATABASE_SCHEMA.md        Tables, keys, and provenance
```

Reserved service and AI directories document where later code belongs. They do not contain unused classes.

## Routes

| Method | Path | Name | Access |
| --- | --- | --- | --- |
| GET | `/` | `home` | Public |
| GET | `/search` | `search` | Public |
| GET | `/login` | `login` | Public placeholder |
| GET | `/admin` | `admin.dashboard` | Authenticated |
| GET | `/api/status` | `api.status` | Public status only |
| GET | `/up` | — | Laravel health check |

## Roadmap

1. Database design for geography, institutions, academics, verification, and SEO. The schema is in place. Official geography is not loaded yet.
2. Institution management: models, validation, admin editing, and public profiles.
3. Nepal geography seed data and location filters.
4. Academic programs, levels, and affiliations.
5. Verification workflow and change history.
6. SEO metadata, canonical URLs, structured data, and sitemaps.
7. DeepSeek-assisted collection, queues, normalization, and duplicate detection.
8. Authentication, roles, and staff permissions.
9. Search that queries real institution records.
10. Production deployment and advertising placement.
