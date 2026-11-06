<?php

namespace App\Http\Requests;

use App\Enums\ManagementType;
use App\Enums\OwnershipType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInstitutionRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $nullable = [
            'parent_institution_id',
            'ward_id',
            'established_year',
            'ownership_type',
            'management_type',
            'official_website',
            'latitude',
            'longitude',
            'slug',
            'registration_number',
            'registration_authority',
            'name_ne',
            'short_name',
            'address',
            'address_ne',
        ];

        foreach ($nullable as $field) {
            if ($this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }

        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'name_ne' => ['nullable', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string'],
            'description_ne' => ['nullable', 'string'],
            'institution_type_id' => ['required', 'integer', 'exists:institution_types,id'],
            'parent_institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'distinct', 'exists:institution_categories,id'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'registration_authority' => ['nullable', 'string', 'max:255'],
            'established_year' => ['nullable', 'integer', 'between:1800,'.(now()->year + 1)],
            'ownership_type' => ['nullable', Rule::enum(OwnershipType::class)],
            'management_type' => ['nullable', Rule::enum(ManagementType::class)],
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'municipality_id' => ['required', 'integer', 'exists:municipalities,id'],
            'ward_id' => ['nullable', 'integer', 'exists:wards,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_ne' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'official_website' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function institutionAttributes(): array
    {
        return $this->safe()->except(['category_ids']);
    }

    /**
     * @return list<int>
     */
    public function categoryIds(): array
    {
        return array_map('intval', $this->validated('category_ids') ?? []);
    }
}
