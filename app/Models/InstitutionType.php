<?php

namespace App\Models;

use Database\Factories\InstitutionTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'name_ne', 'slug', 'description', 'description_ne', 'is_active', 'sort_order'])]
class InstitutionType extends Model
{
    /** @use HasFactory<InstitutionTypeFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<InstitutionType>  $query
     * @return Builder<InstitutionType>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }
}
