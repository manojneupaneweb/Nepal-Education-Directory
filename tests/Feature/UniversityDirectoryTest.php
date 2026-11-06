<?php

namespace Tests\Feature;

use App\Enums\InstitutionStatus;
use App\Enums\ResearchVerificationStatus;
use App\Models\Institution;
use App\Models\Program;
use App\Models\University;
use App\Models\UniversityFaculty;
use App\Services\Universities\UniversityCatalogImporter;
use App\Services\Universities\UniversityResearcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UniversityDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_directory_lists_imported_universities_without_duplicates(): void
    {
        $counts = app(UniversityCatalogImporter::class)->import();
        app(UniversityCatalogImporter::class)->import();

        $this->assertSame(29, University::query()->count());
        $this->assertSame(29, $counts['created']);
        $this->assertDatabaseHas('universities', [
            'slug' => 'tribhuvan-university',
            'official_website' => 'https://tu.edu.np/',
        ]);
        $this->assertDatabaseMissing('universities', [
            'slug' => 'foreign-university-affiliated-higher-education-institutions',
        ]);

        $this->get(route('universities.index', ['q' => 'Tribhuvan']))
            ->assertOk()
            ->assertSee('Tribhuvan University');

        $this->get(route('universities.show', 'tribhuvan-university'))
            ->assertOk()
            ->assertSee('Visit Official Website')
            ->assertSee('https://tu.edu.np/', false);

        $this->get(route('university-programs.index'))->assertOk();
        $this->get(route('sitemap'))->assertOk();
    }

    public function test_university_colleges_page_lists_linked_colleges(): void
    {
        $university = University::factory()->create([
            'name' => 'Synthetic Campus University',
            'slug' => 'synthetic-campus-university',
        ]);
        $college = Institution::factory()->create([
            'name' => 'Synthetic Affiliated College',
            'status' => InstitutionStatus::Published,
        ]);
        $university->institutions()->attach($college->id, [
            'affiliation_type' => 'affiliated',
            'status' => 'pending',
        ]);

        $this->get(route('universities.show', $university))
            ->assertOk()
            ->assertSee('View campuses and colleges')
            ->assertSee(route('universities.colleges', $university), false);

        $this->get(route('universities.colleges', $university))
            ->assertOk()
            ->assertSee('Synthetic Affiliated College');
    }

    public function test_research_fills_only_empty_contact_fields_from_the_official_page(): void
    {
        $university = University::factory()->create([
            'name' => 'Synthetic Research University',
            'slug' => 'synthetic-research-university',
            'official_website' => 'https://example.test',
            'email' => 'kept@example.test',
            'description' => 'Existing description that must stay.',
        ]);

        Http::fake([
            'https://example.test' => Http::response('<html><body>Contact office@example.test phone 01-5555555</body></html>', 200),
        ]);

        app(UniversityResearcher::class)->research($university, applyEmpty: true);

        $university->refresh();
        $this->assertSame('kept@example.test', $university->email);
        $this->assertSame('Existing description that must stay.', $university->description);
        $this->assertSame('01-5555555', $university->phone);
        $this->assertDatabaseHas('university_research_histories', [
            'university_id' => $university->id,
            'approval_status' => 'approved',
        ]);
    }

    public function test_program_index_filters_by_university_and_faculty(): void
    {
        $first = University::factory()->create(['name' => 'Filter Alpha University']);
        $second = University::factory()->create(['name' => 'Filter Beta University']);
        $engineering = UniversityFaculty::query()->create([
            'university_id' => $first->id,
            'name' => 'Filter Engineering',
            'slug' => 'filter-engineering',
            'verification_status' => ResearchVerificationStatus::PartiallyVerified,
        ]);
        $law = UniversityFaculty::query()->create([
            'university_id' => $first->id,
            'name' => 'Filter Law',
            'slug' => 'filter-law',
            'verification_status' => ResearchVerificationStatus::PartiallyVerified,
        ]);
        $other = UniversityFaculty::query()->create([
            'university_id' => $second->id,
            'name' => 'Filter Medicine',
            'slug' => 'filter-medicine',
            'verification_status' => ResearchVerificationStatus::PartiallyVerified,
        ]);

        Program::factory()->create([
            'university_id' => $first->id,
            'university_faculty_id' => $engineering->id,
            'name' => 'Filter Civil Degree',
            'slug' => 'filter-civil-degree',
        ]);
        Program::factory()->create([
            'university_id' => $first->id,
            'university_faculty_id' => $law->id,
            'name' => 'Filter Law Degree',
            'slug' => 'filter-law-degree',
        ]);
        Program::factory()->create([
            'university_id' => $second->id,
            'university_faculty_id' => $other->id,
            'name' => 'Filter Medicine Degree',
            'slug' => 'filter-medicine-degree',
        ]);

        $this->get(route('university-programs.index', ['university' => $first->id]))
            ->assertOk()
            ->assertSee('Filter Civil Degree')
            ->assertSee('Filter Law Degree')
            ->assertDontSee('Filter Medicine Degree')
            ->assertSee('Filter Engineering')
            ->assertDontSee('Filter Medicine');

        $this->get(route('university-programs.index', [
            'university' => $first->id,
            'faculty' => $engineering->id,
        ]))
            ->assertOk()
            ->assertSee('Filter Civil Degree')
            ->assertDontSee('Filter Law Degree');

        $this->get(route('university-programs.index', [
            'university' => $first->id,
            'faculty' => $other->id,
        ]))
            ->assertOk()
            ->assertSee('Filter Civil Degree')
            ->assertSee('Filter Law Degree');
    }

    public function test_program_page_requires_a_university(): void
    {
        $program = Program::factory()->create([
            'name' => 'Synthetic School Program',
            'slug' => 'synthetic-school-program',
        ]);

        $this->get(route('university-programs.show', $program))->assertNotFound();
    }
}
