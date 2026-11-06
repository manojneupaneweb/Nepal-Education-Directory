<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case Unverified = 'unverified';
    case SourceMatched = 'source_matched';
    case AdminReviewed = 'admin_reviewed';
    case InstitutionVerified = 'institution_verified';
}
