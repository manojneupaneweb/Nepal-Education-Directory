# Content keyword map

Date: 2026-11-09

Search volume and keyword difficulty are not included. They were not measured.

One page covers each intent. Minor wording changes do not get a second URL.

| Page | URL | Primary intent | Audience | What the page already covers | Do not add |
| --- | --- | --- | --- | --- | --- |
| Homepage | `/` | Find schools, colleges, and universities in Nepal | Students and parents | Directory entry points | A second “education in Nepal” homepage |
| Schools | `/schools` | Schools in Nepal, including filtered province, district, ownership, and type views | Parents | The published school list. Filters stay `noindex` | `/types/school`, which already canonicalizes here |
| Colleges | `/colleges` | Colleges in Nepal and in a province or district | Applicants | The published college list | `/types/college` |
| Universities | `/universities` | Universities in Nepal and their affiliations | Applicants | University profiles and linked colleges | A separate “top universities” ranking page |
| Programs | `/university-programs` | Bachelor’s and master’s programs that are stored | Applicants | Program records and the providing university | Program pages for degrees that are not in the database |
| Institution | `/institutions/{slug}` | One named school or college | Parents and applicants | Stored description, contact, programs, and place | Fees, rankings, or admission dates that are not stored |
| Province | `/provinces/{slug}` | Schools and colleges in that province | Parents | District list and published counts | A copied introduction with only the province name changed |
| District | `/districts/{slug}` | Schools and colleges in that district | Parents | Municipality counts and institution lists | A doorway page for every “schools in {district}” spelling |
| Municipality | `/districts/{district}/local-governments/{municipality}` | Schools and colleges in that local government | Parents | The published list. Empty pages stay `noindex` | A second municipality URL |
| Blog listing | `/blog` | Directory notes | Readers | Excerpt cards from `data/blogs.json` | An article URL until a sourced article body exists |

Internal links that match these intents: province to district, district to municipality, municipality to institution, institution back to municipality and district, college to stored program and university.

The blog file has titles and excerpts only. Several excerpts state rankings, fee ranges, and national school counts without a source in the file. Those claims are not published as articles.
