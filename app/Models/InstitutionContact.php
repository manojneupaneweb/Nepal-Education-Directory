<?php

namespace App\Models;

use App\Enums\ContactType;
use Database\Factories\InstitutionContactFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'institution_id',
    'contact_type',
    'label',
    'contact_value',
    'is_primary',
    'is_public',
    'source_id',
])]
class InstitutionContact extends Model
{
    /** @use HasFactory<InstitutionContactFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'contact_type' => ContactType::class,
            'is_primary' => 'boolean',
            'is_public' => 'boolean',
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
