<?php

namespace Database\Factories;

use App\Enums\InstitutionStatus;
use App\Enums\VerificationStatus;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Institution>
 */
class InstitutionFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('######');
        $province = Province::factory()->create();
        $district = $province->districts()->create([
            'name' => 'Synthetic District '.$suffix,
            'name_ne' => 'Synthetic District '.$suffix,
            'slug' => 'synthetic-district-'.$suffix,
        ]);
        $municipality = Municipality::factory()->create([
            'district_id' => $district->id,
            'name' => 'Synthetic Municipality '.$suffix,
            'name_ne' => 'Synthetic Municipality '.$suffix,
            'slug' => 'synthetic-municipality-'.$suffix,
        ]);

        return [
            'institution_type_id' => InstitutionType::factory(),
            'name' => 'Synthetic Test Institution '.$suffix,
            'name_ne' => null,
            'slug' => 'synthetic-test-institution-'.$suffix,
            'province_id' => $province->id,
            'district_id' => $district->id,
            'municipality_id' => $municipality->id,
            'status' => InstitutionStatus::Draft,
            'verification_status' => VerificationStatus::Unverified,
            'is_featured' => false,
        ];
    }
}
