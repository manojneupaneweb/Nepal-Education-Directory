<?php

namespace Database\Factories;

use App\Enums\AccessMethod;
use App\Enums\SourceType;
use App\Models\DataSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataSource>
 */
class DataSourceFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'name' => 'Synthetic Source '.$suffix,
            'source_type' => SourceType::Other,
            'base_url' => 'https://example.test/synthetic-source-'.$suffix,
            'authority_level' => 0,
            'access_method' => AccessMethod::Manual,
            'is_active' => true,
            'configuration' => ['synthetic' => true],
        ];
    }
}
