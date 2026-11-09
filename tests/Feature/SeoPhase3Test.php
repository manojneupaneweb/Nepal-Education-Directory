<?php

namespace Tests\Feature;

use App\Enums\ContactType;
use App\Enums\InstitutionStatus;
use App\Models\Institution;
use App\Models\InstitutionContact;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Services\SEO\InstitutionContentQuality;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoPhase3Test extends TestCase
{
    use RefreshDatabase;

    public function test_location_pages_use_published_counts_and_keep_empty_municipalities_out_of_the_index(): void
    {
        $type = InstitutionType::factory()->create(['slug' => 'school', 'name' => 'School']);
        $school = Institution::factory()->create([
            'institution_type_id' => $type->id,
            'name' => 'Counted School',
            'status' => InstitutionStatus::Published,
            'description' => str_repeat('A published school in this local government. ', 3),
        ]);
        $school->province->update(['is_active' => true, 'name' => 'Counted Province']);
        $school->district->update(['is_active' => true, 'name' => 'Counted District']);
        $school->municipality->update(['is_active' => true, 'name' => 'Counted Municipality']);
        $empty = Municipality::factory()->create([
            'district_id' => $school->district_id,
            'is_active' => true,
            'name' => 'Empty Municipality',
        ]);

        $this->get(route('provinces.show', $school->province))
            ->assertOk()
            ->assertSee('1 published schools')
            ->assertSee('Counted District');

        $this->get(route('districts.show', $school->district))
            ->assertOk()
            ->assertSee('Counted Municipality has the most listed schools')
            ->assertSee('1 schools');

        $this->get(route('municipalities.show', [$school->district, $school->municipality]))
            ->assertOk()
            ->assertSee('1 published schools')
            ->assertSee('Counted School');

        $this->get(route('municipalities.show', [$school->district, $empty]))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, follow"', false)
            ->assertSee('0 published schools');
    }

    public function test_a_school_profile_links_its_municipality_and_a_nearby_school(): void
    {
        $type = InstitutionType::factory()->create(['slug' => 'school', 'name' => 'School']);
        $school = Institution::factory()->create([
            'institution_type_id' => $type->id,
            'name' => 'Anchor School',
            'status' => InstitutionStatus::Published,
            'description' => str_repeat('Anchor school teaches students in the municipality. ', 3),
        ]);
        Institution::factory()->create([
            'institution_type_id' => $type->id,
            'province_id' => $school->province_id,
            'district_id' => $school->district_id,
            'municipality_id' => $school->municipality_id,
            'name' => 'Nearby School',
            'status' => InstitutionStatus::Published,
        ]);

        $this->get(route('institutions.show', $school))
            ->assertOk()
            ->assertSee(route('municipalities.show', [$school->district, $school->municipality]), false)
            ->assertSee('Nearby School')
            ->assertDontSee('>Programs<', false);
    }

    public function test_content_levels_use_stored_fields_and_the_blog_has_no_article_urls(): void
    {
        $basic = Institution::factory()->create([
            'status' => InstitutionStatus::Published,
            'description' => null,
            'official_website' => null,
        ]);
        $contactable = Institution::factory()->create([
            'status' => InstitutionStatus::Published,
            'description' => null,
            'official_website' => null,
        ]);
        InstitutionContact::factory()->create([
            'institution_id' => $contactable->id,
            'contact_type' => ContactType::Phone,
            'contact_value' => '9800000000',
        ]);

        $quality = app(InstitutionContentQuality::class);
        $basic->has_public_contact = false;
        $basic->has_programs = false;
        $basic->has_facilities = false;
        $basic->has_images = false;
        $contactable->has_public_contact = true;
        $contactable->has_programs = false;
        $contactable->has_facilities = false;
        $contactable->has_images = false;

        $this->assertSame(1, $quality->level($basic));
        $this->assertSame(2, $quality->level($contactable));
        $this->get('/blog/education-system-overview-nepal')->assertNotFound();
    }
}
