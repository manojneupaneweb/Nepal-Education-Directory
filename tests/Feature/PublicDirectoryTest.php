<?php

namespace Tests\Feature;

use App\Enums\InstitutionStatus;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_directory_pages_render(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('Find schools, colleges, and training centers.');
        $this->get(route('types.index'))->assertOk()->assertSee('Institution types');
        $this->get(route('provinces.index'))->assertOk()->assertSee('Provinces');
        $this->get(route('about'))->assertOk();
        $this->get(route('how-it-works'))->assertOk();
        $this->get(route('contact'))->assertOk();
        $this->get(route('search'))->assertOk()->assertSee('No institutions have been published yet.');
    }

    public function test_search_lists_only_published_institutions(): void
    {
        $published = Institution::factory()->create([
            'name' => 'Synthetic Published College',
            'slug' => 'synthetic-published-college',
            'status' => InstitutionStatus::Published,
        ]);
        Institution::factory()->create([
            'name' => 'Synthetic Draft College',
            'slug' => 'synthetic-draft-college',
            'status' => InstitutionStatus::Draft,
        ]);

        $this->get(route('search', ['q' => 'Synthetic']))
            ->assertOk()
            ->assertSee('Synthetic Published College')
            ->assertDontSee('Synthetic Draft College');

        $this->get(route('institutions.show', $published))->assertOk();
        $this->get('/institutions/synthetic-draft-college')->assertNotFound();
    }

    public function test_province_and_type_pages_use_real_records(): void
    {
        $province = Province::factory()->create(['name' => 'Synthetic Province Page', 'slug' => 'synthetic-province-page']);
        $type = InstitutionType::factory()->create(['name' => 'Synthetic Type Page', 'slug' => 'synthetic-type-page']);

        $this->get(route('provinces.show', $province))->assertOk()->assertSee('Synthetic Province Page');
        $this->get(route('types.show', $type))->assertOk()->assertSee('No published synthetic type page records yet.');
    }
}
