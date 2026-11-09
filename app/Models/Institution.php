<?php

namespace App\Models;

use App\Enums\InstitutionStatus;
use App\Enums\ManagementType;
use App\Enums\OwnershipType;
use App\Enums\VerificationStatus;
use Database\Factories\InstitutionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'parent_institution_id',
    'institution_type_id',
    'name',
    'name_ne',
    'short_name',
    'slug',
    'description',
    'website_sections',
    'description_ne',
    'registration_number',
    'registration_authority',
    'established_year',
    'ownership_type',
    'management_type',
    'province_id',
    'district_id',
    'municipality_id',
    'ward_id',
    'address',
    'address_ne',
    'latitude',
    'longitude',
    'official_website',
    'logo_path',
    'status',
    'verification_status',
    'is_featured',
    'published_at',
])]
class Institution extends Model
{
    /** @use HasFactory<InstitutionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ownership_type' => OwnershipType::class,
            'management_type' => ManagementType::class,
            'status' => InstitutionStatus::class,
            'verification_status' => VerificationStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'website_sections' => 'array',
        ];
    }

    /**
     * @param  Builder<Institution>  $query
     * @return Builder<Institution>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', InstitutionStatus::Published);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(InstitutionType::class, 'institution_type_id');
    }

    public function parentInstitution(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_institution_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(self::class, 'parent_institution_id');
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

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(InstitutionCategory::class, 'institution_category')
            ->withTimestamps();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(InstitutionContact::class);
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'institution_programs')
            ->withPivot(['id', 'duration', 'is_active', 'admission_information', 'source_id'])
            ->withTimestamps();
    }

    public function institutionPrograms(): HasMany
    {
        return $this->hasMany(InstitutionProgram::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(InstitutionFacility::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(InstitutionImage::class);
    }

    public function dataSources(): BelongsToMany
    {
        return $this->belongsToMany(DataSource::class, 'institution_sources')
            ->withPivot(['id', 'source_url', 'source_identifier', 'collected_at', 'last_checked_at'])
            ->withTimestamps();
    }

    public function institutionSources(): HasMany
    {
        return $this->hasMany(InstitutionSource::class);
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(InstitutionAffiliation::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(InstitutionVerification::class);
    }

    public function fieldEvidence(): HasMany
    {
        return $this->hasMany(InstitutionFieldEvidence::class);
    }

    public function changeLogs(): HasMany
    {
        return $this->hasMany(InstitutionChangeLog::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(InstitutionClaim::class);
    }

    public function seo(): HasOne
    {
        return $this->hasOne(InstitutionSeo::class);
    }
}
