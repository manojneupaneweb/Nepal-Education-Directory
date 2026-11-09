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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_create_a_draft_with_optional_fields_empty(): void
    {
        $admin = User::factory()->admin()->create();
        $location = $this->location();
        $type = InstitutionType::factory()->create();
        $category = InstitutionCategory::query()->create([
            'name' => 'Synthetic Category',
            'slug' => 'synthetic-category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.institutions.store'), [
            'name' => 'Synthetic Directory Institution',
            'name_ne' => 'सिन्थेटिक',
            'institution_type_id' => $type->id,
            'category_ids' => [$category->id],
            'province_id' => $location['province']->id,
            'district_id' => $location['district']->id,
            'municipality_id' => $location['municipality']->id,
            'official_website' => '',
            'registration_number' => '',
        ]);

        $institution = Institution::query()->where('slug', 'synthetic-directory-institution')->firstOrFail();
        $response->assertRedirect(route('admin.institutions.show', $institution));
        $this->assertSame(InstitutionStatus::Draft, $institution->status);
        $this->assertSame(VerificationStatus::Unverified, $institution->verification_status);
        $this->assertNull($institution->official_website);
        $this->assertNull($institution->registration_number);
        $this->assertTrue($institution->type->is($type));
        $this->assertTrue($institution->categories->contains($category));
        $this->assertSame('सिन्थेटिक', $institution->name_ne);
    }

    public function test_duplicate_names_receive_distinct_slugs_and_edits_keep_the_slug(): void
    {
        $admin = User::factory()->admin()->create();
        $payload = $this->payload('Synthetic Duplicate Institution');

        $this->actingAs($admin)->post(route('admin.institutions.store'), $payload)->assertRedirect();
        $this->actingAs($admin)->post(route('admin.institutions.store'), $payload)->assertRedirect();

        $first = Institution::query()->where('slug', 'synthetic-duplicate-institution')->firstOrFail();
        $second = Institution::query()->where('slug', 'synthetic-duplicate-institution-2')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.institutions.update', $first), array_merge($payload, [
            'name' => 'Synthetic Renamed Institution',
            'slug' => $first->slug,
        ]))->assertRedirect();

        $this->assertSame('synthetic-duplicate-institution', $first->fresh()->slug);
        $this->assertSame('Synthetic Renamed Institution', $first->fresh()->name);
        $this->assertNotNull($second);
    }

    public function test_location_ward_and_parent_cycles_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $location = $this->location();
        $other = $this->location();
        $ward = Ward::factory()->create(['municipality_id' => $other['municipality']->id, 'ward_number' => 4]);
        $payload = $this->payload('Synthetic Location Institution', $location);

        $this->actingAs($admin)->post(route('admin.institutions.store'), array_merge($payload, [
            'district_id' => $other['district']->id,
        ]))->assertSessionHasErrors('municipality_id');

        $this->actingAs($admin)->post(route('admin.institutions.store'), array_merge($payload, [
            'ward_id' => $ward->id,
        ]))->assertSessionHasErrors('municipality_id');

        $parent = Institution::factory()->create();
        $child = Institution::factory()->create(['parent_institution_id' => $parent->id]);

        $this->actingAs($admin)->put(route('admin.institutions.update', $parent), [
            'name' => $parent->name,
            'slug' => $parent->slug,
            'institution_type_id' => $parent->institution_type_id,
            'parent_institution_id' => $child->id,
            'province_id' => $parent->province_id,
            'district_id' => $parent->district_id,
            'municipality_id' => $parent->municipality_id,
        ])->assertSessionHasErrors('parent_institution_id');

        $this->assertNull($parent->fresh()->parent_institution_id);
    }

    public function test_only_admins_can_submit_and_approval_does_not_verify(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $institution = Institution::factory()->create();

        $this->actingAs($member)->post(route('admin.institutions.submit', $institution))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.institutions.submit', $institution))->assertRedirect();
        $this->assertSame(InstitutionStatus::Pending, $institution->fresh()->status);

        $this->actingAs($member)->post(route('admin.institutions.approve', $institution))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.institutions.approve', $institution))->assertRedirect();

        $institution->refresh();
        $this->assertSame(InstitutionStatus::Published, $institution->status);
        $this->assertSame(VerificationStatus::Unverified, $institution->verification_status);
        $this->assertNotNull($institution->published_at);

        $this->actingAs($admin)->post(route('admin.institutions.reject', $institution), [
            'reason' => 'Too late',
        ])->assertSessionHasErrors('status');
    }

    public function test_rejection_archive_and_soft_delete(): void
    {
        $admin = User::factory()->admin()->create();
        $institution = Institution::factory()->create();

        $this->actingAs($admin)->post(route('admin.institutions.submit', $institution));
        $this->actingAs($admin)->post(route('admin.institutions.reject', $institution), [
            'reason' => 'Incomplete synthetic record',
        ])->assertRedirect();

        $this->assertSame(InstitutionStatus::Rejected, $institution->fresh()->status);
        $this->assertSame(
            'Incomplete synthetic record',
            $institution->changeLogs()->where('new_value->status', InstitutionStatus::Rejected->value)->first()->change_reason,
        );

        $this->actingAs($admin)->post(route('admin.institutions.archive', $institution))->assertRedirect();
        $this->assertSame(InstitutionStatus::Archived, $institution->fresh()->status);

        $this->actingAs($admin)->delete(route('admin.institutions.destroy', $institution))->assertRedirect();
        $this->assertSoftDeleted($institution);
    }

    public function test_a_non_admin_cannot_create_an_institution(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.institutions.store'), $this->payload('Blocked Institution'))
            ->assertForbidden();
    }

    public function test_admin_institution_list_can_be_limited_to_one_type(): void
    {
        $admin = User::factory()->admin()->create();
        $school = InstitutionType::factory()->create(['name' => 'School', 'slug' => 'school']);
        $college = InstitutionType::factory()->create(['name' => 'College', 'slug' => 'college']);
        Institution::factory()->create(['name' => 'Synthetic Sidebar School', 'institution_type_id' => $school->id]);
        Institution::factory()->create(['name' => 'Synthetic Sidebar College', 'institution_type_id' => $college->id]);

        $this->actingAs($admin)
            ->get(route('admin.institutions.index', ['type' => 'school']))
            ->assertOk()
            ->assertSee('Synthetic Sidebar School')
            ->assertDontSee('Synthetic Sidebar College');
    }

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
     * @param  array{province: Province, district: District, municipality: Municipality}|null  $location
     * @return array<string, mixed>
     */
    private function payload(string $name, ?array $location = null): array
    {
        $location ??= $this->location();

        return [
            'name' => $name,
            'institution_type_id' => InstitutionType::factory()->create()->id,
            'province_id' => $location['province']->id,
            'district_id' => $location['district']->id,
            'municipality_id' => $location['municipality']->id,
        ];
    }
}
