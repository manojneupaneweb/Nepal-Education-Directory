# Institution content quality

Date: 2026-11-09  
Source: `php artisan seo:content-audit` on the local directory database. No records were edited.

## Levels

A published profile is placed in the highest level its stored fields support.

| Level | Meaning | Profiles |
| --- | --- | --- |
| 1 Basic | Name and location, without a public phone or email | 19,777 |
| 2 Contactable | Public phone or email, without a full description and offering | 1,570 |
| 3 Informative | Description of at least 80 characters plus a program or facility | 339 |
| 4 Detailed | Contact, a description or offering, and a website, image, or facility | 474 |
| 5 Comprehensive | Description, contact, an offering, and a website or image | 419 |

Published institutions: **22,579**. Profiles that meet the Phase 2 index rule: **9,046**.

## By type

| Type | 1 | 2 | 3 | 4 | 5 |
| --- | --- | --- | --- | --- | --- |
| School | 19,417 | 1,523 | 1 | 212 | 1 |
| College | 360 | 47 | 338 | 262 | 417 |
| Technical institute | 0 | 0 | 0 | 0 | 1 |

## By province

| Province | Basic | Above basic |
| --- | --- | --- |
| Bagmati | 4,094 | 1,175 |
| Koshi | 3,708 | 516 |
| Madhesh | 3,163 | 347 |
| Lumbini | 3,011 | 299 |
| Sudurpashchim | 2,085 | 104 |
| Karnali | 1,936 | 130 |
| Gandaki | 1,780 | 231 |

Districts with the most basic profiles: Kathmandu 1,191 of 1,894, Morang 678 of 876, Rupandehi 590 of 688, Jhapa 583 of 665. Districts with the most profiles above basic: Kathmandu 703, Morang 198, Lalitpur 185, Kaski 142.

## Missing stored fields

| Field | Published profiles missing it |
| --- | --- |
| Description of at least 80 characters | 13,572 |
| Public phone or email | 20,017 |
| Official website | 21,560 |
| Program or facility | 21,814 |
| Logo or gallery image | 22,290 |

Verification status on published profiles: 22,187 source matched, 392 unverified. None are marked institution verified. A source match is not a verified badge.

## Enrichment order

1. Colleges that are still basic or contactable, because a college page is where programs and a website change a decision.
2. Schools that already have a phone or email but no description or offering.
3. Official websites only when the site is the institution’s own site.
4. Leave name-only schools in the directory. Do not write filler descriptions.
