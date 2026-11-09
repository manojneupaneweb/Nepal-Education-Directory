<?php

namespace Tests\Feature;

use App\Models\InstitutionSuggestion;
use App\Models\InstitutionType;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionSuggestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_suggestion_form_is_available(): void
    {
        $this->get(route('suggestions.create'))
            ->assertOk()
            ->assertSee('Suggest an institution');
    }

    public function test_visitor_can_submit_an_institution_suggestion(): void
    {
        $type = InstitutionType::factory()->create();
        $province = Province::factory()->create();

        $this->post(route('suggestions.store'), [
            'institution_name' => 'Synthetic Community School',
            'institution_type_id' => $type->id,
            'province_id' => $province->id,
            'location' => 'Synthetic Municipality',
            'website' => 'https://example.test',
            'submitter_name' => 'Synthetic Submitter',
            'submitter_email' => 'submitter@example.test',
            'notes' => 'Missing from the directory.',
        ])->assertRedirect(route('suggestions.create'));

        $this->assertDatabaseHas('institution_suggestions', [
            'institution_name' => 'Synthetic Community School',
            'institution_type_id' => $type->id,
            'province_id' => $province->id,
            'submitter_email' => 'submitter@example.test',
        ]);
    }

    public function test_suggestion_requires_a_name_and_contact(): void
    {
        $this->from(route('suggestions.create'))
            ->post(route('suggestions.store'), [])
            ->assertRedirect(route('suggestions.create'))
            ->assertSessionHasErrors(['institution_name', 'submitter_name', 'submitter_email']);

        $this->assertSame(0, InstitutionSuggestion::query()->count());
    }
}
