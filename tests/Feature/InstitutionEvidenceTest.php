<?php

namespace Tests\Feature;

use App\Enums\VerificationStatus;
use App\Models\Institution;
use App\Models\User;
use App\Services\Institution\InstitutionEvidenceRecorder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InstitutionEvidenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_proposal_stays_pending_until_review_and_does_not_replace_a_stored_value(): void
    {
        $institution = Institution::factory()->create([
            'official_website' => null,
            'verification_status' => VerificationStatus::Unverified,
        ]);
        $recorder = app(InstitutionEvidenceRecorder::class);

        $this->expectException(ValidationException::class);
        try {
            $recorder->propose($institution, 'official_website', 'https://school.example', 'note');
        } finally {
            $this->assertNull($institution->fresh()->official_website);
        }
    }

    public function test_an_accepted_website_is_stored_with_its_source_and_a_second_website_is_refused(): void
    {
        $institution = Institution::factory()->create([
            'official_website' => null,
            'verification_status' => VerificationStatus::Unverified,
        ]);
        $reviewer = User::factory()->create();
        $recorder = app(InstitutionEvidenceRecorder::class);
        $evidence = $recorder->propose(
            $institution,
            'official_website',
            'https://school.example',
            'https://school.example/about',
        );

        $this->assertNull($institution->fresh()->official_website);

        $recorder->accept($evidence, $reviewer);
        $institution->refresh();

        $this->assertSame('https://school.example', $institution->official_website);
        $this->assertSame(VerificationStatus::SourceMatched, $institution->verification_status);
        $this->assertDatabaseHas('institution_sources', [
            'institution_id' => $institution->id,
            'source_url' => 'https://school.example/about',
        ]);
        $this->assertDatabaseHas('institution_change_logs', [
            'institution_id' => $institution->id,
            'field_name' => 'official_website',
        ]);

        $second = $recorder->propose(
            $institution,
            'official_website',
            'https://other.example',
            'https://other.example',
        );

        $this->expectException(ValidationException::class);
        try {
            $recorder->accept($second, $reviewer);
        } finally {
            $this->assertSame('https://school.example', $institution->fresh()->official_website);
            $this->assertSame('pending', $second->fresh()->review_status->value);
        }
    }

    public function test_review_routes_require_an_administrator_and_keep_a_filled_field_pending(): void
    {
        $institution = Institution::factory()->create([
            'official_website' => 'https://school.example',
            'verification_status' => VerificationStatus::Unverified,
        ]);
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->post(route('admin.source-review.store', $institution), [
            'field' => 'phone',
            'value' => '9800000000',
            'source_url' => 'https://school.example/contact',
        ])->assertRedirect(route('login'));

        $this->actingAs($member)->post(route('admin.source-review.store', $institution), [
            'field' => 'phone',
            'value' => '9800000000',
            'source_url' => 'https://school.example/contact',
        ])->assertForbidden();

        $this->actingAs($admin)->post(route('admin.source-review.store', $institution), [
            'field' => 'phone',
            'value' => '9800000000',
            'source_url' => 'https://other.example/contact',
        ])->assertSessionHasErrors('source_url');

        $this->actingAs($admin)->post(route('admin.source-review.store', $institution), [
            'field' => 'phone',
            'value' => '9800000000',
            'source_url' => 'https://school.example/contact',
        ])->assertRedirect(route('admin.source-review.show', $institution));

        $this->actingAs($admin)->post(route('admin.source-review.store', $institution), [
            'field' => 'phone',
            'value' => '9800000000',
            'source_url' => 'https://school.example/contact',
        ])->assertSessionHasErrors('phone');

        $evidence = $institution->fieldEvidence()->first();
        $institution->contacts()->create([
            'contact_type' => 'phone',
            'label' => 'office',
            'contact_value' => '9801111111',
            'is_public' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.source-review.accept', $evidence))
            ->assertSessionHasErrors('phone');
        $this->assertSame('pending', $evidence->fresh()->review_status->value);
        $this->assertSame(VerificationStatus::Unverified, $institution->fresh()->verification_status);

        $institution->contacts()->delete();
        $this->actingAs($admin)->post(route('admin.source-review.accept', $evidence))->assertRedirect();
        $this->assertDatabaseHas('institution_change_logs', [
            'institution_id' => $institution->id,
            'field_name' => 'phone',
        ]);
        $this->assertStringContainsString('https://school.example/contact', (string) $institution->changeLogs()->first()->change_reason);
        $this->assertSame(VerificationStatus::SourceMatched, $institution->fresh()->verification_status);

        $rejected = app(InstitutionEvidenceRecorder::class)->propose(
            $institution->fresh(),
            'email',
            'office@school.example',
            'https://school.example/contact',
        );
        $this->actingAs($admin)->post(route('admin.source-review.reject', $rejected))->assertRedirect();
        $this->assertSame('rejected', $rejected->fresh()->review_status->value);
        $this->assertSame(0, $institution->contacts()->where('contact_type', 'email')->count());
    }
}
