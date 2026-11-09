<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\Province;
use App\Services\Institution\DistrictCollegeResearchImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DistrictCollegeResearchImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_district_research_file_creates_a_published_college_and_skips_a_repeat(): void
    {
        $province = Province::factory()->create();
        $district = District::factory()->create([
            'province_id' => $province->id,
            'name' => 'Taplejung',
        ]);
        $municipality = Municipality::factory()->create([
            'district_id' => $district->id,
            'name' => 'Phungling Urban Municipality',
        ]);
        InstitutionType::factory()->create(['name' => 'College', 'slug' => 'college']);

        $directory = storage_path('framework/testing/college-research');
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        file_put_contents($directory.'/taplejung.json', json_encode([
            'research_metadata' => ['district' => 'Taplejung'],
            'institutions' => [[
                'overview' => [
                    'name' => 'Pathibhara Multiple Campus',
                    'name_ne' => 'पाथीभरा बहुमुखी क्याम्पस',
                    'ownership' => 'community',
                    'municipality' => 'Phungling Municipality',
                    'description' => 'A community campus affiliated to Tribhuvan University in Taplejung.',
                    'source_urls' => ['https://pathivaracampus.edu.np/'],
                ],
                'courses' => [['name' => 'Bachelor of Education (B.Ed.)']],
                'contact' => ['phones' => ['021-123456'], 'website' => 'https://pathivaracampus.edu.np/'],
            ]],
        ]));

        $importer = app(DistrictCollegeResearchImporter::class);
        $first = $importer->importDirectory($directory);
        $second = $importer->importDirectory($directory);

        $college = Institution::query()->where('name', 'Pathibhara Multiple Campus')->first();

        $this->assertSame(1, $first['created']);
        $this->assertSame(0, $second['created']);
        $this->assertNotNull($college);
        $this->assertTrue($college->district->is($district));
        $this->assertTrue($college->municipality->is($municipality));
        $this->assertSame('published', $college->status->value);
        $this->assertSame('https://pathivaracampus.edu.np/', $college->official_website);
        $this->assertSame(['Bachelor of Education (B.Ed.)'], $college->website_sections['programs']);
    }
}
