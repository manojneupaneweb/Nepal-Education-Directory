<?php

namespace Database\Factories;

use App\Models\EducationLevel;
use App\Models\Program;
use App\Models\ProgramCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'program_category_id' => ProgramCategory::factory(),
            'education_level_id' => EducationLevel::factory(),
            'name' => 'Synthetic Program '.$suffix,
            'slug' => 'synthetic-program-'.$suffix,
            'description' => 'Synthetic program used only by automated tests.',
        ];
    }
}
