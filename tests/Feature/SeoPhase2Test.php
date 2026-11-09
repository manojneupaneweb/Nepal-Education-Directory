<?php

namespace Tests\Feature;

use App\Enums\InstitutionStatus;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\University;
use App\Services\Seo\SeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoPhase2Test extends TestCase
{
    use RefreshDatabase;

    public function test_empty_municipality_pagination_suggestion_and_type_pages_follow_the_index_rules(): void
    {
        $municipality = Municipality::factory()->create(['is_active' => true]);
        $municipality->district->update(['is_active' => true]);
        $municipality->district->province->update(['is_active' => true]);

        $this->get(route('municipalities.show', [$municipality->district, $municipality]))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, follow"', false);

        $this->get(route('schools.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, follow"', false)
            ->assertSee('rel="canonical" href="'.route('schools.index').'?page=2"', false);

        $this->get(route('schools.index'))
            ->assertOk()
            ->assertSee('name="robots" content="index, follow"', false);

        $this->get(route('suggestions.create'))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, follow"', false);

        $schoolType = InstitutionType::factory()->create(['slug' => 'school', 'name' => 'School']);
        $this->get(route('types.show', $schoolType))
            ->assertOk()
            ->assertSee('rel="canonical" href="'.route('schools.index').'"', false)
            ->assertSee('name="robots" content="noindex, follow"', false);

        $university = University::factory()->create();
        $this->get(route('universities.colleges', $university))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, follow"', false);
    }

    public function test_institution_quality_threshold_and_schema_omit_empty_values(): void
    {
        $type = InstitutionType::factory()->create(['slug' => 'school', 'name' => 'School']);
        $thin = Institution::factory()->create([
            'institution_type_id' => $type->id,
            'name' => 'Name Only School',
            'slug' => 'name-only-school',
            'status' => InstitutionStatus::Published,
            'description' => null,
            'official_website' => null,
            'address' => 'Ward 1',
        ]);
        $described = Institution::factory()->create([
            'institution_type_id' => $type->id,
            'name' => 'Described School',
            'slug' => 'described-school',
            'status' => InstitutionStatus::Published,
            'description' => str_repeat('This school teaches local students in the community. ', 3),
            'official_website' => null,
            'logo_path' => null,
        ]);

        $service = app(SeoService::class);
        $this->assertFalse($service->isIndexable($thin));
        $this->assertTrue($service->isIndexable($described));

        $this->get(route('institutions.show', $thin))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, follow"', false);

        $described->load(['municipality', 'district', 'province', 'type', 'contacts', 'programs']);
        $schema = json_encode($service->forInstitution($described)->jsonLd[0]);
        $this->assertStringNotContainsString('null', $schema);
        $this->assertStringNotContainsString('sameAs', $schema);
        $this->assertStringNotContainsString('logo', $schema);
        $this->assertStringContainsString('"@type":"School"', $schema);
    }

    public function test_sitemap_skips_thin_schools_and_drops_deleted_records_after_cache_is_cleared(): void
    {
        $type = InstitutionType::factory()->create(['slug' => 'school', 'name' => 'School']);
        $school = Institution::factory()->create([
            'institution_type_id' => $type->id,
            'name' => 'Cached School',
            'slug' => 'cached-school',
            'status' => InstitutionStatus::Published,
            'description' => str_repeat('A described community school with classes. ', 3),
        ]);
        Institution::factory()->create([
            'institution_type_id' => $type->id,
            'name' => 'Thin School',
            'slug' => 'thin-school',
            'status' => InstitutionStatus::Published,
            'description' => null,
            'official_website' => null,
        ]);

        $this->get(route('sitemap.section', 'schools'))
            ->assertOk()
            ->assertSee('/institutions/cached-school', false)
            ->assertDontSee('/institutions/thin-school');

        $this->assertTrue(Cache::has('seo.sitemap.schools'));
        $school->delete();
        $this->assertFalse(Cache::has('seo.sitemap.schools'));

        $this->get(route('sitemap.section', 'schools'))
            ->assertOk()
            ->assertDontSee('/institutions/cached-school');
    }

    public function test_college_logo_has_an_alt_attribute(): void
    {
        $province = Province::factory()->create(['is_active' => true]);
        $type = InstitutionType::factory()->create(['slug' => 'college', 'name' => 'College']);
        Institution::factory()->create([
            'institution_type_id' => $type->id,
            'province_id' => $province->id,
            'name' => 'Logo College',
            'slug' => 'logo-college',
            'logo_path' => 'images/logo.jpg',
            'status' => InstitutionStatus::Published,
            'description' => str_repeat('A college with published programs and a campus. ', 3),
        ]);

        $this->get(route('colleges.index'))
            ->assertOk()
            ->assertSee('alt="Logo College logo"', false);
    }
}
