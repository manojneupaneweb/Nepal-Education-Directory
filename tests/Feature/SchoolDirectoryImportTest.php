<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\Province;
use App\Services\Institution\SchoolDirectoryImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolDirectoryImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_directory_file_creates_a_published_school_and_skips_a_repeat(): void
    {
        $province = Province::factory()->create();
        $district = District::factory()->create([
            'province_id' => $province->id,
            'name' => 'Doti',
        ]);
        $municipality = Municipality::factory()->create([
            'district_id' => $district->id,
            'name' => 'Shikhar Rural Municipality',
        ]);
        InstitutionType::factory()->create(['name' => 'School', 'slug' => 'school']);

        $path = storage_path('framework/testing/schools.jsonl');
        file_put_contents($path, json_encode([
            'name' => 'Shishu Gyan Kunj',
            'slug' => 'shishu-gyan-kunj',
            'district' => 'Doti',
            'municipality' => 'Shikhar Rural Municipality',
            'address' => 'Khanyadi, Shikhar-3, Doti',
            'website' => 'https://example.edu.np',
        ])."\n");

        $importer = app(SchoolDirectoryImporter::class);
        $first = $importer->import($path);
        $second = $importer->import($path);
        $school = Institution::query()->where('name', 'Shishu Gyan Kunj')->first();

        $this->assertSame(1, $first['created']);
        $this->assertSame(0, $second['created']);
        $this->assertNotNull($school);
        $this->assertTrue($school->municipality->is($municipality));
        $this->assertSame('published', $school->status->value);
        $this->assertSame('https://example.edu.np', $school->official_website);
    }
}
