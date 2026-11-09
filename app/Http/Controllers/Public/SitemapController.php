<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Municipality;
use App\Models\Program;
use App\Models\Province;
use App\Models\University;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $maps = ['pages', 'schools', 'colleges', 'universities', 'programs', 'locations'];

        return $this->xml('public.sitemap-index', ['maps' => $maps]);
    }

    public function section(string $name): Response
    {
        abort_unless(in_array($name, ['pages', 'schools', 'colleges', 'universities', 'programs', 'locations'], true), 404);

        $urls = Cache::remember('seo.sitemap.'.$name, now()->addHours(6), fn () => $this->urls($name));

        return $this->xml('public.sitemap', ['urls' => $urls]);
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /login',
            'Disallow: /search',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * @return list<array{loc: string, lastmod: ?string}>
     */
    private function urls(string $name): array
    {
        return match ($name) {
            'pages' => $this->staticUrls(),
            'schools' => $this->institutionUrls('school'),
            'colleges' => $this->institutionUrls('college'),
            'universities' => $this->universityUrls(),
            'programs' => $this->programUrls(),
            'locations' => $this->locationUrls(),
        };
    }

    /**
     * @return list<array{loc: string, lastmod: ?string}>
     */
    private function staticUrls(): array
    {
        return collect([
            'home', 'schools.index', 'colleges.index', 'universities.index', 'university-programs.index',
            'provinces.index', 'types.index', 'about', 'how-it-works', 'contact', 'blog', 'neb', 'pabson',
        ])->map(fn (string $route) => ['loc' => route($route), 'lastmod' => null])->all();
    }

    /**
     * @return list<array{loc: string, lastmod: ?string}>
     */
    private function institutionUrls(string $type): array
    {
        $typeId = InstitutionType::query()->where('slug', $type)->value('id');
        if ($typeId === null) {
            return [];
        }

        return Institution::query()
            ->published()
            ->where('institution_type_id', $typeId)
            ->orderBy('id')
            ->get(['slug', 'updated_at'])
            ->map(fn (Institution $institution) => [
                'loc' => route('institutions.show', $institution->slug),
                'lastmod' => $institution->updated_at?->toDateString(),
            ])
            ->all();
    }

    /**
     * @return list<array{loc: string, lastmod: ?string}>
     */
    private function universityUrls(): array
    {
        return University::query()->orderBy('id')->get(['slug', 'updated_at'])->flatMap(function (University $university) {
            return [
                ['loc' => route('universities.show', $university->slug), 'lastmod' => $university->updated_at?->toDateString()],
                ['loc' => route('universities.colleges', $university->slug), 'lastmod' => $university->updated_at?->toDateString()],
            ];
        })->all();
    }

    /**
     * @return list<array{loc: string, lastmod: ?string}>
     */
    private function programUrls(): array
    {
        return Program::query()->whereNotNull('university_id')->orderBy('id')->get(['slug', 'updated_at'])
            ->map(fn (Program $program) => [
                'loc' => route('university-programs.show', $program->slug),
                'lastmod' => $program->updated_at?->toDateString(),
            ])->all();
    }

    /**
     * @return list<array{loc: string, lastmod: ?string}>
     */
    private function locationUrls(): array
    {
        $provinces = Province::query()->active()->orderBy('code')->get(['slug', 'updated_at'])
            ->map(fn (Province $province) => ['loc' => route('provinces.show', $province->slug), 'lastmod' => $province->updated_at?->toDateString()]);
        $districts = District::query()->active()->orderBy('name')->get(['slug', 'updated_at'])
            ->map(fn (District $district) => ['loc' => route('districts.show', $district->slug), 'lastmod' => $district->updated_at?->toDateString()]);
        $municipalities = Municipality::query()->active()->whereHas('institutions', fn ($query) => $query->published())->with('district:id,slug')->orderBy('name')->get()
            ->map(fn (Municipality $municipality) => [
                'loc' => route('municipalities.show', [$municipality->district->slug, $municipality->slug]),
                'lastmod' => $municipality->updated_at?->toDateString(),
            ]);

        return $provinces->concat($districts)->concat($municipalities)->all();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function xml(string $view, array $data): Response
    {
        return response(view($view, $data)->render(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
