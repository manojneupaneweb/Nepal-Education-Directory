<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'university_id',
    'status',
    'approval_status',
    'proposed_changes',
    'summary',
    'failure_reason',
    'started_at',
    'finished_at',
    'reviewed_at',
])]
class UniversityResearchHistory extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'proposed_changes' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
