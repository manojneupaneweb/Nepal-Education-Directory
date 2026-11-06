# Institution domain

An institution is one school, campus, college, university, early-childhood center, or training center. Branches use `parent_institution_id`. The public identifier is the unique `slug`, not the numeric id.

## Types and categories

`institution_types` is the broad kind of institution: School, College, University, Montessori, Early Childhood Development (ECD) Center, Technical Institute, Vocational Training Center, and Educational Training Center.

`institution_categories` is a separate, many-to-many label such as Government School or Science College. A category may have a parent category. Categories are not inferred from the institution name.

Seed the initial lists without creating institutions:

```bash
php artisan db:seed --class=InstitutionClassificationSeeder
```

The seeder is idempotent. It does not remove categories you add later.

## Ownership and management

`ownership_type` answers who owns the institution: government, community, private, public, nonprofit, other, or unknown.

`management_type` answers who operates it: `government_managed`, `community_managed`, `privately_managed`, `trust_managed`, `other`, or `unknown`.

A privately owned institution can still be trust-managed. The two fields are stored and validated separately. Both may be empty.

## Publication and verification

`status` is the editorial state: `draft`, `pending`, `published`, `rejected`, or `archived`.

`verification_status` is the evidence state: `unverified`, `source_matched`, `admin_reviewed`, or `institution_verified`.

Creating a record always starts as `draft` and `unverified`. Saving an edit does not change either status. Submit moves a draft or rejected record to `pending`. Approve moves `pending` to `published` and sets `published_at`. Reject moves `pending` to `rejected` and can store a reason on `institution_change_logs`. Archive sets `archived`.

Approve does not set a verification status. Nothing in this milestone marks a record verified because data was extracted automatically.

## Relationships

An institution belongs to one type, one province, one district, and one municipality. Ward, parent, registration number, website, logo, and coordinates are optional. It has many categories, contacts, programs, images, source rows, and verification rows. Child institutions are `branches`.

The province, district, municipality, and ward must be one chain. A ward from another municipality is rejected. An inactive type or category is rejected. A parent chain that loops back to the institution is rejected.

## Slugs

If the slug is blank, it is built from the English name with Laravel's slug rules. If that slug already exists, including on a soft-deleted row, the next free `-2`, `-3`, and so on is used. Editing keeps the current slug when the slug field is blank or unchanged. Nepali text is not converted into Latin letters for the URL. An English name that cannot produce a slug is rejected.

## Admin access

`users.is_admin` must be true. Authenticated users who are not admins receive 403 on institution management, including approve and reject. Guests are sent to the login page.

Admin pages:

- `GET /admin/institutions`
- `GET /admin/institutions/create`
- `GET /admin/institutions/{id}`
- `GET /admin/institutions/{id}/edit`

There is no public institution page in this milestone. No sample institutions are seeded.
