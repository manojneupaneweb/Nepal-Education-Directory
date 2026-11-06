<?php

namespace Database\Factories;

use App\Enums\VerificationStatus;
use App\Models\Institution;
use App\Models\InstitutionVerification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionVerification>
 */
class InstitutionVerificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'verification_type' => 'source_check',
            'verification_status' => VerificationStatus::SourceMatched,
            'evidence' => ['synthetic' => true],
        ];
    }
}
