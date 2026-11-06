<?php

namespace App\Models;

use Database\Factories\ProgramCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description'])]
class ProgramCategory extends Model
{
    /** @use HasFactory<ProgramCategoryFactory> */
    use HasFactory;

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }
}
