<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstitutionSuggestionRequest;
use App\Models\InstitutionSuggestion;
use App\Models\InstitutionType;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstitutionSuggestionController extends Controller
{
    public function create(): View
    {
        return view('public.suggestions.create', [
            'types' => InstitutionType::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'provinces' => Province::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreInstitutionSuggestionRequest $request): RedirectResponse
    {
        InstitutionSuggestion::query()->create($request->safe()->only([
            'institution_name',
            'institution_type_id',
            'province_id',
            'location',
            'website',
            'submitter_name',
            'submitter_email',
            'notes',
        ]));

        return redirect()
            ->route('suggestions.create')
            ->with('status', 'Thank you. We received your suggestion and will review it before adding the institution.');
    }
}
