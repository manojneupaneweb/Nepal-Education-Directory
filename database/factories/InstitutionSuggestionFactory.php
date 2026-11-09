<?php

namespace Database\Factories;

use App\Models\InstitutionSuggestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionSuggestion>
 */
class InstitutionSuggestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'institution_name' => 'Synthetic Suggested School '.fake()->unique()->numerify('####'),
            'institution_type_id' => null,
            'province_id' => null,
            'location' => 'Ward 4, Synthetic Municipality',
            'website' => null,
            'submitter_name' => 'Synthetic Submitter',
            'submitter_email' => 'submitter@example.test',
            'notes' => 'Synthetic suggestion used only by automated tests.',
        ];
    }
}
