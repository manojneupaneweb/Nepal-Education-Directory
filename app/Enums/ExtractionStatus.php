<?php

namespace App\Enums;

enum ExtractionStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Skipped = 'skipped';
}
