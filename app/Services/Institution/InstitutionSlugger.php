<?php

namespace App\Services\Institution;

use App\Models\Institution;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InstitutionSlugger
{
    public function generate(string $englishName, ?int $ignoreId = null): string
    {
        $base = Str::slug($englishName);

        if ($base === '') {
            throw ValidationException::withMessages([
                'name' => 'The English name must contain letters or numbers that can form a unique URL slug. Nepali text is stored separately and is not transliterated.',
            ]);
        }

        $slug = $base;
        $suffix = 2;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    public function resolve(?string $requestedSlug, string $englishName, ?Institution $existing = null): string
    {
        $requestedSlug = trim((string) $requestedSlug);

        if ($existing !== null && ($requestedSlug === '' || $requestedSlug === $existing->slug)) {
            return $existing->slug;
        }

        if ($requestedSlug === '') {
            return $this->generate($englishName, $existing?->id);
        }

        $slug = Str::slug($requestedSlug);

        if ($slug === '') {
            throw ValidationException::withMessages([
                'slug' => 'The slug must contain letters or numbers.',
            ]);
        }

        if ($this->slugExists($slug, $existing?->id)) {
            throw ValidationException::withMessages([
                'slug' => 'That slug is already in use.',
            ]);
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId): bool
    {
        return Institution::withTrashed()
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists();
    }
}
