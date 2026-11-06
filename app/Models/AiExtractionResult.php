<?php

namespace App\Models;

use App\Enums\ExtractionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'source_document_id',
    'provider',
    'model',
    'prompt_version',
    'raw_response',
    'parsed_data',
    'status',
    'confidence_score',
    'token_usage',
    'error_message',
])]
class AiExtractionResult extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'raw_response' => 'array',
            'parsed_data' => 'array',
            'token_usage' => 'array',
            'status' => ExtractionStatus::class,
            'confidence_score' => 'decimal:4',
        ];
    }

    public function sourceDocument(): BelongsTo
    {
        return $this->belongsTo(SourceDocument::class);
    }
}
