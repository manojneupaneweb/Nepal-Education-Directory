<?php

namespace App\Enums;

enum MunicipalityType: string
{
    case Metropolitan = 'metropolitan';
    case SubMetropolitan = 'sub_metropolitan';
    case Municipality = 'municipality';
    case RuralMunicipality = 'rural_municipality';
}
