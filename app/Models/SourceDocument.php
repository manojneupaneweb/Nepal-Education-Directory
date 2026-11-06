<?php

namespace App\Models;

use App\Enums\ExtractionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'collection_job_id',
    'document_type',
    'source_url',
    'storage_path',
    'content_hash',
    'extraction_status',
    'fetched_at',
    'metadata',
])]
class SourceDocument extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'extraction_status' => ExtractionStatus::class,
            'fetched_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function collectionJob(): BelongsTo
    {
        return $this->belongsTo(CollectionJob::class);
    }

    public function extractionResults(): HasMany
    {
        return $this->hasMany(AiExtractionResult::class);
    }
}
