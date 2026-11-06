<?php

namespace App\Models;

use App\Enums\ResearchVerificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'university_id',
    'name',
    'name_ne',
    'slug',
    'description',
    'source_url',
    'verification_status',
])]
class UniversityFaculty extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verification_status' => ResearchVerificationStatus::class,
        ];
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }
}
