<?php

namespace Tests\Feature;

use App\Models\InstitutionType;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUniversityPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_university_filter_opens_a_dedicated_admin_page(): void
    {
        $admin = User::factory()->admin()->create();
        InstitutionType::factory()->create(['name' => 'University', 'slug' => 'university']);
        University::factory()->create(['name' => 'Synthetic Admin University']);

        $this->actingAs($admin)
            ->get(route('admin.institutions.index', ['type' => 'university']))
            ->assertRedirect(route('admin.universities.index'));

        $this->actingAs($admin)
            ->get(route('admin.universities.index'))
            ->assertOk()
            ->assertSee('Synthetic Admin University')
            ->assertSee('separate from school and college records');
    }
}
