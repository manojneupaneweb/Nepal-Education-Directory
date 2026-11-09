<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\Province;
use App\Services\Institution\DistrictSchoolResearchImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DistrictSchoolResearchImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_district_research_file_creates_a_published_school_and_skips_a_repeat(): void
    {
        $province = Province::factory()->create();
        $district = District::factory()->create([
            'province_id' => $province->id,
            'name' => 'Kathmandu',
        ]);
        $municipality = Municipality::factory()->create([
            'district_id' => $district->id,
            'name' => 'Budhanilkantha Urban Municipality',
        ]);
        InstitutionType::factory()->create(['name' => 'School', 'slug' => 'school']);

        $directory = storage_path('framework/testing/school-research');
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        file_put_contents($directory.'/kathmandu.json', json_encode([
            'research_metadata' => ['district' => 'Kathmandu'],
            'schools' => [[
                'identity' => [
                    'name' => 'Budhanilkantha School',
                    'name_ne' => 'बुढानिलकण्ठ स्कुल',
                    'description' => 'A boarding school in Budhanilkantha.',
                    'established_year' => 1972,
                ],
                'location' => [
                    'municipality' => 'Budhanilkantha Municipality',
                    'full_address' => 'Budhanilkantha, Kathmandu, Nepal',
                ],
                'academics' => [
                    'programs' => [['program_name' => 'NEB Grade 11 & 12 Science']],
                ],
                'contact' => [
                    'phones' => ['+977-1-4370165'],
                    'emails' => ['info@bnks.edu.np'],
                    'website' => 'https://www.bnks.edu.np',
                ],
            ]],
        ]));

        $importer = app(DistrictSchoolResearchImporter::class);
        $first = $importer->importDirectory($directory);
        $second = $importer->importDirectory($directory);

        $school = Institution::query()->where('name', 'Budhanilkantha School')->first();

        $this->assertSame(1, $first['created']);
        $this->assertSame(0, $second['created']);
        $this->assertNotNull($school);
        $this->assertTrue($school->district->is($district));
        $this->assertTrue($school->municipality->is($municipality));
        $this->assertSame('published', $school->status->value);
        $this->assertSame('https://www.bnks.edu.np', $school->official_website);
        $this->assertSame(['NEB Grade 11 & 12 Science'], $school->website_sections['programs']);
    }
}
