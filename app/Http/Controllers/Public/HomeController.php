<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
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
            'provinces' => Province::query()->active()->withCount('districts')->orderBy('code')->get(),
            'types' => InstitutionType::query()->active()->orderBy('sort_order')->get(),
            'featuredTypes' => InstitutionType::query()
                ->active()
                ->withCount(['institutions' => fn ($q) => $q->published()])
                ->orderBy('sort_order')
                ->limit(8)
                ->get(),
            'popularUniversities' => University::query()
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
            'counts' => [
                'provinces' => Province::query()->active()->count(),
                'districts' => District::query()->active()->count(),
                'municipalities' => Municipality::query()->active()->count(),
                'institutions' => Institution::query()->published()->count(),
            ],
        ]);
    }
}
