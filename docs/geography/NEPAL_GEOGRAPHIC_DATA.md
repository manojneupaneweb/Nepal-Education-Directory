# Nepal geographic data

ShikshaKhoj stores one administrative hierarchy:

Province → District → Municipality (local government) → Ward

Institutions reference those rows. They do not store a second copy of the place name as their only location.

## Source used for import

| Item | Value |
| --- | --- |
| Dataset | Geographical codes for provinces, districts and local units |
| Organization | National Statistics Office, Nepal. The coding note was prepared by the former Central Bureau of Statistics in 2017. |
| Page | https://nsonepal.gov.np/content/7694/7694-geographical-codes-map-provi/ |
| File | https://giwmscdnone.gov.np/media/app/public/36/posts/1695378470_83.xls |
| Page date | 5 Ashoj 2080 (21 September 2023) |
| Workbook last saved | 22 September 2023 |
| Reference period | The 2017 local-level structure encoded in that workbook |
| Retrieved | 4 November 2026 |
| Machine-readable | Yes. Excel `.xls`, converted to CSV without renaming units |
| Workbook SHA-256 | `b6d139e1d160f9d756a9f42b6e385c1227a58edac51efd2eb64fb68efe472279` |
| Levels present | Province, district, local unit |
| Levels absent | Ward |

The workbook contains 7 province codes, 77 district codes, and 753 local-unit codes. Every local-unit code matches a district code in the same file. No parent link is missing inside this file.

Local-unit counts in the file:

| Type | Count |
| --- | --- |
| Metropolitan | 6 |
| Sub-metropolitan | 11 |
| Municipality | 276 |
| Rural municipality | 460 |

The NSO page links a coding-method PDF, a district map, a provincial map, and this spreadsheet. The PDF is https://giwmscdnone.gov.np/media/app/public/36/posts/1695378468_14.pdf. It describes the code pattern: one province digit, two district digits, two local-unit digits. Example from that note: Kathmandu Metropolitan City is `30603`.

No separate license text was attached to the download. It is a public statistical table on the NSO site. The original workbook is kept at `database/data/geography/nso-geographical-codes-2017/1695378470_83.xls`. The CSV files next to it are a direct extract. They do not add names that the workbook does not contain.

## Sources checked and not merged

MoFAGA publishes a local-government contact directory at https://www.mofaga.gov.np/local-contact and a profile book notice at https://www.mofaga.gov.np/detail/3249. The contact page shows current province names such as कोशी प्रदेश. It is an HTML directory, not the code file, and it was not merged with the NSO codes.

NSO census ward tables, including https://data.nsonepal.gov.np/nl/dataset/ward and https://censusresults.nsonepal.gov.np/downloads/ward?type=data, are population tables. A complete official ward list was not downloaded into this repository. Ward numbers are therefore not imported. They are not guessed from those tables.

The NSO code file and the MoFAGA pages do not use the same labels for provinces. The code file says `State 1` through `State 7`. MoFAGA's contact directory uses the later province names. Those names are not copied into the official name columns, because that would mix two sources. When a Unicode name list is obtained from one chosen official file, it can be imported as an update or as rows in `geographic_aliases`.

## What was not imported, and why

- Province and district Nepali columns in the workbook are legacy font characters such as `k|b]z`, not Unicode. They are discarded. `name_ne` stays null. Nepali text is not transliterated.
- English province names stay `State 1` through `State 7`, which is what the workbook says.
- District English names are the workbook names with capitalization normalized (`TAPLEJUNG` becomes `Taplejung`). Spelling is not changed.
- Metropolitan and sub-metropolitan rows are spelled `Metropolitian` and `Sub-Metropolitian` in the workbook. The stored name keeps that spelling. The type is taken from the designation, including that spelling, so a sub-metropolitan city is not stored as a metropolitan city. The import report lists those spellings for manual review.
- Wards are not created. The file has no ward numbers, names, or ward codes.

## Import

From the project root, after migrations:

```bash
php artisan geography:import --validate-only --report
php artisan geography:import --dry-run --report
php artisan geography:import --report
```

The same import can be run with:

```bash
php artisan db:seed --class=Database\\Seeders\\Geography\\GeographySeeder
```

`db:seed` without that class does not load geography.

Options:

- `--path` points at another directory with the same CSV layout. The default path is `database/data/geography/nso-geographical-codes-2017`.
- `--dry-run` loads the rows inside a transaction and rolls it back.
- `--validate-only` checks the files and writes nothing.
- `--report` writes JSON under `storage/app/geography`.

Running the import twice updates existing rows matched by official code. It does not insert a second copy. It does not delete a row that is absent from a newer file, and it does not mark rows inactive by itself. It does not replace a stored Nepali name with an empty value.

## File format

`manifest.json` requires `key`, `name`, `organization`, `source_url`, and `levels`.

`provinces.csv`: `code,name_en,name_ne`

`districts.csv`: `code,name_en,name_ne,province_code`

`local-units.csv`: `code,name_en,name_ne,district_code`

`wards.csv`, optional: `municipality_code,ward_number,name_en,name_ne,code`

Leave `name_en` and `name_ne` empty when the source has no name. Do not invent a ward name just because a ward number exists.

## Normalization

- Trim text and collapse repeated spaces.
- Store Nepali only when the value is Devanagari Unicode. Normalize it to NFC.
- Title-case an English name only when the source cell is entirely uppercase.
- Build the slug from the English name with Laravel's slug rules. A slug collision with a different code is rejected and reported. Units are not merged because the names look similar.
- Municipality type comes from the official English designation in the source name: `rural municipality`, `sub-metropolitan`, `metropolitan` (including the workbook's `metropolitian` spelling), then `municipality`. Any other wording is invalid and is not inserted.

## Validation

The importer reports discovered, new, updated, unchanged, skipped, invalid, duplicate, and missing-parent rows. Invalid rows are printed and are not inserted.

The database also enforces:

- a district's province foreign key
- a municipality's district foreign key
- a ward's municipality foreign key
- unique province, district, and municipality codes
- unique ward number inside a municipality
- municipality type restricted to the four supported values

Foreign keys do not stop an institution from pointing at a district in a different province from `province_id`. `App\Services\Location\LocationConsistency` reports that mismatch. Cascading form lists are served by:

- `GET /api/geography/provinces`
- `GET /api/geography/provinces/{province}/districts`
- `GET /api/geography/districts/{district}/municipalities`
- `GET /api/geography/municipalities/{municipality}/wards`

Those routes return active rows for the requested parent only.

## Updating the dataset later

1. Obtain one official file that covers the levels you intend to change.
2. Put it in a new directory under `database/data/geography/` with a manifest and CSV extract.
3. Run `--validate-only` and read the report.
4. Run `--dry-run` and compare counts.
5. Import only after the report has no unexpected invalid rows.
6. Match on official codes. Do not delete old rows in the same step.
7. If a unit was renamed or replaced and the change is documented by the source, keep the old row, set `is_active` to false in a reviewed change, and record the previous name in `geographic_aliases`.
8. Add wards only from a file that lists municipality code and ward number. Leave ward names null when the file has numbers only.

The current import is the 2017 code structure as republished by NSO in the 2023 spreadsheet. It is not a claim that every later local-level upgrade is included. Treat province names `State 1` through `State 7` as dataset labels, not as the current constitutional names, until a single official Unicode name list is imported.
