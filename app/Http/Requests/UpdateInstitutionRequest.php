<?php

namespace App\Http\Requests;

class UpdateInstitutionRequest extends StoreInstitutionRequest
{
    public function authorize(): bool
    {
        $institution = $this->route('institution');

        return $this->user()?->can('update', $institution) === true;
    }
}
