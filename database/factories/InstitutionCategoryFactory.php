<?php

namespace Database\Factories;

use App\Models\InstitutionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionCategory>
 */
class InstitutionCategoryFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'name' => 'Synthetic Category '.$suffix,
            'name_ne' => null,
            'slug' => 'synthetic-category-'.$suffix,
            'description' => null,
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
