<?php

namespace App\Models;

use App\Enums\MunicipalityType;
use Database\Factories\MunicipalityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['district_id', 'name', 'name_ne', 'slug', 'municipality_type', 'code', 'is_active', 'geographic_dataset_id'])]
class Municipality extends Model
{
    /** @use HasFactory<MunicipalityFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'municipality_type' => MunicipalityType::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Municipality>  $query
     * @return Builder<Municipality>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(GeographicDataset::class, 'geographic_dataset_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }

    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }
}
