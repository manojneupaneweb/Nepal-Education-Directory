<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'province_id' => Province::factory(),
            'name' => 'Synthetic District '.$suffix,
            'name_ne' => 'Synthetic District '.$suffix,
            'slug' => 'synthetic-district-'.$suffix,
            'code' => null,
        ];
    }
}
