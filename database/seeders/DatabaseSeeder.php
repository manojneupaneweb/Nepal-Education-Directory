<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@shikshalink.com'],
            [
                'name' => 'Manoj Neupane',
                'password' => 'password',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'is_admin' => true,
            ],
        );

        $this->call([
            InstitutionClassificationSeeder::class,
            UniversityCatalogSeeder::class,
            CollegeStreamSeeder::class,
        ]);
    }
}
