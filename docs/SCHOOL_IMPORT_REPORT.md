# School import report

Date: 2026-11-06.

No school import ran. The 2025-2026 Annex 2 workbook was not recovered, so it was not validated and not imported.

Recovery tried:

- CEHRD publication page, including the www host. Both expose the same `giwmscdnone.gov.np` file URL.
- Nepal Public Data’s source record for that annex. It stores a SHA-256 and a size only. It does not provide the workbook.

Blocker: `giwmscdnone.gov.np` returns an empty reply for the Annex 2 file. There is no second permitted copy of the bytes.

Mirror metadata that could not be checked against a file:

- SHA-256 `612addcec9747a327c202ac533d0c7e4dcd9ba5493915301a84ce05da222d36b`
- Size 305,742 bytes
- Cited worksheet label “Annex 2.1 Schools”, Nepal total row 841, 35,951 schools

It is not safe to import. The file is absent, and the cited size suggests the annex may be geographic totals rather than one row per school.

| Check | Result |
| --- | --- |
| Source file saved | No |
| Source rows read | 0 |
| Schools inserted | 0 |
| Unique schools in `institutions` | 0 |
| Duplicate candidates | 0 |
| Matched to a local government | 0 |
| Unresolved location | 0 |
| Verified | 0 |
| Unverified | 0 |
| Failed source | Annex 2 download, empty reply from giwmscdnone.gov.np |

Provinces (7), districts (77), municipalities (753), universities, faculties, and university programs were not changed.

This is not a complete collection of Nepal’s schools. The next step is to place `Annex 2_School Details, 2025-2026` in `storage/app/research/sources/cehrd/` and import from that file only.
