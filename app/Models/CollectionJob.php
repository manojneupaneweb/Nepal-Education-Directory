<?php

namespace App\Models;

use App\Enums\CollectionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'collection_run_id',
    'data_source_id',
    'source_url',
    'status',
    'attempts',
    'error_message',
    'scheduled_at',
    'processed_at',
])]
class CollectionJob extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CollectionStatus::class,
            'attempts' => 'integer',
            'scheduled_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function collectionRun(): BelongsTo
    {
        return $this->belongsTo(CollectionRun::class);
    }

    public function dataSource(): BelongsTo
    {
        return $this->belongsTo(DataSource::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SourceDocument::class);
    }
}
