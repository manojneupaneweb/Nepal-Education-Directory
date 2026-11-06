<?php

namespace App\Enums;

enum ImageType: string
{
    case Logo = 'logo';
    case Campus = 'campus';
    case Classroom = 'classroom';
    case Infrastructure = 'infrastructure';
    case Other = 'other';
}
