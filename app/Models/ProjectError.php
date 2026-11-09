<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'exception',
    'message',
    'file',
    'line',
    'url',
    'occurrences',
    'resolved_at',
])]
class ProjectError extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'occurrences' => 'integer',
            'line' => 'integer',
        ];
    }

    /**
     * @param  Builder<ProjectError>  $query
     * @return Builder<ProjectError>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('resolved_at');
    }
}
