<?php

namespace App\Services\Location;

use App\Enums\MunicipalityType;
use Illuminate\Support\Str;
use Normalizer;

class GeographyNormalizer
{
    public function englishName(string $value): string
    {
        $value = $this->collapseWhitespace($value);

        if ($value !== '' && mb_strtoupper($value, 'UTF-8') === $value) {
            return mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        }

        return $value;
    }

    public function nepaliName(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = $this->collapseWhitespace($value);

        if ($value === '' || ! $this->isDevanagari($value)) {
            return null;
        }

        if (class_exists(Normalizer::class)) {
            $normalized = Normalizer::normalize($value, Normalizer::FORM_C);

            return is_string($normalized) ? $normalized : $value;
        }

        return $value;
    }

    public function slug(string $englishName): string
    {
        return Str::slug($englishName);
    }

    public function municipalityType(string $englishName): ?MunicipalityType
    {
        $name = mb_strtolower($englishName, 'UTF-8');

        if (str_contains($name, 'rural municipality')) {
            return MunicipalityType::RuralMunicipality;
        }

        if (str_contains($name, 'sub-metropol') || str_contains($name, 'sub metropol')) {
            return MunicipalityType::SubMetropolitan;
        }

        if (str_contains($name, 'metropol')) {
            return MunicipalityType::Metropolitan;
        }

        if (str_contains($name, 'municipality')) {
            return MunicipalityType::Municipality;
        }

        return null;
    }

    public function isDevanagari(string $value): bool
    {
        return preg_match('/\p{Devanagari}/u', $value) === 1
            && preg_match('/[A-Za-z]/', $value) !== 1;
    }

    private function collapseWhitespace(string $value): string
    {
        $value = str_replace("\u{00A0}", ' ', trim($value));

        return preg_replace('/\s+/u', ' ', $value) ?? $value;
    }
}
