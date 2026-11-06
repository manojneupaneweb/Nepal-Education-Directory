<?php

namespace App\Enums;

enum FieldReviewStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}
