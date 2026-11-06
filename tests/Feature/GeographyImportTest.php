<?php

namespace Tests\Feature;

use App\Enums\MunicipalityType;
use App\Models\District;
use App\Models\Institution;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Ward;
use App\Services\Location\GeographyImporter;
use App\Services\Location\GeographyNormalizer;
use App\Services\Location\LocationConsistency;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeographyImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixture_import_is_idempotent_and_keeps_unicode_names(): void
    {
        $importer = app(GeographyImporter::class);
        $path = base_path('tests/Fixtures/geography');

        $first = $importer->import($path);
        $second = $importer->import($path);

        $province = Province::query()->where('code', '1')->firstOrFail();
        $district = District::query()->where('code', '101')->firstOrFail();
        $municipality = Municipality::query()->where('code', '10101')->firstOrFail();

        $this->assertSame('सिन्थेटिक', $province->name_ne);
        $this->assertSame('Synthetic District', $district->name);
        $this->assertTrue($district->province->is($province));
        $this->assertTrue($municipality->district->is($district));
        $this->assertSame(MunicipalityType::RuralMunicipality, $municipality->municipality_type);
        $this->assertSame(MunicipalityType::Metropolitan, Municipality::query()->where('code', '10102')->firstOrFail()->municipality_type);
        $this->assertNull(Municipality::query()->where('code', '10103')->first());
        $this->assertSame(1, $first->levels['province']['created']);
        $this->assertSame(1, $second->levels['province']['unchanged']);
        $this->assertSame(2, Ward::query()->count());
        $this->assertNotEmpty($first->invalid);
        $this->assertNotEmpty($first->missingRelationships);
        $this->assertNotEmpty($first->manualReview);
    }

    public function test_dry_run_does_not_keep_records(): void
    {
        $this->artisan('geography:import', [
            '--path' => base_path('tests/Fixtures/geography'),
            '--dry-run' => true,
            '--report' => true,
        ])->assertFailed();

        $this->assertSame(0, Province::query()->count());
        $this->assertNotEmpty(glob(storage_path('app/geography/import-*.json')));
    }

    public function test_validate_only_does_not_write(): void
    {
        $report = app(GeographyImporter::class)->validate(base_path('tests/Fixtures/geography'));

        $this->assertTrue($report->validateOnly);
        $this->assertSame(0, Province::query()->count());
        $this->assertSame(3, $report->levels['municipality']['discovered']);
    }

    public function test_reimport_preserves_an_institution_foreign_key_and_existing_nepali_name(): void
    {
        $importer = app(GeographyImporter::class);
        $importer->import(base_path('tests/Fixtures/geography'));
        $municipality = Municipality::query()->where('code', '10101')->firstOrFail();
        $institution = Institution::factory()->create([
            'province_id' => $municipality->district->province_id,
            'district_id' => $municipality->district_id,
            'municipality_id' => $municipality->id,
        ]);
        $directory = storage_path('framework/testing/geography-empty-nepali');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        foreach (['manifest.json', 'districts.csv', 'local-units.csv', 'wards.csv'] as $file) {
            copy(base_path('tests/Fixtures/geography/'.$file), $directory.'/'.$file);
        }

        file_put_contents($directory.'/provinces.csv', "code,name_en,name_ne\n1,Synthetic Province Alpha,\n");
        $province = $municipality->district->province;
        $province->update(['name_ne' => 'राखिएको']);

        $importer->import($directory);

        $this->assertSame($municipality->id, $institution->fresh()->municipality_id);
        $this->assertSame('राखिएको', $province->fresh()->name_ne);
    }

    public function test_ward_numbers_are_unique_inside_a_municipality(): void
    {
        $ward = Ward::factory()->create(['ward_number' => 1]);

        $this->expectException(QueryException::class);

        Ward::factory()->create([
            'municipality_id' => $ward->municipality_id,
            'ward_number' => 1,
        ]);
    }

    public function test_location_consistency_rejects_a_mismatched_province(): void
    {
        $institution = Institution::factory()->create();
        $other = Province::factory()->create();
        $institution->update(['province_id' => $other->id]);

        $violations = app(LocationConsistency::class)->violations($institution->fresh());

        $this->assertNotEmpty($violations);
    }

    public function test_geography_endpoints_return_only_the_active_child_records(): void
    {
        $importer = app(GeographyImporter::class);
        $importer->import(base_path('tests/Fixtures/geography'));
        $province = Province::query()->where('code', '1')->firstOrFail();
        $district = $province->districts()->firstOrFail();
        $inactive = District::factory()->create([
            'province_id' => $province->id,
            'is_active' => false,
        ]);

        $this->getJson(route('api.geography.districts', $province))
            ->assertOk()
            ->assertJsonFragment(['code' => '101'])
            ->assertJsonMissing(['id' => $inactive->id]);

        $this->getJson(route('api.geography.municipalities', $district))
            ->assertOk()
            ->assertJsonFragment(['code' => '10101']);
    }

    public function test_source_spelling_still_distinguishes_sub_metropolitan_units(): void
    {
        $normalizer = app(GeographyNormalizer::class);

        $this->assertSame(
            MunicipalityType::SubMetropolitan,
            $normalizer->municipalityType('Butwal Sub-Metropolitian City'),
        );
        $this->assertSame(
            MunicipalityType::Metropolitan,
            $normalizer->municipalityType('Kathmandu Metropolitian City'),
        );
    }

    public function test_official_dataset_contains_the_published_code_counts_and_unicode(): void
    {
        $localUnits = file_get_contents(database_path('data/geography/nso-geographical-codes-2017/local-units.csv'));
        $districts = file(database_path('data/geography/nso-geographical-codes-2017/districts.csv'));
        $provinces = file(database_path('data/geography/nso-geographical-codes-2017/provinces.csv'));

        $this->assertCount(8, $provinces);
        $this->assertCount(78, $districts);
        $this->assertStringContainsString('फक्ताङ्लुङ्ग गाउँपालिका', $localUnits);
        $this->assertSame(754, count(file(database_path('data/geography/nso-geographical-codes-2017/local-units.csv'))));
    }
}
