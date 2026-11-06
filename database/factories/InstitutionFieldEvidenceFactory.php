<?php

namespace Database\Factories;

use App\Enums\FieldReviewStatus;
use App\Models\Institution;
use App\Models\InstitutionFieldEvidence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionFieldEvidence>
 */
class InstitutionFieldEvidenceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'field_name' => 'official_website',
            'extracted_value' => 'https://example.test/synthetic',
            'normalized_value' => 'https://example.test/synthetic',
            'confidence' => '0.5000',
            'review_status' => FieldReviewStatus::Pending,
        ];
    }
}
