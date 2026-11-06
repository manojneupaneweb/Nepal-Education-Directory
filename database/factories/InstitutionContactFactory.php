<?php

namespace Database\Factories;

use App\Enums\ContactType;
use App\Models\Institution;
use App\Models\InstitutionContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionContact>
 */
class InstitutionContactFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('######');

        return [
            'institution_id' => Institution::factory(),
            'contact_type' => ContactType::Email,
            'label' => 'Synthetic contact',
            'contact_value' => 'synthetic-'.$suffix.'@example.test',
            'is_primary' => false,
            'is_public' => false,
        ];
    }
}
