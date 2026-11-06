<?php

namespace App\Models;

use App\Enums\FieldReviewStatus;
use Database\Factories\InstitutionFieldEvidenceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'institution_id',
    'institution_source_id',
    'field_name',
    'extracted_value',
    'normalized_value',
    'confidence',
    'review_status',
    'reviewed_by',
    'reviewed_at',
])]
class InstitutionFieldEvidence extends Model
{
    /** @use HasFactory<InstitutionFieldEvidenceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'confidence' => 'decimal:4',
            'review_status' => FieldReviewStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function institutionSource(): BelongsTo
    {
        return $this->belongsTo(InstitutionSource::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
