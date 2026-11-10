<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstitutionEvidenceRequest;
use App\Models\Institution;
use App\Models\InstitutionFieldEvidence;
use App\Models\InstitutionType;
use App\Services\Institution\InstitutionEvidenceRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SourceReviewController extends Controller
{
    public function __construct(private readonly InstitutionEvidenceRecorder $evidence) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->is_admin === true, 403);

        $typeSlug = $request->string('type')->toString() ?: 'school';
        $gap = $request->string('gap')->toString() ?: 'phone';
        $review = $request->string('evidence')->toString();
        $type = InstitutionType::query()->where('slug', $typeSlug)->first();

        $institutions = Institution::query()
            ->published()
            ->when($type, fn ($query) => $query->where('institution_type_id', $type->id))
            ->when($request->boolean('checked'), fn ($query) => $query->whereHas('institutionSources', fn ($sources) => $sources->where('last_checked_at', '>=', now()->subDays(30))))
            ->when($review === '', function ($query) use ($request, $gap): void {
                $query->where('official_website', 'like', 'http%');
                if (! $request->boolean('include_complete')) {
                    $this->missing($query, $gap);
                }
            })
            ->when($review !== '', fn ($query) => $query->whereHas('fieldEvidence', fn ($evidence) => $evidence->where('review_status', $review)))
            ->with(['municipality:id,name', 'district:id,name'])
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.source-review.index', [
            'institutions' => $institutions,
            'typeSlug' => $typeSlug,
            'gap' => $gap,
            'review' => $review,
        ]);
    }

    public function show(Request $request, Institution $institution): View
    {
        abort_unless($request->user()?->is_admin === true, 403);
        $institution->load(['type', 'municipality', 'district', 'province', 'contacts', 'fieldEvidence.institutionSource', 'institutionSources']);

        return view('admin.source-review.show', [
            'institution' => $institution,
            'fields' => $this->evidence->fields(),
        ]);
    }

    public function store(StoreInstitutionEvidenceRequest $request, Institution $institution): RedirectResponse
    {
        $this->evidence->propose(
            $institution,
            $request->string('field')->toString(),
            $request->string('value')->toString(),
            $request->string('source_url')->toString(),
        );

        return redirect()
            ->route('admin.source-review.show', $institution)
            ->with('status', 'The proposal was saved for review. The institution record was not changed.');
    }

    public function accept(Request $request, InstitutionFieldEvidence $evidence): RedirectResponse
    {
        abort_unless($request->user()?->is_admin === true, 403);
        $this->evidence->accept($evidence, $request->user());

        return redirect()
            ->route('admin.source-review.show', $evidence->institution_id)
            ->with('status', 'The proposal was accepted into an empty field.');
    }

    public function reject(Request $request, InstitutionFieldEvidence $evidence): RedirectResponse
    {
        abort_unless($request->user()?->is_admin === true, 403);
        $this->evidence->reject($evidence, $request->user());

        return redirect()
            ->route('admin.source-review.show', $evidence->institution_id)
            ->with('status', 'The proposal was rejected. The institution record was not changed.');
    }

    private function missing($query, string $gap): void
    {
        match ($gap) {
            'email' => $query->whereDoesntHave('contacts', fn ($contacts) => $contacts->where('contact_type', 'email')->where('contact_value', '!=', '')),
            'description' => $query->whereRaw(($query->getConnection()->getDriverName() === 'sqlite' ? 'length' : 'char_length').'(trim(coalesce(description, ?))) < ?', ['', (int) config('seo.quality.min_description_length', 80)]),
            default => $query->whereDoesntHave('contacts', fn ($contacts) => $contacts->where('contact_type', 'phone')->where('contact_value', '!=', '')->where(function ($contacts): void {
                $contacts->whereNull('label')->orWhere('label', '!=', 'Named principal');
            })),
        };
    }
}
