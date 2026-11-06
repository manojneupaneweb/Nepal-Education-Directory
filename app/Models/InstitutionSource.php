<?php

namespace App\Models;

use Database\Factories\InstitutionSourceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'institution_id',
    'data_source_id',
    'source_url',
    'source_identifier',
    'collected_at',
    'last_checked_at',
])]
class InstitutionSource extends Model
{
    /** @use HasFactory<InstitutionSourceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'collected_at' => 'datetime',
            'last_checked_at' => 'datetime',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function dataSource(): BelongsTo
    {
        return $this->belongsTo(DataSource::class);
    }

    public function fieldEvidence(): HasMany
    {
        return $this->hasMany(InstitutionFieldEvidence::class);
    }
}
