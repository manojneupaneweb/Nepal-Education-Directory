<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'key',
    'name',
    'organization',
    'source_url',
    'version',
    'reference_period',
    'retrieved_on',
    'imported_at',
    'file_checksum',
    'levels',
    'validation_status',
    'notes',
])]
class GeographicDataset extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'retrieved_on' => 'date',
            'imported_at' => 'datetime',
            'levels' => 'array',
        ];
    }

    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipality::class);
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }
}
