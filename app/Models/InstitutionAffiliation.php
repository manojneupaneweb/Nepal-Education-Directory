<?php

namespace App\Models;

use App\Enums\AffiliationStatus;
use App\Enums\AffiliationType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'institution_id',
    'university_id',
    'affiliation_type',
    'affiliation_number',
    'valid_from',
    'valid_until',
    'status',
    'source_id',
])]
class InstitutionAffiliation extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'affiliation_type' => AffiliationType::class,
            'status' => AffiliationStatus::class,
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(DataSource::class, 'source_id');
    }
}
