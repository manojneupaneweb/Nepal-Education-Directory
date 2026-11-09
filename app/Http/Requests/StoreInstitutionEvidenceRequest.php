<?php

namespace App\Http\Requests;

use App\Services\Institution\InstitutionEvidenceRecorder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInstitutionEvidenceRequest extends FormRequest
{
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
            'field' => ['required', 'string', Rule::in(app(InstitutionEvidenceRecorder::class)->fields())],
            'value' => ['required', 'string', 'max:5000'],
            'source_url' => ['required', 'string', 'max:500'],
        ];
    }
}
