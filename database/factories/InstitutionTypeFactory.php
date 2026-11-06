<?php

namespace Database\Factories;

use App\Models\InstitutionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionType>
 */
class InstitutionTypeFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'name' => 'Synthetic Institution Type '.$suffix,
            'slug' => 'synthetic-institution-type-'.$suffix,
            'description' => 'Synthetic classification used only by automated tests.',
            'description_ne' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
