<?php

namespace App\Models;

use App\Enums\ResearchVerificationStatus;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'university_id',
    'university_faculty_id',
    'program_category_id',
    'education_level_id',
    'name',
    'name_ne',
    'slug',
    'code',
    'description',
    'duration',
    'discipline',
    'eligibility',
    'admission_requirements',
    'specialization',
    'curriculum_url',
    'admission_url',
    'program_status',
    'source_url',
    'verification_status',
    'seo_title',
    'meta_description',
])]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verification_status' => ResearchVerificationStatus::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(UniversityFaculty::class, 'university_faculty_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProgramCategory::class, 'program_category_id');
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class, 'institution_programs')
            ->withPivot(['id', 'duration', 'is_active', 'admission_information', 'source_id'])
            ->withTimestamps();
    }
}
