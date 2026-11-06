<?php

namespace App\Models;

use App\Enums\ResearchVerificationStatus;
use Database\Factories\UniversityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'name_ne',
    'abbreviation',
    'alternative_names',
    'slug',
    'university_type',
    'ownership',
    'ugc_category',
    'official_website',
    'description',
    'history',
    'vision',
    'mission',
    'objectives',
    'recognition_status',
    'established_year_bs',
    'established_year_ad',
    'province_id',
    'district_id',
    'municipality_id',
    'location_label',
    'address',
    'postal_address',
    'email',
    'phone',
    'contact_page_url',
    'latitude',
    'longitude',
    'logo_path',
    'social_links',
    'facilities',
    'admission_information',
    'scholarship_information',
    'examination_information',
    'academic_calendar',
    'research_summary',
    'ugc_constituent_colleges',
    'ugc_affiliated_colleges',
    'ugc_community_colleges',
    'ugc_total_colleges',
    'seo_title',
    'meta_description',
    'verification_status',
    'is_aggregate',
    'researched_at',
])]
class University extends Model
{
    /** @use HasFactory<UniversityFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'alternative_names' => 'array',
            'social_links' => 'array',
            'verification_status' => ResearchVerificationStatus::class,
            'is_aggregate' => 'boolean',
            'researched_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(InstitutionAffiliation::class);
    }

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class, 'institution_affiliations')
            ->withPivot([
                'id',
                'affiliation_type',
                'affiliation_number',
                'valid_from',
                'valid_until',
                'status',
                'source_id',
            ])
            ->withTimestamps();
    }

    public function faculties(): HasMany
    {
        return $this->hasMany(UniversityFaculty::class);
    }

    public function leaders(): HasMany
    {
        return $this->hasMany(UniversityLeader::class);
    }

    public function sources(): HasMany
    {
        return $this->hasMany(UniversitySource::class);
    }

    public function researchHistories(): HasMany
    {
        return $this->hasMany(UniversityResearchHistory::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }
}
