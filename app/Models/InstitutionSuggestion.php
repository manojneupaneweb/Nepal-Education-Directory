<?php

namespace App\Models;

use Database\Factories\InstitutionSuggestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'institution_name',
    'institution_type_id',
    'province_id',
    'location',
    'website',
    'submitter_name',
    'submitter_email',
    'notes',
])]
class InstitutionSuggestion extends Model
{
    /** @use HasFactory<InstitutionSuggestionFactory> */
    use HasFactory;

    public function type(): BelongsTo
    {
        return $this->belongsTo(InstitutionType::class, 'institution_type_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
