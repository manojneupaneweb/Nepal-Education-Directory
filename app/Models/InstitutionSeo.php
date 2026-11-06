<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'institution_id',
    'meta_title',
    'meta_description',
    'canonical_url',
    'indexable',
    'structured_data_enabled',
])]
class InstitutionSeo extends Model
{
    protected $table = 'institution_seo';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'indexable' => 'boolean',
            'structured_data_enabled' => 'boolean',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
