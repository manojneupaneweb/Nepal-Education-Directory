# School data research

Date: 2026-11-06.

## Authoritative source found

CEHRD publishes the school census as Flash I annexes.

- Page: https://cehrd.gov.np/content/15831/flash-i-report-2082/
- File named on that page: Annex 2, School Details, 2025-2026
- Direct file URL: https://giwmscdnone.gov.np/media/files/Annex%202_School%20Details%2C%202025-2026_sxuckm1.xlsx
- Earlier year, same series: https://cehrd.gov.np/content/14541/annex-file/ (Annex 2, School Details, 2024-2025)

IEMIS (https://emis.cehrd.gov.np/) shows national totals for academic year 2082, including about 39,535 school units on the public dashboard. That dashboard does not offer a school-by-school download. Individual profiles require an EMIS code.

## Recovery on 2026-11-06

The CEHRD page https://cehrd.gov.np/content/15831/flash-i-report-2082/ and the www host of the same path both point at one file:

https://giwmscdnone.gov.np/media/files/Annex%202_School%20Details%2C%202025-2026_sxuckm1.xlsx

That host returned an empty reply. The same URL was not requested again.

Nepal Public Data (https://nepalpublicdata.com/figure-sch_schools.html) cites this workbook and records a fingerprint. It does not host the xlsx. Its metadata, collected 2026-10-04, says:

- Title: Flash I Report 2082: Annex 2_School Details, 2025-2026
- SHA-256: 612addcec9747a327c202ac533d0c7e4dcd9ba5493915301a84ce05da222d36b
- Size: 305,742 bytes
- Publisher page: the CEHRD Flash I 2082 page above
- Cited sheet: Annex 2.1 Schools, including a Nepal total on row 841 of 35,951 schools for academic year 2082 (2025/26)

No other official or mirror copy of the bytes was found. The workbook was not saved, so the sheet names, headings, and row types were not opened and are not verified.

The cited size and the row-841 total are a warning. A file of about 300 KB that is quoted by a single national total row is the shape of a province, district, and local-level summary, not a list of about 36,000 named schools. That is an inference from the mirror’s citation. It is not a validation of the workbook.

## Download result

The annex workbook was not recovered. No school rows were imported.

Older public lists were not used as a substitute:

- Open Nepal school exam results for 2063 BS are exam scores, not the current school census.
- A mapped point file of some schools does not cover every school and is not the CEHRD annex.

## What the annex is expected to contain

CEHRD school-detail annexes in this series list each reported school with identity and location fields (name, code, type, district, local level). Exact column names will be confirmed from the workbook header when the file can be downloaded. Fields the annex does not contain, such as a website or principal, will stay empty.

## Matching rule already decided

A school will be linked to a municipality only when the local-level name matches one of the 753 stored local governments in the right district. A district-only match will not pick a municipality. Schools whose district cannot be matched cannot be inserted until `district_id` is allowed to be empty, or until they are kept in a review file. That schema change waits until the workbook is actually in hand.
