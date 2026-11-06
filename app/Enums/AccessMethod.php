<?php

namespace App\Enums;

enum AccessMethod: string
{
    case Manual = 'manual';
    case Http = 'http';
    case Api = 'api';
    case File = 'file';
}
