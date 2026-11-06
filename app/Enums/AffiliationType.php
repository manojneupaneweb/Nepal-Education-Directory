<?php

namespace App\Enums;

enum AffiliationType: string
{
    case Affiliated = 'affiliated';
    case Constituent = 'constituent';
    case Other = 'other';
}
