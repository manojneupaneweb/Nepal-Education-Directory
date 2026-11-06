<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Database\Factories\InstitutionVerificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'institution_id',
    'verification_type',
    'verification_status',
    'evidence',
    'verified_by',
    'verified_at',
])]
class InstitutionVerification extends Model
{
    /** @use HasFactory<InstitutionVerificationFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verification_status' => VerificationStatus::class,
            'evidence' => 'array',
            'verified_at' => 'datetime',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
