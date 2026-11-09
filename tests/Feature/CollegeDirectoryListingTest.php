<?php

namespace Tests\Feature;

use App\Enums\InstitutionStatus;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollegeDirectoryListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_college_index_lists_logo_colleges_and_search_includes_colleges_without_a_logo(): void
    {
        $province = Province::factory()->create(['is_active' => true]);
        $type = InstitutionType::factory()->create(['slug' => 'college']);
        $withLogo = Institution::factory()->create([
            'institution_type_id' => $type->id,
            'province_id' => $province->id,
            'name' => 'Logo College',
            'slug' => 'logo-college',
            'logo_path' => 'logos/logo-college.png',
            'status' => InstitutionStatus::Published,
        ]);
        $withoutLogo = Institution::factory()->create([
            'institution_type_id' => $type->id,
            'province_id' => $province->id,
            'name' => 'Plain Campus',
            'slug' => 'plain-campus',
            'logo_path' => null,
            'status' => InstitutionStatus::Published,
        ]);

        $this->get(route('colleges.index'))
            ->assertOk()
            ->assertSee('2 colleges')
            ->assertSee($withLogo->name)
            ->assertDontSee($withoutLogo->name);

        $this->get(route('colleges.index', ['q' => 'Plain']))
            ->assertOk()
            ->assertSee($withoutLogo->name);
    }
}
