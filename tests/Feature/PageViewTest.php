<?php

namespace Tests\Feature;

use App\Enums\InstitutionStatus;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\PageView;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_count_views_and_only_admin_shows_them(): void
    {
        $schoolType = InstitutionType::factory()->create(['name' => 'School', 'slug' => 'school']);
        $school = Institution::factory()->create([
            'name' => 'Synthetic Viewed School',
            'slug' => 'synthetic-viewed-school',
            'institution_type_id' => $schoolType->id,
            'status' => InstitutionStatus::Published,
        ]);
        $university = University::factory()->create([
            'name' => 'Synthetic Viewed University',
            'slug' => 'synthetic-viewed-university',
        ]);

        $this->get(route('institutions.show', $school))->assertOk();
        $this->get(route('universities.show', $university))->assertOk();

        $this->assertSame(1, PageView::query()->where('kind', 'school')->count());
        $this->assertSame(1, PageView::query()->where('kind', 'university')->count());

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('School views')
            ->assertDontSee('University views');

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('School views')
            ->assertSee('College views')
            ->assertSee('University views');
    }
}
