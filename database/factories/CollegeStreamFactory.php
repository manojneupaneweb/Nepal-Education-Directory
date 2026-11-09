<?php

namespace Database\Factories;

use App\Models\CollegeStream;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CollegeStream>
 */
class CollegeStreamFactory extends Factory
{
    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('####');

        return [
            'name' => 'Synthetic Stream '.$suffix,
            'slug' => 'synthetic-stream-'.$suffix,
            'colleges_count' => 12,
            'keywords' => ['Synthetic'],
            'icon_path' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
            'color' => '#2563EB',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
