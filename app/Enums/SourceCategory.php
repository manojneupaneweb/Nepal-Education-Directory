<?php

namespace App\Enums;

enum SourceCategory: string
{
    case OfficialWebsite = 'official_website';
    case GovernmentWebsite = 'government_website';
    case UgcOfficial = 'ugc_official';
    case AffiliatedInstitution = 'affiliated_institution';
    case ThirdParty = 'third_party';
    case NewsMedia = 'news_media';
    case Other = 'other';
}
