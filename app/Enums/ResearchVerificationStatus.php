<?php

namespace App\Enums;

enum ResearchVerificationStatus: string
{
    case Verified = 'verified';
    case PartiallyVerified = 'partially_verified';
    case Unverified = 'unverified';
    case Conflicting = 'conflicting';
    case Outdated = 'outdated';
}
