<?php

namespace Tests\Feature;

use App\Enums\InstitutionStatus;
use App\Enums\VerificationStatus;
use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionCategory;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\User;
use App\Models\Ward;
use App\Services\Institution\InstitutionService;
use App\Services\Institution\InstitutionSlugger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InstitutionDomainTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Type relationships
    // -------------------------------------------------------------------------

    public function test_institution_belongs_to_its_type(): void
    {
        $type = InstitutionType::factory()->create();
        $institution = Institution::factory()->create(['institution_type_id' => $type->id]);

        $this->assertTrue($institution->type->is($type));
        $this->assertTrue($type->institutions->contains($institution));
    }

    public function test_institution_type_active_scope(): void
    {
        $active = InstitutionType::factory()->create(['is_active' => true]);
        $inactive = InstitutionType::factory()->create(['is_active' => false]);

        $results = InstitutionType::query()->active()->get();

        $this->assertTrue($results->contains($active));
        $this->assertFalse($results->contains($inactive));
    }

    // -------------------------------------------------------------------------
    // Category relationships
    // -------------------------------------------------------------------------

    public function test_institution_category_has_parent_child_relationship(): void
    {
        $parent = InstitutionCategory::factory()->create(['parent_id' => null]);
        $child = InstitutionCategory::factory()->create(['parent_id' => $parent->id]);

        $this->assertTrue($child->parent->is($parent));
        $this->assertTrue($parent->children->contains($child));
    }

    public function test_institution_belongs_to_many_categories(): void
    {
        $institution = Institution::factory()->create();
        $categoryA = InstitutionCategory::factory()->create();
        $categoryB = InstitutionCategory::factory()->create();

        $institution->categories()->sync([$categoryA->id, $categoryB->id]);
        $institution->refresh();

        $this->assertSame(2, $institution->categories->count());
        $this->assertTrue($institution->categories->contains($categoryA));
        $this->assertTrue($institution->categories->contains($categoryB));
    }

    // -------------------------------------------------------------------------
    // Geographic relationships
    // -------------------------------------------------------------------------

    public function test_institution_has_geographic_relationships(): void
    {
        ['province' => $province, 'district' => $district, 'municipality' => $municipality] = $this->location();
        $ward = Ward::factory()->create(['municipality_id' => $municipality->id]);

        $institution = Institution::factory()->create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'municipality_id' => $municipality->id,
            'ward_id' => $ward->id,
        ]);

        $this->assertTrue($institution->province->is($province));
        $this->assertTrue($institution->district->is($district));
        $this->assertTrue($institution->municipality->is($municipality));
        $this->assertTrue($institution->ward->is($ward));
    }

    // -------------------------------------------------------------------------
    // Cross-level location validation via InstitutionService
    // -------------------------------------------------------------------------

    public function test_institution_service_rejects_mismatched_district(): void
    {
        $locA = $this->location();
        $locB = $this->location();

        $attributes = $this->institutionAttributes($locA, [
            'district_id' => $locB['district']->id,
            'municipality_id' => $locB['municipality']->id,
        ]);

        $this->expectException(ValidationException::class);

        try {
            app(InstitutionService::class)->create($attributes, []);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('municipality_id', $e->errors());
            throw $e;
        }
    }

    public function test_institution_service_rejects_mismatched_municipality(): void
    {
        $loc = $this->location();
        $otherMunicipality = Municipality::factory()->create(); // belongs to its own district

        $attributes = $this->institutionAttributes($loc, [
            'municipality_id' => $otherMunicipality->id,
        ]);

        $this->expectException(ValidationException::class);

        try {
            app(InstitutionService::class)->create($attributes, []);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('municipality_id', $e->errors());
            throw $e;
        }
    }

    public function test_institution_service_rejects_mismatched_ward(): void
    {
        $loc = $this->location();
        $otherMunicipality = Municipality::factory()->create();
        $badWard = Ward::factory()->create(['municipality_id' => $otherMunicipality->id]);

        $attributes = $this->institutionAttributes($loc, [
            'ward_id' => $badWard->id,
        ]);

        $this->expectException(ValidationException::class);

        try {
            app(InstitutionService::class)->create($attributes, []);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('municipality_id', $e->errors());
            throw $e;
        }
    }

    // -------------------------------------------------------------------------
    // Circular parent prevention
    // -------------------------------------------------------------------------

    public function test_institution_service_prevents_circular_parent(): void
    {
        $service = app(InstitutionService::class);
        $loc = $this->location();

        $parent = $service->create($this->institutionAttributes($loc, ['name' => 'Parent Institution']), []);
        $child = $service->create($this->institutionAttributes($loc, [
            'name' => 'Child Institution',
            'parent_institution_id' => $parent->id,
        ]), []);

        $this->expectException(ValidationException::class);

        try {
            $service->update($parent, $this->institutionAttributes($loc, [
                'name' => $parent->name,
                'parent_institution_id' => $child->id,
                'slug' => $parent->slug,
            ]), []);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('parent_institution_id', $e->errors());
            throw $e;
        }
    }

    // -------------------------------------------------------------------------
    // Parent-child institution relationship
    // -------------------------------------------------------------------------

    public function test_institution_has_parent_child_relationship(): void
    {
        $parent = Institution::factory()->create();
        $child = Institution::factory()->create(['parent_institution_id' => $parent->id]);

        $this->assertTrue($child->parentInstitution->is($parent));
        $this->assertTrue($parent->branches->contains($child));
    }

    // -------------------------------------------------------------------------
    // Status & verification
    // -------------------------------------------------------------------------

    public function test_institution_created_as_draft_and_unverified(): void
    {
        $loc = $this->location();
        $institution = app(InstitutionService::class)->create($this->institutionAttributes($loc), []);

        $this->assertSame(InstitutionStatus::Draft, $institution->status);
        $this->assertSame(VerificationStatus::Unverified, $institution->verification_status);
    }

    public function test_approval_does_not_change_verification_status(): void
    {
        $admin = User::factory()->admin()->create();
        $service = app(InstitutionService::class);
        $loc = $this->location();

        $institution = $service->create($this->institutionAttributes($loc), []);
        $institution = $service->transition($institution, InstitutionStatus::Pending, $admin);
        $institution = $service->transition($institution, InstitutionStatus::Published, $admin);

        $this->assertSame(InstitutionStatus::Published, $institution->status);
        $this->assertSame(VerificationStatus::Unverified, $institution->verification_status);
    }

    // -------------------------------------------------------------------------
    // Soft deletion
    // -------------------------------------------------------------------------

    public function test_soft_deletion_hides_institution(): void
    {
        $institution = Institution::factory()->create();
        $id = $institution->id;

        $institution->delete();

        $this->assertNull(Institution::query()->find($id));
        $this->assertNotNull(Institution::withTrashed()->find($id));
        $this->assertSoftDeleted($institution);
    }

    // -------------------------------------------------------------------------
    // Nullable optional fields
    // -------------------------------------------------------------------------

    public function test_nullable_optional_fields_are_null_by_default(): void
    {
        $institution = Institution::factory()->create([
            'name_ne' => null,
            'short_name' => null,
            'registration_number' => null,
            'official_website' => null,
            'latitude' => null,
            'longitude' => null,
            'logo_path' => null,
            'address' => null,
            'description' => null,
            'ward_id' => null,
            'parent_institution_id' => null,
            'established_year' => null,
        ]);

        $this->assertNull($institution->registration_number);
        $this->assertNull($institution->official_website);
        $this->assertNull($institution->latitude);
        $this->assertNull($institution->longitude);
        $this->assertNull($institution->logo_path);
        $this->assertNull($institution->name_ne);
        $this->assertNull($institution->short_name);
        $this->assertNull($institution->address);
        $this->assertNull($institution->description);
        $this->assertNull($institution->ward_id);
        $this->assertNull($institution->parent_institution_id);
        $this->assertNull($institution->established_year);
    }

    // -------------------------------------------------------------------------
    // Inactive type/category rejection
    // -------------------------------------------------------------------------

    public function test_inactive_institution_type_is_rejected_by_service(): void
    {
        $inactiveType = InstitutionType::factory()->create(['is_active' => false]);
        $loc = $this->location();

        $attributes = $this->institutionAttributes($loc, [
            'institution_type_id' => $inactiveType->id,
        ]);

        $this->expectException(ValidationException::class);

        try {
            app(InstitutionService::class)->create($attributes, []);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('institution_type_id', $e->errors());
            throw $e;
        }
    }

    public function test_inactive_category_is_rejected_by_service(): void
    {
        $loc = $this->location();
        $inactiveCategory = InstitutionCategory::factory()->create(['is_active' => false]);

        $attributes = $this->institutionAttributes($loc);

        $this->expectException(ValidationException::class);

        try {
            app(InstitutionService::class)->create($attributes, [$inactiveCategory->id]);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('category_ids', $e->errors());
            throw $e;
        }
    }

    // -------------------------------------------------------------------------
    // Slugger
    // -------------------------------------------------------------------------

    public function test_slugger_generates_unique_slugs_for_duplicate_names(): void
    {
        // Persist an institution so the slugger sees 'test-institution' is taken
        Institution::factory()->create(['slug' => 'test-institution']);

        $slugger = app(InstitutionSlugger::class);

        $second = $slugger->generate('Test Institution');
        $this->assertSame('test-institution-2', $second);
    }

    public function test_slug_preserved_on_update_when_not_changed(): void
    {
        $service = app(InstitutionService::class);
        $loc = $this->location();

        $institution = $service->create($this->institutionAttributes($loc, [
            'name' => 'My Slug Institution',
            'slug' => 'my-slug',
        ]), []);

        $this->assertSame('my-slug', $institution->slug);

        $updated = $service->update($institution, $this->institutionAttributes($loc, [
            'name' => $institution->name,
            'slug' => 'my-slug',
        ]), []);

        $this->assertSame('my-slug', $updated->slug);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * @return array{province: Province, district: District, municipality: Municipality}
     */
    private function location(): array
    {
        $province = Province::factory()->create();
        $district = District::factory()->create(['province_id' => $province->id]);
        $municipality = Municipality::factory()->create(['district_id' => $district->id]);

        return compact('province', 'district', 'municipality');
    }

    /**
     * @param  array{province: Province, district: District, municipality: Municipality}  $location
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function institutionAttributes(array $location, array $overrides = []): array
    {
        return array_merge([
            'name' => 'Synthetic Domain Test Institution',
            'institution_type_id' => InstitutionType::factory()->create()->id,
            'province_id' => $location['province']->id,
            'district_id' => $location['district']->id,
            'municipality_id' => $location['municipality']->id,
            'status' => InstitutionStatus::Draft,
            'verification_status' => VerificationStatus::Unverified,
            'is_featured' => false,
        ], $overrides);
    }
}
