<?php

namespace Database\Factories;

use App\Models\DataSource;
use App\Models\Institution;
use App\Models\InstitutionSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionSource>
 */
class InstitutionSourceFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('######');

        return [
            'institution_id' => Institution::factory(),
            'data_source_id' => DataSource::factory(),
            'source_url' => 'https://example.test/synthetic-page-'.$suffix,
            'source_identifier' => 'synthetic-'.$suffix,
        ];
    }
}
