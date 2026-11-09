<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CollegeStream;
use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\University;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'provinces' => Province::query()->active()->withDirectoryCounts()->orderBy('code')->get(),
            'types' => InstitutionType::query()->active()->orderBy('sort_order')->get(),
            'featuredTypes' => InstitutionType::query()
                ->active()
                ->whereIn('slug', ['school', 'college', 'university'])
                ->withCount(['institutions' => fn ($q) => $q->published()])
                ->orderBy('sort_order')
                ->get()
                ->each(function (InstitutionType $type): void {
                    if ($type->slug === 'university') {
                        $type->setAttribute('institutions_count', University::query()->count());
                    }
                }),
            'popularUniversities' => University::query()
                ->with(['faculties:id,university_id,name'])
                ->withCount(['faculties', 'programs', 'institutions'])
                ->whereIn('slug', [
                    'tribhuvan-university',
                    'kathmandu-university',
                    'pokhara-university',
                    'purbanchal-university',
                ])
                ->get()
                ->sortBy(fn (University $university) => array_search($university->slug, [
                    'tribhuvan-university',
                    'kathmandu-university',
                    'pokhara-university',
                    'purbanchal-university',
                ], true))
                ->values(),
            'featuredColleges' => Institution::query()
                ->published()
                ->whereNotNull('logo_path')
                ->where('logo_path', '!=', '')
                ->whereHas('type', fn ($query) => $query->where('slug', 'college'))
                ->with(['district', 'municipality'])
                ->orderByDesc('is_featured')
                ->orderBy('name')
                ->limit(8)
                ->get(),
            'collegeStreams' => CollegeStream::query()->active()->orderBy('sort_order')->get()
                ->each(function (CollegeStream $stream): void {
                    $stream->setAttribute('matched_colleges_count', $stream->matchingInstitutions()->count());
                }),
            'counts' => [
                'provinces' => Province::query()->active()->count(),
                'districts' => District::query()->active()->count(),
                'municipalities' => Municipality::query()->active()->count(),
                'institutions' => Institution::query()->published()->count(),
            ],
        ]);
    }
}
