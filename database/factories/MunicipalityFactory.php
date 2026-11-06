<?php

namespace Database\Factories;

use App\Enums\MunicipalityType;
use App\Models\District;
use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Municipality>
 */
class MunicipalityFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'district_id' => District::factory(),
            'name' => 'Synthetic Municipality '.$suffix,
            'name_ne' => 'Synthetic Municipality '.$suffix,
            'slug' => 'synthetic-municipality-'.$suffix,
            'municipality_type' => MunicipalityType::Municipality,
            'code' => null,
        ];
    }
}
