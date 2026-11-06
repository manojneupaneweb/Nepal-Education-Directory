<?php

namespace Database\Factories;

use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Province>
 */
class ProvinceFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'name' => 'Synthetic Province '.$suffix,
            'name_ne' => 'Synthetic Province '.$suffix,
            'slug' => 'synthetic-province-'.$suffix,
            'code' => null,
        ];
    }
}
