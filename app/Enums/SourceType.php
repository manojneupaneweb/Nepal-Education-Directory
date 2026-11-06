<?php

namespace App\Enums;

enum SourceType: string
{
    case Government = 'government';
    case University = 'university';
    case EducationalBoard = 'educational_board';
    case InstitutionWebsite = 'institution_website';
    case Municipality = 'municipality';
    case Other = 'other';
}
