<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'institution_id',
    'claimant_name',
    'claimant_email',
    'claimant_role',
    'evidence',
    'status',
    'reviewed_by',
    'reviewed_at',
])]
class InstitutionClaim extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'evidence' => 'array',
            'status' => ClaimStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
