# MASTER DEEP RESEARCH PROMPT — COMPLETE SCHOOL DIRECTORY OF NEPAL

## 1. RESEARCH TARGET

Country: Nepal

Province: [CHANGE_PROVINCE_NAME]

District: [CHANGE_DISTRICT_NAME]

Research objective: Discover and collect comprehensive information about ALL schools operating within the specified district.

Conduct extensive research using Google Search, Gemini Deep Research, browser search, webpage browsing, publicly available databases, social media, news, directories, educational portals, PDFs, reports and other available internet sources.

IMPORTANT: Do not restrict research to official websites. Use any publicly available source that provides useful information about a school.

A school does not need to have a verified website to be included.

## 2. SCHOOL CATEGORIES TO DISCOVER

Find every type of school, including:

1. Government schools
2. Community schools
3. Public schools
4. Private schools
5. Institutional schools
6. Boarding schools
7. English-medium schools
8. Montessori schools
9. Primary schools
10. Basic education schools
11. Secondary schools
12. Higher secondary schools
13. Schools offering +2 programs
14. Religious schools
15. Technical and vocational schools
16. Special education schools
17. Inclusive education institutions
18. Other recognized educational institutions

Do not confuse colleges with schools.

If an institution operates both a school and a college, include the school-level information separately and mention its additional educational levels.

## 3. RESEARCH COVERAGE

Research the entire district municipality by municipality.

For each municipality, search:

- Metropolitan cities
- Sub-metropolitan cities
- Municipalities
- Rural municipalities
- Villages
- Wards
- Major towns
- Remote settlements

Do not stop after discovering popular schools.

Search using combinations such as:

- Schools in [DISTRICT]
- Government schools in [DISTRICT]
- Community schools in [DISTRICT]
- Private boarding schools in [DISTRICT]
- Best schools in [DISTRICT]
- Secondary schools in [DISTRICT]
- Basic schools in [DISTRICT]
- English medium schools in [DISTRICT]
- School list [DISTRICT]
- [MUNICIPALITY] school directory
- [MUNICIPALITY] private schools
- [MUNICIPALITY] government schools
- [MUNICIPALITY] boarding schools

Search in English and Nepali.

Use local municipality names, ward names and popular school names to discover additional institutions.

## 4. INFORMATION TO COLLECT FOR EVERY SCHOOL

### A. Basic Identity

- Official school name
- Nepali name
- Alternative names
- Commonly known name
- School category
- Ownership type
- Government/community/private/boarding classification
- Establishment year
- Registration number (if available)
- School status
- Logo URL (if available)
- Logo image source
- Short description
- Detailed description
- Main highlights
- School features
- Why choose this school?

### B. Location

- Province
- District
- Municipality
- Ward number
- Village or locality
- Tole
- Full address
- Latitude
- Longitude
- Google Maps URL
- Nearby landmarks

Do not invent coordinates.

### C. Educational Information

- Education levels
- ECD / Montessori
- Primary level
- Basic level
- Lower secondary
- Secondary level
- Grades offered
- +2 programs
- Academic streams
- Subjects
- Curriculum
- NEB affiliation (if applicable)
- Study system
- Examination system
- Medium of instruction
- Academic calendar
- Teaching methodology

### D. Programs and Classes

For every available program, collect:

- Program name
- Grade or level
- Subject or stream
- Duration
- Curriculum
- Eligibility
- Admission requirements
- Medium of instruction
- Available seats (if published)
- Fees (if published)

### E. Admission Information

- Admission procedure
- Admission requirements
- Age requirements
- Required documents
- Application procedure
- Admission form URL
- Admission notices
- Admission deadlines
- Entrance examination
- Scholarship admission opportunities
- Contact for admission

Clearly distinguish current admission information from historical notices.

### F. Fees and Financial Support

- Admission fees
- Monthly fees
- Annual fees
- Examination fees
- Transportation fees
- Hostel fees
- Other charges
- Scholarship opportunities
- Merit scholarships
- Need-based scholarships
- Government scholarships
- Reserved quota scholarships
- Fee discounts
- Financial assistance

Never invent fee amounts.

### G. Facilities and Infrastructure

Collect all available information about:

- Classrooms
- Smart classrooms
- Science laboratories
- Computer laboratories
- Library
- Digital library
- Internet and Wi-Fi
- Playground
- Sports facilities
- School buses
- Transportation
- Hostel facilities
- Separate hostel facilities
- Cafeteria
- Canteen
- Drinking water
- Health and first aid
- CCTV/security
- Auditorium
- Music and arts rooms
- Robotics and ICT facilities
- Accessibility facilities
- Other infrastructure

### H. Teachers and Administration

- Principal name
- Headteacher name
- Vice-principal
- Chairman
- Founder
- School management committee
- Administrative contacts
- Teachers (only if publicly available)
- Teaching methodology
- Faculty information
- Teacher training and development

### I. School Activities and Student Life

- Extracurricular activities
- Sports
- Music
- Dance
- Arts
- Cultural programs
- Science exhibitions
- Robotics
- Debate competitions
- Quiz competitions
- Educational tours
- Student clubs
- Student organizations
- Community service
- Student leadership
- Annual programs
- Student welfare

### J. Achievements and Recognition

- Academic achievements
- Board examination results
- SEE results
- NEB results
- Awards
- Rankings
- Competitions
- Sports achievements
- Science and technology achievements
- National recognition
- International recognition
- Alumni achievements
- Institutional milestones

Only include achievements supported by available evidence.

### K. Contact Information

- Primary phone
- Secondary phone
- Mobile numbers
- Principal contact (if publicly listed)
- Admission contact
- Official email
- Alternative email
- Full postal address
- Website
- Contact person
- Office hours

### L. Social Media

Find available public social accounts:

- Facebook
- Instagram
- TikTok
- YouTube
- LinkedIn
- X/Twitter
- Other social media

Do not exclude a school if its social media account cannot be confirmed.

### M. Boarding School Information

For boarding schools, additionally research:

- Boys' hostel
- Girls' hostel
- Hostel availability
- Hostel facilities
- Residential supervision
- Meals
- Study hours
- Security
- Warden information
- Transportation
- Accommodation charges
- Hostel admission procedure

### N. Other Information

- School history
- Mission
- Vision
- Educational philosophy
- Message from principal
- Message from chairman
- School environment
- Parent engagement
- Parent-teacher association
- Career guidance
- Counseling
- Alumni network
- Future development plans
- Other useful information

## 5. RESEARCH AND DATA COLLECTION RULES

1. Search broadly across the internet.
2. Official websites are preferred but NOT mandatory.
3. Include schools found through Google Maps, Facebook, directories, news and other public sources.
4. Never exclude a real school simply because it has no website.
5. Do not invent missing information.
6. Use null for unavailable single values.
7. Use empty arrays for unavailable lists.
8. Avoid duplicate schools.
9. Match alternative names to the same institution where appropriate.
10. Make sure each school belongs to the specified district.
11. Include schools with incomplete information.
12. Do not fabricate phone numbers, email addresses, logos or social links.
13. Separate confirmed facts from information that is only reported by secondary sources.
14. Collect source URLs for traceability wherever possible.
15. Do not spend excessive research time trying to verify whether every source URL works.
16. Do not make a working website URL mandatory for including a school.
17. If a logo is unavailable, use null.
18. Do not generate fake school logos or fake institutional photographs.

## 6. REQUIRED JSON OUTPUT FORMAT

Return valid JSON only. No Markdown code fences.

{
  "research_metadata": {
    "country": "Nepal",
    "province": "PROVINCE_NAME",
    "district": "DISTRICT_NAME",
    "research_date": "YYYY-MM-DD",
    "total_schools_discovered": 0,
    "municipalities_covered": [],
    "research_status": "completed_or_partial",
    "coverage_notes": ""
  },
  "schools": [
    {
      "identity": {
        "name": "",
        "name_ne": "",
        "alternative_names": [],
        "slug": "",
        "category": "school",
        "school_type": "government/community/private/boarding/other",
        "ownership": "",
        "established_year": null,
        "registration_number": null,
        "status": "active/closed/unknown",
        "logo_url": null,
        "short_description": "",
        "description": ""
      },
      "location": {
        "province": "",
        "district": "",
        "municipality": "",
        "ward": null,
        "village": "",
        "tole": "",
        "full_address": "",
        "latitude": null,
        "longitude": null,
        "google_maps_url": null
      },
      "academics": {
        "education_levels": [],
        "grades_offered": [],
        "programs": [],
        "curriculum": [],
        "study_system": "",
        "examination_system": "",
        "medium_of_instruction": [],
        "academic_calendar": "",
        "teaching_methodology": ""
      },
      "admission": {
        "requirements": [],
        "admission_process": [],
        "required_documents": [],
        "entrance_exam": null,
        "application_url": null,
        "admission_contact": [],
        "notices": []
      },
      "fees_and_scholarships": {
        "fee_details": [],
        "scholarships": [],
        "financial_support": []
      },
      "facilities": [],
      "infrastructure": [],
      "boarding": {
        "has_hostel": null,
        "boys_hostel": null,
        "girls_hostel": null,
        "accommodation": [],
        "meals": null,
        "security": null,
        "warden_information": null,
        "hostel_fees": null
      },
      "administration": {
        "principal": null,
        "headteacher": null,
        "vice_principal": null,
        "chairman": null,
        "founder": null,
        "teachers": []
      },
      "institutional_profile": {
        "mission": null,
        "vision": null,
        "highlights": [],
        "features": [],
        "why_choose_this_school": [],
        "achievements": [],
        "milestones": []
      },
      "student_life": {
        "extracurricular_activities": [],
        "sports": [],
        "clubs": [],
        "cultural_activities": [],
        "student_welfare": [],
        "campus_experience": null
      },
      "contact": {
        "phones": [],
        "emails": [],
        "website": null,
        "contact_person": null,
        "office_hours": null,
        "full_address": ""
      },
      "social_links": {
        "facebook": null,
        "instagram": null,
        "tiktok": null,
        "youtube": null,
        "linkedin": null,
        "x": null,
        "other": []
      },
      "seo": {
        "suggested_title": "",
        "meta_description": "",
        "keywords": [],
        "faq": []
      },
      "research_sources": [
        {
          "source_name": "",
          "source_url": "",
          "information_obtained": "",
          "source_type": "official/secondary/social_media/directory/news"
        }
      ],
      "verification": {
        "status": "verified/partially_verified/unverified",
        "missing_information": [],
        "notes": []
      }
    }
  ],
  "district_summary": {
    "total_schools": 0,
    "government_schools": 0,
    "community_schools": 0,
    "private_schools": 0,
    "boarding_schools": 0,
    "other_schools": 0,
    "schools_by_municipality": [],
    "schools_by_education_level": [],
    "research_gaps": []
  }
}

## 7. RESEARCH EXECUTION PROCESS

Follow these stages:

STAGE 1: Identify every municipality and rural municipality in the district.

STAGE 2: Discover schools across all municipalities using multiple search queries.

STAGE 3: Create a master list of discovered schools and remove duplicates.

STAGE 4: Research every school individually and collect maximum available information.

STAGE 5: Research school social accounts, Google Maps listings, news articles, directories and other sources.

STAGE 6: Organize all collected information in the required JSON structure.

STAGE 7: Calculate district-level totals and municipality-wise coverage.

STAGE 8: Identify research gaps and schools needing additional investigation.

## 8. OUTPUT BATCHING

If the district contains too many schools for one response:

- Produce JSON in manageable batches.
- Keep every institution as a complete JSON object.
- Never cut an object in the middle.
- Use stable unique identifiers.
- Keep the same schema across all batches.
- Include batch number and continuation information.
- Do not repeat schools from previous batches.
- Do not claim that the district is complete until all identified municipalities have been researched.

FINAL OBJECTIVE:

Create the most comprehensive possible directory of government, community, private and boarding schools in the specified district of Nepal.

The data will be imported into Shiksha Link, a Laravel-based educational directory platform.

Prioritize school discovery, maximum useful information, district-wide coverage, accurate data and structured JSON.

Remember: A school with no website, no social media, or incomplete information must still be included if there is credible evidence that it exists.
