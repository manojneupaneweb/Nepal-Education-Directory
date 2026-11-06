# ShikshaKhoj architecture

ShikshaKhoj is a modular monolith. One Laravel application owns the public directory, administration, and future collection workflows. Domains stay in separate namespaces so they can grow without splitting into services.

## Institution management

Owns educational institution records: identity, type, contacts, location references, and public profile content. Public pages and the admin tools will read and write institutions through services in `app/Services/Institution`, not through raw SQL in controllers.

## Geographic management

Owns Nepal's administrative geography: provinces, districts, municipalities, and wards. Institution addresses will reference these records. Code for this domain will live in `app/Services/Location`.

## Academic management

Owns education levels, faculties, programs, universities, and affiliations. An institution can offer programs and belong to a university or board without those concepts being stored as free text only. Code will live in `app/Services/Academic`.

## AI collection

Reserved for a later DeepSeek integration. The intended pieces are source discovery, extraction, normalization, and duplicate detection. Placeholders for that work are `app/AI` and `app/Services/Collection`. The API key is read from `DEEPSEEK_API_KEY` and is not used yet. No scraper or model client ships in the foundation.

## Verification management

Owns source evidence, verification state, admin review, and change history. Collected facts should remain distinguishable from reviewed facts. Code will live in `app/Services/Verification`.

## SEO management

Owns titles, descriptions, canonical URLs, structured data, indexing rules, and sitemaps. Public controllers will ask an SEO service for metadata instead of hardcoding tags in every view. Code will live in `app/Services/SEO`.

## Administration

Owns the staff dashboard, permissions, and configuration screens. Admin routes live under `/admin` and use the `auth` middleware. Guests are redirected to the named `login` route. Policies in `app/Policies` will authorize specific actions once those actions exist.

## Request flow

HTTP controllers stay thin. They validate input with form requests, call a service or action, and return a Blade view or API resource. Eloquent relationships are the default data access path. Queues in `app/Jobs` are reserved for later background collection and verification work.

## Configuration

Secrets and environment-specific values stay in `.env`. `config/app.php` defaults the timezone to `Asia/Kathmandu`. Debug output is off unless `APP_DEBUG` is explicitly true. Session cookies are HTTP-only, SameSite `lax`, and encrypted. Set `SESSION_SECURE_COOKIE=true` on HTTPS deployments.
