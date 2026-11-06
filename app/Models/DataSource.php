<?php

namespace App\Models;

use App\Enums\AccessMethod;
use App\Enums\SourceType;
use Database\Factories\DataSourceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'source_type',
    'base_url',
    'authority_level',
    'access_method',
    'is_active',
    'crawl_frequency',
    'last_collected_at',
    'configuration',
])]
class DataSource extends Model
{
    /** @use HasFactory<DataSourceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'source_type' => SourceType::class,
            'access_method' => AccessMethod::class,
            'is_active' => 'boolean',
            'authority_level' => 'integer',
            'last_collected_at' => 'datetime',
            'configuration' => 'array',
        ];
    }

    public function collectionRuns(): HasMany
    {
        return $this->hasMany(CollectionRun::class);
    }

    public function collectionJobs(): HasMany
    {
        return $this->hasMany(CollectionJob::class);
    }

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class, 'institution_sources')
            ->withPivot(['id', 'source_url', 'source_identifier', 'collected_at', 'last_checked_at'])
            ->withTimestamps();
    }

    public function institutionSources(): HasMany
    {
        return $this->hasMany(InstitutionSource::class);
    }
}
