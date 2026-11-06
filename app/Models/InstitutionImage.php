<?php

namespace App\Models;

use App\Enums\ImageType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'institution_id',
    'image_type',
    'file_path',
    'alt_text',
    'alt_text_ne',
    'caption',
    'is_primary',
    'sort_order',
    'source_id',
])]
class InstitutionImage extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'image_type' => ImageType::class,
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(DataSource::class, 'source_id');
    }
}
