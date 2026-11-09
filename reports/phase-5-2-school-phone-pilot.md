# Phase 5.2 school phone pilot

Date: 2026-11-09

The eligible set was read from the local database. No institution field was changed. No phone proposal was created, accepted, or rejected.

Websites were not opened. An automatic fetch of the 33 homepages would have been a crawler, which this pilot does not add. A phone number is therefore not recorded from a page that was not read.

## Eligible schools

The same rule as the source-review queue returned **33** published schools with an `http` or `https` official website and no public phone contact. None of them had a stored phone, a stored email, or an evidence proposal.

| ID | Name | Municipality | District | Official website |
| --- | --- | --- | --- | --- |
| 1120 | Green Plant Boarding Secondary School | Devdaha Urban Municipality | Rupandehi | https://gpbss.edu.np/ |
| 1216 | Budhanilkantha Secondary School | Kathmandu Metropolitian City | Kathmandu | http://www.budhanilkanthahigher.edu.np |
| 1820 | Sunkuda Secondary School | Bitthadchir Rural Municipality | Bajhang | https://www.sunkudaschool.edu.np/ |
| 2867 | Talbesi Secondary School | Joshipur Rural Municipality | Kailali | https://www.talbesi.edu.np/ |
| 3863 | Shree Mahendra Ratna Secondary School | Jhapa Rural Municipality | Jhapa | https://www.mahendraratnaschool.edu.np |
| 4268 | Maiya Devi Girls College | Shuklaphanta Urban Municipality | Kanchanpur | https://maiyadevigirlscollege.edu.np/ |
| 4282 | Bhawani Secondary School, Gauripur | Tansen Urban Municipality | Palpa | https://www.bhawani.edu.np/ |
| 8986 | Shree Adarsha Secondary School | Mechinagar Urban Municipality | Jhapa | http://adarshahsschool.edu.np/ |
| 9864 | Sarbajanik Secondary School | Pheta Rural Municipality | Bara | https://sarwajanikmavi.edu.np/ |
| 20566 | Secondary School, Khakhana | Mahottari Rural Municipality | Mahottari | https://khakhanass.edu.np/ |
| 21215 | Gaurishankar Secondary School | Punarbas Urban Municipality | Kanchanpur | https://www.sgsspunarbas.edu.np/ |
| 21310 | Gauri Shankar Secondary School | Ramechhap Urban Municipality | Ramechhap | https://www.shreegaurishankar.edu.np/about-us/… |
| 22087 | Nawalpur Secondary Boarding School | Madhya Bindu Urban Municipality | Nawalparasi East | https://nawalpurebs.edu.np/ |
| 22088 | Gaindakot English School | Gaidakot Urban Municipality | Nawalparasi East | https://ges.edu.np/ |
| 22089 | Nawalpur Academy | Kawasoti Urban Municipality | Nawalparasi East | https://nawalpuracademy.edu.np/ |
| 22090 | Kalika Model Secondary School | Gaidakot Urban Municipality | Nawalparasi East | https://kalikasschool.edu.np/ |
| 22091 | Blue Star English Secondary School | Binayi Tribeni Rural Municipality | Nawalparasi East | https://bluestar.edu.np/ |
| 22121 | Polar Star Secondary English Boarding School | Bharatpur Metropolitian City | Chitawan | https://pssebs.edu.np/ |
| 22122 | Parijat Secondary English Boarding School | Bharatpur Metropolitian City | Chitawan | https://parijatboarding.edu.np/ |
| 22123 | Man Shiksha Mandir English Boarding School | Bharatpur Metropolitian City | Chitawan | https://msm.edu.np/ |
| 22124 | Gurukul Secondary School | Bharatpur Metropolitian City | Chitawan | https://gurukulchitwan.edu.np/ |
| 22125 | Motherland English Boarding School | Bharatpur Metropolitian City | Chitawan | https://mebsschool.edu.np/ |
| 22133 | Sky Rider Secondary English Boarding School | Ratnanagar Urban Municipality | Chitawan | https://skyrider.edu.np/ |
| 22140 | Daisy English Boarding Secondary School | Khairahani Urban Municipality | Chitawan | https://mydaisyschool.com/ |
| 22141 | Glow Shine Academy Secondary School | Khairahani Urban Municipality | Chitawan | https://gsacademy.edu.np/ |
| 22145 | Sainik Awasiya Mahavidyalaya, Chitwan | Kalika Urban Municipality | Chitawan | https://samc.edu.np/ |
| 22146 | Kumudini English Boarding School | Bardaghat Urban Municipality | Nawalparasi West | https://kumudiniebs.edu.np/ |
| 22158 | Scholars Home Boarding Secondary School | Butwal Sub-Metropolitian City | Rupandehi | https://scholarshomebss.edu.np/ |
| 22159 | Tinau English Boarding School | Butwal Sub-Metropolitian City | Rupandehi | https://tinauschool.edu.np/ |
| 22160 | New Horizon English Boarding Secondary School | Butwal Sub-Metropolitian City | Rupandehi | https://newhorizon.edu.np/ |
| 22161 | Oxford English Boarding Secondary School | Butwal Sub-Metropolitian City | Rupandehi | https://oxfordsecondaryschool.edu.np/ |
| 22162 | Deep Boarding Secondary School | Butwal Sub-Metropolitian City | Rupandehi | https://deepboarding.edu.np/ |
| 22170 | Sagarmatha Secondary Boarding School | Devdaha Urban Municipality | Rupandehi | https://sagarmathaacademy.edu.np/ |

Record 4268 is named Maiya Devi Girls College and is stored as a school. Its website was not used to change that type.

Record 21310 stores a long about-page URL rather than the site root. The full stored value is `https://www.shreegaurishankar.edu.np/about-us/` followed by a Nepali slug.

## Review result

| Result | Count |
| --- | --- |
| Websites opened and read | 0 |
| Websites unavailable | not measured |
| Websites with no published phone | not measured |
| Proposals created | 0 |
| Proposals accepted | 0 |
| Proposals rejected | 0 |
| Proposals left pending | 0 |
| Phone fields enriched | 0 |

There are no rejection reasons because no proposal was made.

## Checks

`php artisan seo:validate` passed. `InstitutionEvidenceTest` and `SeoPhase2Test` passed: 7 tests, 61 assertions. SEO rules were not changed.

## Next step

An administrator should open `/admin/source-review` with the school and missing-phone filters, read each contact page, and save a phone proposal only when that page shows the number for that school. Acceptance stays a separate review action.
