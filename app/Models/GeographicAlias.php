<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['aliasable_type', 'aliasable_id', 'alias', 'alias_ne', 'kind', 'geographic_dataset_id'])]
class GeographicAlias extends Model
{
    public function aliasable(): MorphTo
    {
        return $this->morphTo();
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(GeographicDataset::class, 'geographic_dataset_id');
    }
}
