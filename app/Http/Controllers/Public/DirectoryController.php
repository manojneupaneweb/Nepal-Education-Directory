<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionCategory;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    public function schools(Request $request): View
    {
        $schoolType = InstitutionType::where('slug', 'school')->first();

        $province = Province::query()->active()->find($request->integer('province'));
        $district = District::query()->active()
            ->when($province, fn ($query) => $query->where('province_id', $province->id))
            ->find($request->integer('district'));
        $municipality = Municipality::query()->active()
            ->when($district, fn ($query) => $query->where('district_id', $district->id))
            ->find($request->integer('municipality'));

        $query = Institution::query()->published();
        if ($schoolType) {
            $query->where('institution_type_id', $schoolType->id);
        }
        if ($province) {
            $query->where('province_id', $province->id);
        }
        if ($district) {
            $query->where('district_id', $district->id);
        }
        if ($municipality) {
            $query->where('municipality_id', $municipality->id);
        }

        $schools = (clone $query)
            ->with(['municipality', 'district', 'province', 'categories'])
            ->orderBy('name')
            ->paginate(18)
            ->withQueryString();

        $totalSchools = (clone $query)->count();

        $categories = InstitutionCategory::query()
            ->active()
            ->where('slug', 'like', '%school%')
            ->withCount(['institutions' => function ($q) use ($schoolType) {
                $q->published();
                if ($schoolType) {
                    $q->where('institution_type_id', $schoolType->id);
                }
            }])
            ->get();

        $provinces = Province::query()
            ->active()
            ->withCount('districts')
            ->orderBy('code')
            ->get();

        $districts = District::query()
            ->active()
            ->when($province, fn ($query) => $query->where('province_id', $province->id))
            ->orderBy('name')
            ->get();

        $municipalities = $district
            ? Municipality::query()->active()->where('district_id', $district->id)->orderBy('name')->get()
            : collect();

        return view('public.schools.index', [
            'schools' => $schools,
            'totalSchools' => $totalSchools,
            'schoolType' => $schoolType,
            'categories' => $categories,
            'provinces' => $provinces,
            'districts' => $districts,
            'municipalities' => $municipalities,
            'selectedProvince' => $province,
            'selectedDistrict' => $district,
            'selectedMunicipality' => $municipality,
        ]);
    }

    public function types(): View
    {
        return view('public.types.index', [
            'types' => InstitutionType::query()
                ->active()
                ->withCount(['institutions' => fn ($query) => $query->published()])
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function type(InstitutionType $institutionType): View
    {
        abort_unless($institutionType->is_active, 404);

        return view('public.types.show', [
            'type' => $institutionType,
            'institutions' => $institutionType->institutions()
                ->published()
                ->with(['municipality', 'district'])
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function provinces(): View
    {
        return view('public.places.provinces', [
            'provinces' => Province::query()->active()->withCount('districts')->orderBy('code')->get(),
        ]);
    }

    public function province(Province $province): View
    {
        abort_unless($province->is_active, 404);

        return view('public.places.province', [
            'province' => $province,
            'districts' => $province->districts()->active()->withCount('municipalities')->orderBy('name')->get(),
        ]);
    }

    public function district(District $district): View
    {
        abort_unless($district->is_active, 404);
        $district->load('province');

        return view('public.places.district', [
            'district' => $district,
            'municipalities' => $district->municipalities()->active()->orderBy('name')->get(),
        ]);
    }

    public function municipality(District $district, Municipality $municipality): View
    {
        abort_unless($district->is_active && $municipality->is_active, 404);
        abort_unless($municipality->district_id === $district->id, 404);
        $district->load('province');

        return view('public.places.municipality', [
            'district' => $district,
            'municipality' => $municipality,
            'institutions' => Institution::query()
                ->published()
                ->where('municipality_id', $municipality->id)
                ->with('type')
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function show(Institution $institution): View
    {
        abort_unless($institution->status->value === 'published', 404);
        $institution->load(['type', 'categories', 'province', 'district', 'municipality', 'ward']);

        return view('public.institutions.show', [
            'institution' => $institution,
        ]);
    }

    public function neb(): View
    {
        return view('public.neb');
    }

    public function pabson(): View
    {
        return view('public.pabson');
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function howItWorks(): View
    {
        return view('public.how-it-works');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function blog(): View
    {
        $filePath = base_path('data/blogs.json');
        $blogs = [];

        if (file_exists($filePath)) {
            $blogs = json_decode(file_get_contents($filePath), true) ?: [];
        }

        $categories = collect($blogs)->pluck('category')->unique()->sort()->values();

        return view('public.blog', [
            'blogs' => $blogs,
            'categories' => $categories,
        ]);
    }
}
