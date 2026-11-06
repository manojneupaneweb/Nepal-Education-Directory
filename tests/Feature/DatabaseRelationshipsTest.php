<?php

namespace Tests\Feature;

use App\Enums\AffiliationStatus;
use App\Enums\AffiliationType;
use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionAffiliation;
use App\Models\InstitutionContact;
use App\Models\InstitutionFieldEvidence;
use App\Models\InstitutionSource;
use App\Models\InstitutionVerification;
use App\Models\Municipality;
use App\Models\Program;
use App\Models\Province;
use App\Models\University;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_province_has_many_districts(): void
    {
        $province = Province::factory()->create();
        $district = District::factory()->create(['province_id' => $province->id]);

        $this->assertTrue($province->districts->contains($district));
        $this->assertTrue($district->province->is($province));
    }

    public function test_a_district_has_many_municipalities(): void
    {
        $district = District::factory()->create();
        $municipality = Municipality::factory()->create(['district_id' => $district->id]);

        $this->assertTrue($district->municipalities->contains($municipality));
        $this->assertTrue($municipality->district->is($district));
    }

    public function test_a_municipality_has_many_institutions(): void
    {
        $institution = Institution::factory()->create();

        $this->assertTrue($institution->municipality->institutions->contains($institution));
        $this->assertTrue($institution->district->province->is($institution->province));
    }

    public function test_an_institution_belongs_to_a_type(): void
    {
        $institution = Institution::factory()->create();

        $this->assertTrue($institution->type->institutions->contains($institution));
    }

    public function test_an_institution_can_have_branches(): void
    {
        $parent = Institution::factory()->create();
        $branch = Institution::factory()->create([
            'parent_institution_id' => $parent->id,
        ]);

        $this->assertTrue($parent->branches->contains($branch));
        $this->assertTrue($branch->parentInstitution->is($parent));
    }

    public function test_an_institution_can_have_multiple_contacts(): void
    {
        $institution = Institution::factory()->create();
        InstitutionContact::factory()->count(2)->create([
            'institution_id' => $institution->id,
        ]);

        $this->assertCount(2, $institution->contacts);
    }

    public function test_an_institution_can_offer_many_programs(): void
    {
        $institution = Institution::factory()->create();
        $program = Program::factory()->create();
        $institution->programs()->attach($program->id, ['is_active' => true]);

        $this->assertTrue($institution->programs->contains($program));
        $this->assertTrue($program->category->programs->contains($program));
        $this->assertTrue($program->educationLevel->programs->contains($program));
    }

    public function test_an_institution_can_record_a_university_affiliation(): void
    {
        $institution = Institution::factory()->create();
        $university = University::factory()->create();

        InstitutionAffiliation::query()->create([
            'institution_id' => $institution->id,
            'university_id' => $university->id,
            'affiliation_type' => AffiliationType::Affiliated,
            'status' => AffiliationStatus::Active,
        ]);

        $this->assertTrue($university->institutions->contains($institution));
        $this->assertSame(AffiliationType::Affiliated, $institution->affiliations->first()->affiliation_type);
    }

    public function test_an_institution_can_have_multiple_sources(): void
    {
        $institution = Institution::factory()->create();
        InstitutionSource::factory()->count(2)->create([
            'institution_id' => $institution->id,
        ]);

        $this->assertCount(2, $institution->institutionSources);
        $this->assertCount(2, $institution->dataSources);
    }

    public function test_field_evidence_is_stored_per_field(): void
    {
        $source = InstitutionSource::factory()->create();
        $evidence = InstitutionFieldEvidence::factory()->create([
            'institution_id' => $source->institution_id,
            'institution_source_id' => $source->id,
            'field_name' => 'name',
            'extracted_value' => 'Synthetic Test Institution',
        ]);

        $this->assertTrue($source->institution->fieldEvidence->contains($evidence));
        $this->assertTrue($evidence->institutionSource->is($source));
    }

    public function test_verification_history_is_kept(): void
    {
        $institution = Institution::factory()->create();
        InstitutionVerification::factory()->count(2)->create([
            'institution_id' => $institution->id,
        ]);

        $this->assertCount(2, $institution->verifications);
    }

    public function test_institution_slugs_are_unique(): void
    {
        Institution::factory()->create(['slug' => 'synthetic-shared-slug']);

        $this->expectException(QueryException::class);

        Institution::factory()->create(['slug' => 'synthetic-shared-slug']);
    }

    public function test_districts_require_a_real_province(): void
    {
        $this->expectException(QueryException::class);

        District::query()->create([
            'province_id' => 999999,
            'name' => 'Synthetic District Missing Province',
            'name_ne' => 'Synthetic District Missing Province',
            'slug' => 'synthetic-district-missing-province',
        ]);
    }

    public function test_institutions_use_soft_deletes(): void
    {
        $institution = Institution::factory()->create();
        $institution->delete();

        $this->assertSoftDeleted($institution);
        $this->assertNull(Institution::query()->find($institution->id));
        $this->assertNotNull(Institution::withTrashed()->find($institution->id));
    }
}
