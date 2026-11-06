<?php

namespace App\Models;

use App\Enums\SourceCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'university_id',
    'category',
    'url',
    'title',
    'notes',
    'retrieved_at',
])]
class UniversitySource extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => SourceCategory::class,
            'retrieved_at' => 'datetime',
        ];
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
