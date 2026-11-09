<?php

namespace App\Http\Controllers\Public;

use App\Enums\InstitutionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionCategory;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\PageView;
use App\Models\Province;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
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

        $term = trim((string) $request->string('q'));
        $category = InstitutionCategory::query()
            ->whereIn('slug', ['government-school', 'private-school'])
            ->where('slug', (string) $request->string('category'))
            ->first();

        $query = Institution::query()->published();
        if ($schoolType) {
            $query->where('institution_type_id', $schoolType->id);
        }
        if ($term !== '') {
            $like = '%'.addcslashes($term, '%_\\').'%';
            $query->where('name', 'like', $like);
        }
        if ($category) {
            $query->whereHas('categories', fn ($categories) => $categories->whereKey($category->id));
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

        $sort = (string) $request->string('sort');
        $ordered = (clone $query)->with(['municipality', 'district', 'province', 'categories']);
        if ($sort === 'za') {
            $ordered->orderByDesc('name');
        } elseif ($sort === 'new') {
            $ordered->orderByDesc('published_at')->orderBy('name');
        } else {
            $sort = 'az';
            $ordered->orderBy('name');
        }

        $schools = $ordered
            ->paginate(18)
            ->withQueryString();

        $totalSchools = (clone $query)->count();

        $categories = InstitutionCategory::query()
            ->active()
            ->whereIn('slug', ['government-school', 'private-school'])
            ->withCount(['institutions' => function ($q) use ($schoolType) {
                $q->published();
                if ($schoolType) {
                    $q->where('institution_type_id', $schoolType->id);
                }
            }])
            ->get();

        $provinces = Province::query()
            ->active()
            ->withDirectoryCounts()
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
            'term' => $term,
            'selectedCategory' => $category,
            'selectedProvince' => $province,
            'selectedDistrict' => $district,
            'selectedMunicipality' => $municipality,
            'sort' => $sort,
        ]);
    }

    public function colleges(Request $request): View
    {
        $collegeType = InstitutionType::query()->where('slug', 'college')->first();

        $province = Province::query()->active()->find($request->integer('province'));
        $district = District::query()->active()
            ->when($province, fn ($query) => $query->where('province_id', $province->id))
            ->find($request->integer('district'));
        $municipality = Municipality::query()->active()
            ->when($district, fn ($query) => $query->where('district_id', $district->id))
            ->find($request->integer('municipality'));

        $term = trim((string) $request->string('q'));
        $query = Institution::query()->published();
        if ($collegeType) {
            $query->where('institution_type_id', $collegeType->id);
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

        if ($term !== '') {
            $like = '%'.addcslashes($term, '%_\\').'%';
            $query->where('name', 'like', $like);
        }

        $colleges = (clone $query)
            ->with(['municipality', 'district', 'province', 'affiliations.university', 'programs'])
            ->orderBy('name')
            ->paginate(18)
            ->withQueryString();

        return view('public.colleges.index', [
            'colleges' => $colleges,
            'totalColleges' => (clone $query)->count(),
            'provinces' => Province::query()->active()->withCount([
                'institutions as colleges_count' => fn ($count) => $count->published()
                    ->when($collegeType, fn ($inner) => $inner->where('institution_type_id', $collegeType->id)),
            ])->orderBy('code')->get(),
            'term' => $term,
            'districts' => District::query()->active()->when($province, fn ($query) => $query->where('province_id', $province->id))->orderBy('name')->get(),
            'municipalities' => $district
                ? Municipality::query()->active()->where('district_id', $district->id)->orderBy('name')->get()
                : collect(),
            'selectedProvince' => $province,
            'selectedDistrict' => $district,
            'selectedMunicipality' => $municipality,
            'districtMap' => District::query()->active()->withCount([
                'institutions as colleges_count' => fn ($count) => $count->published()
                    ->when($collegeType, fn ($inner) => $inner->where('institution_type_id', $collegeType->id)),
            ])->orderBy('name')->get(),
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
            'provinces' => Province::query()->active()->withDirectoryCounts()->orderBy('code')->get(),
        ]);
    }

    public function province(Province $province): View
    {
        abort_unless($province->is_active, 404);

        $listed = $this->listedInstitutions(provinceId: $province->id);
        $province->loadCount('universities');
        $typeIds = InstitutionType::query()->whereIn('slug', ['school', 'college'])->pluck('id', 'slug');

        return view('public.places.province', [
            'province' => $province,
            'districts' => $province->districts()->active()->withCount([
                'municipalities',
                'universities',
                'institutions as schools_count' => fn ($query) => $query->where('status', InstitutionStatus::Published)->where('institution_type_id', $typeIds->get('school') ?? 0),
                'institutions as colleges_count' => fn ($query) => $query->where('status', InstitutionStatus::Published)->where('institution_type_id', $typeIds->get('college') ?? 0),
            ])->orderBy('name')->get(),
            'schoolCount' => $listed['schoolCount'],
            'collegeCount' => $listed['collegeCount'],
            'schools' => $listed['schools'],
            'colleges' => $listed['colleges'],
        ]);
    }

    public function district(District $district): View
    {
        abort_unless($district->is_active, 404);
        $district->load('province');

        $listed = $this->listedInstitutions(districtId: $district->id);

        return view('public.places.district', [
            'district' => $district,
            'municipalities' => $district->municipalities()->active()->orderBy('name')->get(),
            'schoolCount' => $listed['schoolCount'],
            'collegeCount' => $listed['collegeCount'],
            'schools' => $listed['schools'],
            'colleges' => $listed['colleges'],
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

    /**
     * @return array{schoolCount: int, collegeCount: int, schools: LengthAwarePaginator, colleges: LengthAwarePaginator}
     */
    private function listedInstitutions(?int $provinceId = null, ?int $districtId = null): array
    {
        $typeIds = InstitutionType::query()->whereIn('slug', ['school', 'college'])->pluck('id', 'slug');

        $base = Institution::query()->published()
            ->when($provinceId, fn ($query) => $query->where('province_id', $provinceId))
            ->when($districtId, fn ($query) => $query->where('district_id', $districtId));

        $page = function (string $slug, string $pageName) use ($base, $typeIds) {
            return (clone $base)
                ->when($typeIds->get($slug), fn ($query, $typeId) => $query->where('institution_type_id', $typeId))
                ->with(['type', 'district', 'municipality'])
                ->orderBy('name')
                ->paginate(12, ['*'], $pageName)
                ->withQueryString();
        };

        return [
            'schoolCount' => (clone $base)->when($typeIds->get('school'), fn ($query, $typeId) => $query->where('institution_type_id', $typeId))->count(),
            'collegeCount' => (clone $base)->when($typeIds->get('college'), fn ($query, $typeId) => $query->where('institution_type_id', $typeId))->count(),
            'schools' => $page('school', 'schools_page'),
            'colleges' => $page('college', 'colleges_page'),
        ];
    }

    public function show(Institution $institution): View
    {
        abort_unless($institution->status->value === 'published', 404);
        $institution->load([
            'type',
            'categories',
            'province',
            'district',
            'municipality',
            'ward',
            'contacts',
            'affiliations.university',
            'programs.university',
        ]);

        PageView::record($institution->type?->slug ?? '');

        $universityIds = $institution->affiliations->pluck('university_id')->filter()->unique()->values();
        $similar = collect();

        if ($institution->type?->slug === 'college') {
            $similar = Institution::query()
                ->where('status', InstitutionStatus::Published)
                ->where('institution_type_id', $institution->institution_type_id)
                ->whereKeyNot($institution->id)
                ->where(function ($query) use ($institution, $universityIds): void {
                    $query->where('municipality_id', $institution->municipality_id);

                    if ($universityIds->isNotEmpty()) {
                        $query->orWhereHas('affiliations', function ($affiliations) use ($universityIds): void {
                            $affiliations->whereIn('university_id', $universityIds);
                        });
                    }
                })
                ->with(['municipality', 'district', 'affiliations.university'])
                ->orderByRaw('case when municipality_id = ? then 0 else 1 end', [$institution->municipality_id])
                ->orderBy('name')
                ->limit(6)
                ->get();
        }

        return view('public.institutions.show', [
            'institution' => $institution,
            'similarColleges' => $similar,
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

    public function storeContact(StoreContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'subject' => $request->validated('subject'),
            'institution_name' => $request->validated('institution'),
            'message' => $request->validated('message'),
        ]);

        return redirect()
            ->route('contact')
            ->with('status', 'Your message was received. We aim to reply within 2–3 working days.');
    }

    public function blog(Request $request): View
    {
        $filePath = base_path('data/blogs.json');
        $posts = [];

        if (file_exists($filePath)) {
            $posts = json_decode(file_get_contents($filePath), true) ?: [];
        }

        $categories = collect($posts)->pluck('category')->unique()->sort()->values();
        $categoryCounts = collect($posts)->countBy('category');
        $perPage = 9;
        $page = max(1, $request->integer('page', 1));
        $blogs = new Paginator(
            array_slice($posts, ($page - 1) * $perPage, $perPage),
            count($posts),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('public.blog', [
            'blogs' => $blogs,
            'categories' => $categories,
            'categoryCounts' => $categoryCounts,
        ]);
    }
}
