<?php

namespace Database\Factories;

use App\Models\EducationLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EducationLevel>
 */
class EducationLevelFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'name' => 'Synthetic Education Level '.$suffix,
            'slug' => 'synthetic-education-level-'.$suffix,
            'description' => 'Synthetic level used only by automated tests.',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
