<?php

namespace Tests\Feature;

use App\Enums\InstitutionStatus;
use App\Models\Institution;
use App\Models\InstitutionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_have_unique_metadata_and_private_pages_stay_out_of_the_index(): void
    {
        $type = InstitutionType::factory()->create(['slug' => 'school', 'name' => 'School']);
        $school = Institution::factory()->create([
            'institution_type_id' => $type->id,
            'name' => 'Budhanilkantha School',
            'slug' => 'budhanilkantha-school',
            'status' => InstitutionStatus::Published,
            'address' => 'Budhanilkantha, Kathmandu',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('name="robots" content="index, follow"', false)
            ->assertSee('WebSite', false);

        $this->get(route('search'))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, follow"', false);

        $this->get(route('schools.index', ['q' => 'Budhanilkantha']))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, follow"', false);

        $this->get(route('institutions.show', $school))
            ->assertOk()
            ->assertSee('Budhanilkantha School', false)
            ->assertSee('"@type":"School"', false)
            ->assertSee(route('institutions.show', $school), false);

        $this->get('/institutions/missing-school')->assertNotFound();

        $this->get(route('robots'))
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap:');

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee('<sitemapindex', false)
            ->assertSee('/sitemaps/schools.xml', false)
            ->assertDontSee('/search');
    }
}
