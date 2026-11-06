<?php

namespace App\Enums;

enum AffiliationStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Expired = 'expired';
    case Pending = 'pending';
}
