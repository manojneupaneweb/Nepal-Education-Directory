<?php

namespace Database\Factories;

use App\Models\ProgramCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramCategory>
 */
class ProgramCategoryFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'name' => 'Synthetic Program Category '.$suffix,
            'slug' => 'synthetic-program-category-'.$suffix,
            'description' => 'Synthetic category used only by automated tests.',
        ];
    }
}
