<?php

namespace App\Models;

use Database\Factories\CollegeStreamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug', 'colleges_count', 'keywords', 'icon_path', 'color', 'sort_order', 'is_active'])]
class CollegeStream extends Model
{
    /** @use HasFactory<CollegeStreamFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'colleges_count' => 'integer',
            'keywords' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param  Builder<CollegeStream>  $query
     * @return Builder<CollegeStream>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Published colleges whose name or description matches this stream.
     *
     * @return Builder<Institution>
     */
    public function matchingInstitutions(): Builder
    {
        $collegeTypeId = InstitutionType::query()->where('slug', 'college')->value('id');
        $keywords = collect($this->keywords ?: [$this->name])
            ->map(fn (string $word) => trim($word))
            ->filter();

        return Institution::query()
            ->published()
            ->when($collegeTypeId, fn (Builder $query) => $query->where('institution_type_id', $collegeTypeId))
            ->where(function (Builder $query) use ($keywords) {
                foreach ($keywords as $word) {
                    $like = '%'.addcslashes($word, '%_\\').'%';
                    $query->orWhere('name', 'like', $like)
                        ->orWhere('name_ne', 'like', $like)
                        ->orWhere('short_name', 'like', $like)
                        ->orWhere('description', 'like', $like);
                }
            });
    }
}
