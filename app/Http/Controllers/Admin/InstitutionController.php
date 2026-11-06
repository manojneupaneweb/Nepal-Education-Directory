<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InstitutionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstitutionRequest;
use App\Http\Requests\UpdateInstitutionRequest;
use App\Models\Institution;
use App\Models\InstitutionCategory;
use App\Models\InstitutionType;
use App\Services\Institution\InstitutionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly InstitutionService $institutions) {}

    public function index(): View
    {
        $this->authorize('viewAny', Institution::class);

        return view('admin.institutions.index', [
            'institutions' => Institution::query()
                ->with(['type', 'district'])
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Institution::class);

        return view('admin.institutions.create', $this->formData(new Institution));
    }

    public function store(StoreInstitutionRequest $request): RedirectResponse
    {
        $institution = $this->institutions->create(
            $request->institutionAttributes(),
            $request->categoryIds(),
        );

        return redirect()
            ->route('admin.institutions.show', $institution)
            ->with('status', 'Institution saved as a draft.');
    }

    public function show(Institution $institution): View
    {
        $this->authorize('view', $institution);

        $institution->load([
            'type',
            'parentInstitution',
            'branches',
            'province',
            'district',
            'municipality',
            'ward',
            'categories',
            'contacts',
            'programs',
            'images',
            'institutionSources',
            'verifications',
        ]);

        return view('admin.institutions.show', [
            'institution' => $institution,
        ]);
    }

    public function edit(Institution $institution): View
    {
        $this->authorize('update', $institution);

        $institution->load('categories');

        return view('admin.institutions.edit', $this->formData($institution));
    }

    public function update(UpdateInstitutionRequest $request, Institution $institution): RedirectResponse
    {
        $this->institutions->update(
            $institution,
            $request->institutionAttributes(),
            $request->categoryIds(),
        );

        return redirect()
            ->route('admin.institutions.show', $institution)
            ->with('status', 'Institution updated. Publication status was left unchanged.');
    }

    public function submit(Institution $institution): RedirectResponse
    {
        $this->authorize('review', $institution);
        $this->institutions->transition($institution, InstitutionStatus::Pending, request()->user());

        return back()->with('status', 'Institution submitted for review.');
    }

    public function approve(Institution $institution): RedirectResponse
    {
        $this->authorize('review', $institution);
        $this->institutions->transition($institution, InstitutionStatus::Published, request()->user(), request('reason'));

        return back()->with('status', 'Institution approved for publication. Verification status was not changed.');
    }

    public function reject(Request $request, Institution $institution): RedirectResponse
    {
        $this->authorize('review', $institution);
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);
        $this->institutions->transition($institution, InstitutionStatus::Rejected, $request->user(), $data['reason'] ?? null);

        return back()->with('status', 'Institution rejected.');
    }

    public function archive(Institution $institution): RedirectResponse
    {
        $this->authorize('review', $institution);
        $this->institutions->transition($institution, InstitutionStatus::Archived, request()->user(), request('reason'));

        return back()->with('status', 'Institution archived.');
    }

    public function destroy(Institution $institution): RedirectResponse
    {
        $this->authorize('delete', $institution);

        $institution->delete();

        return redirect()
            ->route('admin.institutions.index')
            ->with('status', 'Institution moved to the trash.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Institution $institution): array
    {
        return [
            'institution' => $institution,
            'types' => InstitutionType::query()->active()->orderBy('sort_order')->orderBy('name')->get(),
            'categories' => InstitutionCategory::query()->active()->orderBy('sort_order')->orderBy('name')->get(),
        ];
    }
}
