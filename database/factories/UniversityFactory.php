<?php

namespace Database\Factories;

use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<University>
 */
class UniversityFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'name' => 'Synthetic University '.$suffix,
            'slug' => 'synthetic-university-'.$suffix,
            'university_type' => 'synthetic',
            'description' => 'Synthetic university used only by automated tests.',
        ];
    }
}
