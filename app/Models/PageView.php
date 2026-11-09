<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['kind'])]
class PageView extends Model
{
    public static function record(string $kind): void
    {
        if (! in_array($kind, ['school', 'college', 'university'], true)) {
            return;
        }

        static::query()->create(['kind' => $kind]);
    }
}
