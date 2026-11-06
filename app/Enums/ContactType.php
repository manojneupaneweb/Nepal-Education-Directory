<?php

namespace App\Enums;

enum ContactType: string
{
    case Phone = 'phone';
    case Email = 'email';
    case Website = 'website';
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case Youtube = 'youtube';
    case Other = 'other';
}
