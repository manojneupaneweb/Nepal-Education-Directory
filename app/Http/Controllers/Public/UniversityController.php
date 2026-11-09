<?php

namespace App\Http\Controllers\Public;

use App\Enums\InstitutionStatus;
use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\Province;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityController extends Controller
{
    public function index(Request $request): View
    {
        $ordered = University::query()
            ->with(['province', 'district'])
            ->withCount(['programs', 'institutions'])
            ->when($request->string('q')->toString(), function ($query, string $term) {
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('name_ne', 'like', "%{$term}%")
                        ->orWhere('location_label', 'like', "%{$term}%");
                });
            })
            ->when($request->integer('province'), fn ($query, int $id) => $query->where('province_id', $id))
            ->when($request->integer('district'), fn ($query, int $id) => $query->where('district_id', $id))
            ->when($request->string('type')->toString(), fn ($query, string $type) => $query->where('university_type', $type))
            ->when($request->string('ownership')->toString(), fn ($query, string $ownership) => $query->where('ownership', $ownership))
            ->when($request->integer('level') || $request->string('discipline')->toString(), function ($query) use ($request) {
                $query->whereHas('programs', function ($programs) use ($request) {
                    $programs->when($request->integer('level'), fn ($inner, int $level) => $inner->where('education_level_id', $level));
                    $programs->when($request->string('discipline')->toString(), fn ($inner, string $discipline) => $inner->where('discipline', $discipline));
                });
            });

        $sort = (string) $request->string('sort');
        if ($sort === 'programs') {
            $ordered->orderByDesc('programs_count')->orderBy('name');
        } elseif ($sort === 'colleges') {
            $ordered->orderByDesc('institutions_count')->orderBy('name');
        } elseif ($sort === 'oldest') {
            $ordered->orderBy('established_year_bs')->orderBy('name');
        } else {
            $sort = 'az';
            $ordered->orderBy('name');
        }

        $universities = $ordered
            ->paginate(12)
            ->withQueryString();

        $totals = University::query()->withCount(['programs', 'institutions'])->get();

        return view('public.universities.index', [
            'universities' => $universities,
            'totalUniversities' => University::query()->count(),
            'totalPrograms' => $totals->sum('programs_count'),
            'totalColleges' => $totals->sum('institutions_count'),
            'types' => University::query()->whereNotNull('university_type')->distinct()->orderBy('university_type')->pluck('university_type'),
            'provinces' => Province::query()->active()->orderBy('name')->get(),
            'sort' => $sort,
        ]);
    }

    public function show(University $university): View
    {
        $university->load([
            'province',
            'district',
            'municipality',
            'leaders',
            'faculties',
            'sources',
            'programs.educationLevel',
            'programs.faculty',
        ]);
        $university->loadCount('institutions');

        PageView::record('university');

        return view('public.universities.show', [
            'university' => $university,
        ]);
    }

    public function colleges(University $university): View
    {
        $colleges = $university->institutions()
            ->where('institutions.status', InstitutionStatus::Published)
            ->with(['district', 'municipality', 'province'])
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        return view('public.universities.colleges', [
            'university' => $university,
            'colleges' => $colleges,
        ]);
    }
}
