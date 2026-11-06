<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Province;
use Illuminate\Http\JsonResponse;

class GeographyController extends Controller
{
    public function provinces(): JsonResponse
    {
        return response()->json([
            'data' => Province::query()->active()->orderBy('code')->get(['id', 'name', 'name_ne', 'slug', 'code']),
        ]);
    }

    public function districts(Province $province): JsonResponse
    {
        abort_unless($province->is_active, 404);

        return response()->json([
            'data' => $province->districts()->active()->orderBy('name')->get(['id', 'name', 'name_ne', 'slug', 'code', 'province_id']),
        ]);
    }

    public function municipalities(District $district): JsonResponse
    {
        abort_unless($district->is_active, 404);

        return response()->json([
            'data' => $district->municipalities()->active()->orderBy('name')->get([
                'id',
                'name',
                'name_ne',
                'slug',
                'code',
                'district_id',
                'municipality_type',
            ]),
        ]);
    }

    public function wards(Municipality $municipality): JsonResponse
    {
        abort_unless($municipality->is_active, 404);

        return response()->json([
            'data' => $municipality->wards()->active()->orderBy('ward_number')->get([
                'id',
                'ward_number',
                'name',
                'name_ne',
                'code',
                'municipality_id',
            ]),
        ]);
    }
}
