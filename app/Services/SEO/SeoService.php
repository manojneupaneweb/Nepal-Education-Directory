<?php

namespace App\Services\Seo;

use App\Enums\InstitutionStatus;
use App\Models\Institution;
use App\Models\Program;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeoService
{
    public function forView(array $data, Request $request): SeoMeta
    {
        $route = $request->route()?->getName();

        if ($route === 'institutions.show' && isset($data['institution']) && $data['institution'] instanceof Institution) {
            return $this->forInstitution($data['institution']);
        }

        if (isset($data['university']) && $data['university'] instanceof University && $route === 'universities.show') {
            return $this->forUniversity($data['university']);
        }

        if (isset($data['program']) && $data['program'] instanceof Program) {
            return $this->forProgram($data['program']);
        }

        if ($route === 'universities.colleges' && isset($data['university']) && $data['university'] instanceof University) {
            $meta = $this->page(
                'Colleges affiliated with '.$data['university']->name,
                'Colleges connected to '.$data['university']->name.' in the Nepal education directory.',
                route('universities.colleges', $data['university']),
            );
            $total = isset($data['colleges']) && method_exists($data['colleges'], 'total') ? $data['colleges']->total() : 0;
            if ($total === 0) {
                $meta->robots = 'noindex, follow';
            }

            return $meta;
        }

        if ($route === 'university-programs.university' && isset($data['university']) && $data['university'] instanceof University) {
            return $this->page(
                $data['university']->name.' programs',
                'Academic programs listed for '.$data['university']->name.'.',
                route('university-programs.university', str_ends_with($data['university']->slug, '-university') ? Str::beforeLast($data['university']->slug, '-university') : $data['university']->slug),
            );
        }

        $meta = $this->staticPage($route, $data);
        if (in_array($route, ['schools.index', 'colleges.index'], true)) {
            $page = $request->integer('page');
            if ($page > 1) {
                $meta->robots = 'noindex, follow';
                $meta->canonical = $request->url().'?page='.$page;
            }
            if ($this->listingHasFilters($request)) {
                $meta->robots = 'noindex, follow';
                $meta->canonical = $page > 1 ? $request->url().'?page='.$page : $request->url();
            }
        }

        return $meta;
    }

    public function forInstitution(Institution $institution): SeoMeta
    {
        $place = collect([
            $this->shortPlace($institution->municipality?->name),
            $institution->district?->name,
        ])->filter()->implode(', ');
        $titlePlace = $this->shortPlace($institution->municipality?->name);
        $title = $institution->name.($titlePlace ? ', '.$titlePlace : '');
        $facts = collect([
            $institution->address ? 'location' : null,
            $institution->type?->name,
            $institution->official_website ? 'official website' : null,
            $institution->contacts->contains(fn ($contact) => $contact->contact_type->value === 'phone') ? 'contact details' : null,
        ])->filter()->implode(', ');
        $description = 'Explore '.$institution->name.($place !== '' ? ' in '.$place : '').'.';
        if ($facts !== '') {
            $description .= ' Available information includes '.$facts.'.';
        }

        $type = match ($institution->type?->slug) {
            'school' => 'School',
            'college', 'university' => 'CollegeOrUniversity',
            default => 'EducationalOrganization',
        };
        $url = route('institutions.show', $institution);
        $image = $this->absoluteImage($institution->logo_path);
        $sameAs = $this->sameAs($institution);
        $address = array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $this->nullable($institution->address),
            'addressLocality' => $this->nullable($institution->municipality?->name),
            'addressRegion' => $this->nullable($institution->district?->name),
            'addressCountry' => ($institution->address || $institution->municipality || $institution->district) ? 'NP' : null,
        ], fn ($value) => $value !== null && $value !== '');

        return new SeoMeta(
            title: $title,
            description: $description,
            canonical: $url,
            robots: $this->isIndexable($institution) ? 'index, follow' : 'noindex, follow',
            image: $image,
            breadcrumbs: array_values(array_filter([
                ['name' => 'Home', 'url' => route('home')],
                $institution->type?->slug === 'school' ? ['name' => 'Schools', 'url' => route('schools.index')] : null,
                $institution->type?->slug === 'college' ? ['name' => 'Colleges', 'url' => route('colleges.index')] : null,
                $institution->province ? ['name' => $institution->province->name, 'url' => route('provinces.show', $institution->province)] : null,
                $institution->district ? ['name' => $institution->district->name, 'url' => route('districts.show', $institution->district)] : null,
                ['name' => $institution->name, 'url' => $url],
            ])),
            jsonLd: [$this->prune([
                '@context' => 'https://schema.org',
                '@type' => $type,
                'name' => $institution->name,
                'url' => $url,
                'description' => $description,
                'address' => count($address) > 1 ? $address : null,
                'logo' => $image,
                'sameAs' => $sameAs !== [] ? $sameAs : null,
            ])],
        );
    }

    /**
     * A published named institution is indexable when it has one quality signal.
     * A website is a signal, not a requirement. Name and address alone are not enough.
     */
    public function isIndexable(Institution $institution): bool
    {
        if ($institution->status !== InstitutionStatus::Published || trim($institution->name) === '') {
            return false;
        }

        $minimum = (int) config('seo.quality.min_description_length', 80);
        if (mb_strlen(trim((string) $institution->description)) >= $minimum) {
            return true;
        }

        if ($this->absoluteHttp($institution->official_website) !== null) {
            return true;
        }

        $contacts = $institution->relationLoaded('contacts')
            ? $institution->contacts
            : $institution->contacts()->get();
        if ($contacts->contains(fn ($contact) => in_array($contact->contact_type->value, ['phone', 'email'], true) && trim((string) $contact->contact_value) !== '')) {
            return true;
        }

        $programs = $institution->relationLoaded('programs') ? $institution->programs : $institution->programs()->get(['programs.id']);
        if ($programs->isNotEmpty()) {
            return true;
        }

        $stored = $institution->website_sections['programs'] ?? [];

        return is_array($stored) && collect($stored)->filter(fn ($value) => trim((string) (is_array($value) ? ($value['program_name'] ?? $value['name'] ?? '') : $value)) !== '')->isNotEmpty();
    }

    public function forUniversity(University $university): SeoMeta
    {
        $url = route('universities.show', $university);
        $description = trim((string) $university->description) !== ''
            ? Str::limit(trim(preg_replace('/\s+/', ' ', (string) $university->description)), 180, '')
            : $university->name.' is listed in the Nepal university directory, including faculties and academic programs where available.';

        return new SeoMeta(
            title: $university->name,
            description: $description,
            canonical: $url,
            image: $this->absoluteImage($university->logo_path),
            breadcrumbs: [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Universities', 'url' => route('universities.index')],
                ['name' => $university->name, 'url' => $url],
            ],
            jsonLd: [[
                '@context' => 'https://schema.org',
                '@type' => 'CollegeOrUniversity',
                'name' => $university->name,
                'url' => $url,
                'description' => $description,
            ]],
        );
    }

    public function forProgram(Program $program): SeoMeta
    {
        $url = route('university-programs.show', $program);
        $university = $program->university?->name;
        $description = $program->name.($university ? ' at '.$university : '').'.';
        if (filled($program->duration)) {
            $description .= ' Duration: '.$program->duration.'.';
        }

        return new SeoMeta(
            title: $program->name.($university ? ' — '.$university : ''),
            description: $description,
            canonical: $url,
            breadcrumbs: [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Programs', 'url' => route('university-programs.index')],
                ['name' => $program->name, 'url' => $url],
            ],
            jsonLd: [[
                '@context' => 'https://schema.org',
                '@type' => 'Course',
                'name' => $program->name,
                'description' => $description,
                'url' => $url,
                'provider' => $university ? ['@type' => 'CollegeOrUniversity', 'name' => $university] : null,
            ]],
        );
    }

    private function staticPage(?string $route, array $data): SeoMeta
    {
        $site = config('app.name');
        $pages = [
            'home' => ['Schools and Colleges in Nepal', 'Search schools, colleges, and universities across Nepal. Compare institutions by province, district, and local government.', true],
            'schools.index' => ['Schools in Nepal', 'Browse published schools in Nepal by province, district, and local government.', false],
            'colleges.index' => ['Colleges in Nepal', 'Browse published colleges in Nepal, including affiliation and location where available.', false],
            'universities.index' => ['Universities in Nepal', 'Browse universities in Nepal and the colleges and programs connected to them.', false],
            'university-programs.index' => ['University Programs in Nepal', 'Browse academic programs offered by universities in Nepal.', false],
            'provinces.index' => ['Provinces of Nepal', 'Browse educational institutions by Nepal\'s seven provinces.', false],
            'types.index' => ['Institution Types', 'Browse schools, colleges, universities, and other education providers in Nepal.', false],
            'about' => ['About', 'Shikshya Sanjal is an education directory for students, parents, and institutions in Nepal.', false],
            'how-it-works' => ['How It Works', 'How institution records are published and updated in the Nepal education directory.', false],
            'contact' => ['Contact', 'Contact the Shikshya Sanjal education directory.', false],
            'blog' => ['Blog', 'Notes and updates from the Nepal education directory.', false],
            'neb' => ['National Examinations Board', 'Information about the National Examinations Board in the education directory.', false],
            'pabson' => ['PABSON', 'Information about PABSON in the education directory.', false],
            'suggestions.create' => ['Suggest an Institution', 'Suggest a school, college, or other educational institution for review.', false],
            'search' => ['Search', 'Search the Nepal education directory.', false],
            'login' => ['Sign in', 'Sign in to the directory administration area.', false],
        ];

        if (isset($data['province']) && $route === 'provinces.show') {
            $name = $data['province']->name;

            return $this->page($name.' Schools and Colleges', 'Educational institutions in '.$name.', Nepal, grouped by district.', route('provinces.show', $data['province']));
        }

        if (isset($data['district']) && $route === 'districts.show') {
            $name = $data['district']->name;

            return $this->page('Schools and Colleges in '.$name, 'Schools and colleges in '.$name.' district, Nepal.', route('districts.show', $data['district']));
        }

        if (isset($data['municipality']) && $route === 'municipalities.show') {
            $name = $data['municipality']->name;
            $meta = $this->page('Schools and Colleges in '.$name, 'Educational institutions in '.$name.', Nepal.', route('municipalities.show', [$data['district'], $data['municipality']]));
            $total = isset($data['institutions']) && method_exists($data['institutions'], 'total') ? $data['institutions']->total() : 0;
            if ($total === 0) {
                $meta->robots = 'noindex, follow';
            }

            return $meta;
        }

        if (isset($data['type']) && $route === 'types.show') {
            $name = $data['type']->name;
            $meta = $this->page($name.' in Nepal', 'Browse '.$name.' records in the Nepal education directory.', route('types.show', $data['type']));
            if (in_array($data['type']->slug, ['school', 'college'], true)) {
                $meta->canonical = route($data['type']->slug === 'school' ? 'schools.index' : 'colleges.index');
                $meta->robots = 'noindex, follow';
            }

            return $meta;
        }

        [$title, $description] = $pages[$route] ?? ['Nepal Education Directory', 'An education directory for schools, colleges, and universities in Nepal.'];
        $noindex = in_array($route, ['search', 'login', 'suggestions.create'], true);
        $canonical = $route ? route($route) : url('/');
        $jsonLd = [];
        if ($route === 'home') {
            $jsonLd[] = [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $site,
                'url' => route('home'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => route('search').'?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ];
            $jsonLd[] = [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => $site,
                'url' => route('home'),
            ];
        }

        return new SeoMeta(
            title: $title,
            description: $description,
            canonical: $canonical,
            robots: $noindex ? 'noindex, follow' : 'index, follow',
            image: asset('images/logo.jpg'),
            jsonLd: $jsonLd,
        );
    }

    private function page(string $title, string $description, string $canonical): SeoMeta
    {
        return new SeoMeta($title, $description, $canonical, image: asset('images/logo.jpg'));
    }

    private function listingHasFilters(Request $request): bool
    {
        if (! in_array($request->route()?->getName(), ['schools.index', 'colleges.index', 'search'], true)) {
            return false;
        }

        return collect($request->query())->except('page')->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty();
    }

    private function cleanListingUrl(Request $request): string
    {
        $page = $request->integer('page');

        return $page > 1 ? $request->url().'?page='.$page : $request->url();
    }

    private function shortPlace(?string $name): ?string
    {
        if ($name === null || trim($name) === '') {
            return null;
        }

        return trim((string) preg_replace('/\s+(metropolitan city|metropolitian city|sub-metropolitan city|urban municipality|rural municipality)$/i', '', $name));
    }

    private function absoluteImage(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : asset($path);
    }

    /**
     * @return list<string>
     */
    private function sameAs(Institution $institution): array
    {
        $urls = [$institution->official_website];
        $contacts = $institution->relationLoaded('contacts') ? $institution->contacts : $institution->contacts()->get();
        foreach ($contacts as $contact) {
            if (in_array($contact->contact_type->value, ['website', 'facebook', 'instagram', 'youtube'], true)) {
                $urls[] = $contact->contact_value;
            }
        }

        return collect($urls)
            ->map(fn ($url) => $this->absoluteHttp($url))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function absoluteHttp(mixed $url): ?string
    {
        $url = trim((string) $url);

        return filter_var($url, FILTER_VALIDATE_URL) && str_starts_with($url, 'http') ? $url : null;
    }

    private function nullable(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array<string, mixed>
     */
    private function prune(array $value): array
    {
        $pruned = [];
        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $item = $this->prune($item);
                if ($item === []) {
                    continue;
                }
            }
            if ($item === null || $item === '') {
                continue;
            }
            $pruned[$key] = $item;
        }

        return $pruned;
    }
}
