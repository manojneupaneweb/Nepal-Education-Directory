<?php

namespace App\Models;

use App\Enums\CollectionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'data_source_id',
    'status',
    'started_at',
    'completed_at',
    'total_discovered',
    'total_processed',
    'total_failed',
    'metadata',
])]
class CollectionRun extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CollectionStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function dataSource(): BelongsTo
    {
        return $this->belongsTo(DataSource::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(CollectionJob::class);
    }
}
