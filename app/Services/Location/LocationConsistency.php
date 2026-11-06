<?php

namespace App\Services\Location;

use App\Models\Institution;

class LocationConsistency
{
    /**
     * @return list<string>
     */
    public function violations(Institution $institution): array
    {
        $institution->loadMissing(['district', 'municipality.district', 'ward']);
        $violations = [];

        if ($institution->district !== null && $institution->district->province_id !== $institution->province_id) {
            $violations[] = 'The district does not belong to the selected province.';
        }

        if ($institution->municipality !== null && $institution->municipality->district_id !== $institution->district_id) {
            $violations[] = 'The municipality does not belong to the selected district.';
        }

        if ($institution->ward !== null && $institution->ward->municipality_id !== $institution->municipality_id) {
            $violations[] = 'The ward does not belong to the selected municipality.';
        }

        return $violations;
    }
}
