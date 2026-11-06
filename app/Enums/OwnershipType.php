<?php

namespace App\Enums;

enum OwnershipType: string
{
    case Government = 'government';
    case Community = 'community';
    case Private = 'private';
    case Public = 'public';
    case Nonprofit = 'nonprofit';
    case Other = 'other';
    case Unknown = 'unknown';
}
