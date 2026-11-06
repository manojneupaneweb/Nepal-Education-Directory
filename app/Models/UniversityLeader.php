<?php

namespace App\Models;

use App\Enums\ResearchVerificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'university_id',
    'name',
    'designation',
    'profile_url',
    'source_url',
    'verification_status',
])]
class UniversityLeader extends Model
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
}
