<?php

namespace App\Models;

use Database\Factories\WardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['municipality_id', 'ward_number', 'name', 'name_ne', 'code', 'is_active', 'geographic_dataset_id'])]
class Ward extends Model
{
    /** @use HasFactory<WardFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ward_number' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Ward>  $query
     * @return Builder<Ward>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(GeographicDataset::class, 'geographic_dataset_id');
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }
}
