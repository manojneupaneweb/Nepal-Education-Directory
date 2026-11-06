<?php

namespace Database\Factories;

use App\Models\Municipality;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ward>
 */
class WardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'municipality_id' => Municipality::factory(),
            'ward_number' => fake()->unique()->numberBetween(1, 30),
            'name' => null,
            'name_ne' => null,
        ];
    }
}
