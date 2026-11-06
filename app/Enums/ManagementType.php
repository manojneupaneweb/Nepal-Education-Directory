<?php

namespace App\Enums;

enum ManagementType: string
{
    case GovernmentManaged = 'government_managed';
    case CommunityManaged = 'community_managed';
    case PrivatelyManaged = 'privately_managed';
    case TrustManaged = 'trust_managed';
    case Other = 'other';
    case Unknown = 'unknown';
}
