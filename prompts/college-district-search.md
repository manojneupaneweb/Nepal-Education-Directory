Search the public web and return only JSON for colleges in this district. No preamble, no refusal, no markdown fence.

Country: Nepal
Province: [PROVINCE_NAME]
District: [CHANGE_DISTRICT_NAME]

Find every higher-education college and campus that operates in this district: government, community, private, constituent, affiliated, medical, engineering, management, education, law, CTEVT higher programs. Skip +2-only schools.

Keep searching past the first page. Use English and Nepali queries, municipality names, university campus lists, UGC, and college websites. If one source is thin, open another. Stop only when a further search adds no new college.

For each college fill these sections from public pages. Use null or [] when a fact is not published. Do not invent phones, fees, reviews, coordinates, or scholarships.

- overview
- courses
- facilities
- gallery (image URLs)
- videos (URLs)
- updates (notices with dates and URLs)
- faqs
- contact
- reviews (only published reviews, with source URL)
- qa

Return this JSON shape:

{
  "research_metadata": {
    "country": "Nepal",
    "province": "[PROVINCE_NAME]",
    "district": "[CHANGE_DISTRICT_NAME]",
    "research_date": "YYYY-MM-DD",
    "total_institutions_discovered": 0,
    "research_status": "completed"
  },
  "institutions": [
    {
      "overview": {
        "name": "",
        "name_ne": "",
        "slug": "",
        "ownership": "",
        "established_year": null,
        "university": "",
        "municipality": "",
        "address": "",
        "description": "",
        "mission": "",
        "vision": "",
        "source_urls": []
      },
      "courses": [
        {"name": "", "level": "", "duration": "", "faculty": "", "fee": null, "source_url": ""}
      ],
      "facilities": [],
      "gallery": [{"url": "", "caption": "", "source_url": ""}],
      "videos": [{"title": "", "url": "", "platform": ""}],
      "updates": [{"title": "", "date": null, "summary": "", "source_url": ""}],
      "faqs": [{"question": "", "answer": ""}],
      "contact": {
        "phones": [],
        "emails": [],
        "website": null,
        "map_url": null
      },
      "reviews": [{"platform": "", "rating": null, "text": "", "source_url": ""}],
      "qa": [{"question": "", "answer": null, "source_url": ""}]
    }
  ]
}
