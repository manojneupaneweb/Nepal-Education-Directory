@extends('layouts.public')

@php
    $typeSlug = $institution->type?->slug;
    $indexRoute = match ($typeSlug) {
        'school' => route('schools.index'),
        'college' => route('colleges.index'),
        default => route('home'),
    };
    $indexLabel = match ($typeSlug) {
        'school' => 'Schools',
        'college' => 'Colleges',
        default => 'Directory',
    };
    $place = collect([$institution->municipality?->name, $institution->district?->name, $institution->province?->name])->filter()->implode(', ');
    $clean = fn (?string $text): string => trim(preg_replace('/\s+/', ' ', html_entity_decode(str_replace('&nbsp;', ' ', (string) $text), ENT_QUOTES | ENT_HTML5)));
    $paragraphs = collect(preg_split("/\r\n|\n/", (string) $institution->description))
        ->map(fn ($line) => $clean($line))
        ->filter(fn ($line) => strlen($line) > 40)
        ->unique()
        ->values();
    $people = $institution->contacts->filter(fn ($contact) => str_contains((string) $contact->label, 'Named'));
    $channels = $institution->contacts
        ->reject(fn ($contact) => $people->contains($contact))
        ->unique(function ($contact) use ($institution) {
            $value = strtolower($contact->contact_value);
            if ($contact->contact_type->value === 'phone') {
                $value = preg_replace('/\D+/', '', $value);
            }
            if (str_starts_with($value, 'http') && $institution->official_website && parse_url($value, PHP_URL_HOST) === parse_url($institution->official_website, PHP_URL_HOST)) {
                return 'website';
            }

            return $contact->contact_type->value.'|'.$value;
        })
        ->values();
    $sections = collect($institution->website_sections ?? []);
    $offeredPrograms = collect($sections->get('programs') ?? [])->filter();
    $facts = collect([
        ['Established', $institution->established_year],
        ['Ownership', $institution->ownership_type?->value && $institution->ownership_type->value !== 'unknown' ? ucfirst(str_replace('_', ' ', $institution->ownership_type->value)) : null],
        ['Programs', $institution->programs->count() ?: ($offeredPrograms->count() ?: null)],
    ])->filter(fn ($fact) => filled($fact[1]));
    $logo = $institution->logo_path
        ? (str_starts_with($institution->logo_path, 'http') ? $institution->logo_path : asset($institution->logo_path))
        : null;
@endphp

@section('title', $institution->name.' — '.config('app.name'))
@section('meta_description', $paragraphs->first() ?: $institution->name.' is listed in the '.config('app.name').' education directory.')
@section('canonical', route('institutions.show', $institution))

@push('meta')
    <meta property="og:title" content="{{ $institution->name }}">
    <meta property="og:description" content="{{ $paragraphs->first() }}">
    <meta property="og:url" content="{{ route('institutions.show', $institution) }}">
    <meta property="og:type" content="website">
    @if ($logo)
        <meta property="og:image" content="{{ $logo }}">
    @endif
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $institution->name,
            'description' => $paragraphs->implode(' '),
            'url' => url()->current(),
            'logo' => $logo,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $institution->address,
                'addressLocality' => $institution->municipality?->name,
                'addressRegion' => $institution->district?->name,
                'addressCountry' => 'NP',
            ],
            'sameAs' => $institution->official_website,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')
    <section class="border-b border-slate-200 bg-white py-8">
        <div class="mx-auto max-w-6xl px-4">
            <nav class="text-sm text-slate-500" aria-label="Breadcrumb">
                <a class="hover:text-blue-800" href="{{ route('home') }}">Home</a>
                <span aria-hidden="true"> / </span>
                <a class="hover:text-blue-800" href="{{ $indexRoute }}">{{ $indexLabel }}</a>
                <span aria-hidden="true"> / </span>
                <span class="text-slate-900">{{ $institution->name }}</span>
            </nav>

            <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center">
                @if ($logo)
                    <img src="{{ $logo }}" alt="" class="h-20 w-20 shrink-0 rounded-full border border-slate-200 bg-white object-contain p-1 shadow-sm">
                @else
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-blue-50 text-2xl font-black text-blue-900" aria-hidden="true">{{ strtoupper(substr($institution->name, 0, 1)) }}</div>
                @endif
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-700">{{ $institution->type?->name }}</p>
                    <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $institution->name }}</h1>
                    @if ($institution->name_ne)
                        <p class="mt-1 text-lg text-slate-600">{{ $institution->name_ne }}</p>
                    @endif
                    <p class="mt-2 text-sm text-slate-600">{{ $institution->address ?: $place }}</p>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap gap-2 text-sm font-semibold">
                <a href="#about" class="rounded-full bg-slate-100 px-3 py-1.5 text-slate-700 hover:bg-blue-50 hover:text-blue-800">About</a>
                <a href="#programs" class="rounded-full bg-slate-100 px-3 py-1.5 text-slate-700 hover:bg-blue-50 hover:text-blue-800">Programs</a>
                <a href="#gallery" class="rounded-full bg-slate-100 px-3 py-1.5 text-slate-700 hover:bg-blue-50 hover:text-blue-800">Gallery</a>
                <a href="#contact" class="rounded-full bg-blue-700 px-3 py-1.5 text-white hover:bg-blue-800">Contact</a>
                @if ($institution->official_website)
                    <a href="{{ $institution->official_website }}" target="_blank" rel="noopener noreferrer" class="rounded-full border border-slate-300 px-3 py-1.5 text-slate-700 hover:border-blue-300">Official website</a>
                @endif
            </div>

            @if ($facts->isNotEmpty())
                <dl class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($facts as [$label, $value])
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                            <dd class="mt-1 text-lg font-bold text-slate-900">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>
    </section>

    <div class="mx-auto grid max-w-6xl items-start gap-8 px-4 py-10 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @php
                $isPublicLine = fn (?string $line): bool => $clean($line) !== ''
                    && ! preg_match('/this pass|stored on this college|record had no website|was not used|not re-checked|directory page, not the campus/i', $clean($line));
                $aboutLines = collect($sections->get('about') ?? [])->map(fn ($line) => $clean($line))->filter(fn ($line) => strlen($line) > 40 && $isPublicLine($line))->values();
                $shownAbout = $aboutLines->count() > $paragraphs->count() ? $aboutLines : $paragraphs->filter(fn ($line) => $isPublicLine($line))->values();
            @endphp
            @if ($shownAbout->isNotEmpty())
                <section id="about" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>About</h2>
                    <div class="mt-3 space-y-3 text-sm leading-relaxed text-slate-700">
                        @foreach ($shownAbout as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($offeredPrograms->isNotEmpty() && $institution->programs->isEmpty())
                <section id="programs" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Offered programs <span class="text-sm font-semibold text-slate-500">{{ $offeredPrograms->count() }}</span></h2>
                    <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($offeredPrograms as $programName)
                            <li class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Program</p>
                                <p class="mt-1 text-sm font-bold leading-snug text-slate-900">{{ $programName }}</p>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @foreach ([
                'mission' => 'Mission and vision',
                'highlights' => 'Highlights',
                'features' => 'Why this college',
                'study_system' => 'Study system',
                'admissions' => 'Admission',
                'guidelines' => 'Admission process',
                'scholarships' => 'Scholarships and financial support',
                'facilities' => 'Facilities and infrastructure',
                'faculty' => 'Teaching faculty and learning',
                'student_life' => 'Student life and activities',
                'placement' => 'Placement and career cell',
                'awards' => 'Achievements and milestones',
                'chairman' => 'Message from the principal and chairperson',
                'social' => 'Social links',
                'reviews' => 'Reviews',
                'location_note' => 'Location',
            ] as $key => $title)
                @php $lines = collect($sections->get($key) ?? [])->map(fn ($line) => $clean($line))->filter(fn ($line) => $isPublicLine($line))->values(); @endphp
                @if ($lines->isNotEmpty())
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>{{ $title }}</h2>
                        @if ($key === 'chairman')
                            <blockquote class="mt-4 border-l-4 border-brand-700 bg-brand-50 px-4 py-3 text-sm leading-relaxed text-slate-800">
                                @foreach ($lines as $line)
                                    <p @class(['mt-3' => ! $loop->first])>{{ $line }}</p>
                                @endforeach
                            </blockquote>
                        @else
                            <div class="mt-3 space-y-3 text-sm leading-relaxed text-slate-700">
                                @foreach ($lines as $line)
                                    <p>{{ $line }}</p>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif
            @endforeach

            @php $gallery = collect($sections->get('gallery') ?? [])->filter(); @endphp
            @if ($gallery->isNotEmpty())
                <section id="gallery">
                    <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Gallery</h2>
                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($gallery as $image)
                            <img src="{{ $image }}" alt="" class="h-28 w-full rounded-xl border border-slate-200 object-cover">
                        @endforeach
                    </div>
                </section>
            @endif

            @php $videos = collect($sections->get('videos') ?? [])->filter(); @endphp
            @if ($videos->isNotEmpty())
                <section>
                    <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Videos</h2>
                    <ul class="mt-3 space-y-2">
                        @foreach ($videos as $video)
                            <li><a class="text-sm font-semibold text-brand-700" href="{{ $video }}" rel="noopener noreferrer" target="_blank">{{ $video }}</a></li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($people->isNotEmpty())
                <section>
                    <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>People named on the website</h2>
                    <ul class="mt-3 grid gap-3 sm:grid-cols-2">
                        @foreach ($people as $person)
                            @php
                                [$title, $name] = array_pad(explode(': ', $person->contact_value, 2), 2, $person->contact_value);
                            @endphp
                            <li class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                                <p class="font-semibold text-slate-900">{{ $name }}</p>
                                <p class="text-slate-500">{{ $title }}</p>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($institution->programs->isNotEmpty())
                <section>
                    <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Programs</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        @foreach ($institution->programs as $program)
                            <a class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:border-brand-300" href="{{ route('university-programs.show', $program) }}">
                                <span class="font-semibold text-slate-900">{{ $program->name }}</span>
                                @if ($program->university)
                                    <span class="mt-1 block text-sm text-slate-500">{{ $program->university->name }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($institution->affiliations->isNotEmpty())
                <section>
                    <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>University</h2>
                    <ul class="mt-3 grid gap-3 sm:grid-cols-2">
                        @foreach ($institution->affiliations as $affiliation)
                            @if ($affiliation->university)
                                <li>
                                    <a class="block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:border-brand-300" href="{{ route('universities.show', $affiliation->university) }}">
                                        <span class="font-semibold text-slate-900">{{ $affiliation->university->name }}</span>
                                        <span class="mt-1 block text-sm text-slate-500">{{ ucfirst($affiliation->affiliation_type->value) }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </section>
            @endif

            @if (($similarColleges ?? collect())->isNotEmpty())
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Similar colleges</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($similarColleges as $college)
                            @php
                                $logo = $college->logo_path
                                    ? (str_starts_with($college->logo_path, 'http') ? $college->logo_path : asset($college->logo_path))
                                    : null;
                                $university = $college->affiliations->first()?->university;
                            @endphp
                            <a href="{{ route('institutions.show', $college) }}" class="flex gap-3 rounded-2xl border border-slate-200 p-3 hover:border-brand-300">
                                @if ($logo)
                                    <img src="{{ $logo }}" alt="" class="h-12 w-12 shrink-0 rounded-xl border border-slate-200 bg-white object-contain p-1">
                                @else
                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-700 text-lg font-black text-white">{{ mb_strtoupper(mb_substr($college->name, 0, 1)) }}</span>
                                @endif
                                <span class="min-w-0">
                                    <span class="block font-bold leading-snug text-slate-900">{{ $college->name }}</span>
                                    <span class="mt-1 block text-xs text-slate-500">{{ $college->municipality?->name }}</span>
                                    @if ($university)
                                        <span class="mt-1 block truncate text-xs font-semibold text-brand-800">{{ $university->name }}</span>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <aside class="space-y-6 lg:sticky lg:top-24">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">Location</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $institution->address ?: $place }}</p>
                <ul class="mt-3 space-y-1 text-sm">
                    @if ($institution->province)
                        <li><a class="font-semibold text-brand-700" href="{{ route('provinces.show', $institution->province) }}">{{ $institution->province->name }}</a></li>
                    @endif
                    @if ($institution->district)
                        <li><a class="font-semibold text-brand-700" href="{{ route('districts.show', $institution->district) }}">{{ $institution->district->name }}</a></li>
                    @endif
                </ul>
            </section>

            @if ($channels->isNotEmpty() || $institution->official_website)
            <section id="contact" class="rounded-2xl bg-[#123B70] p-5 text-white shadow-sm">
                <h2 class="text-lg font-bold">Contact</h2>
                    <ul class="mt-3 space-y-3">
                        @if ($institution->official_website)
                            <li>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-200">Website</p>
                                <a class="mt-1 block break-all text-sm font-semibold text-white" href="{{ $institution->official_website }}" rel="noopener noreferrer" target="_blank">{{ parse_url($institution->official_website, PHP_URL_HOST) ?: $institution->official_website }}</a>
                            </li>
                        @endif
                        @php
                            $extraText = collect($sections->get('extra') ?? [])->map(fn ($line) => $clean($line))->implode(' ');
                            preg_match_all('/(?:\+977[-\s]?)?(?:0\d{1,2}[-\s]?\d{6,8}|9\d{9})/', $extraText, $phoneMatches);
                            preg_match_all('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $extraText, $emailMatches);
                            $sidebarPhones = collect($phoneMatches[0] ?? [])->unique()->values();
                            $sidebarEmails = collect($emailMatches[0] ?? [])->unique()->values();
                        @endphp
                        @foreach ($sidebarPhones as $phone)
                            <li>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-200">Phone</p>
                                <a class="mt-1 block text-sm font-semibold text-white" href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a>
                            </li>
                        @endforeach
                        @foreach ($sidebarEmails as $email)
                            <li>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-200">Email</p>
                                <a class="mt-1 block break-all text-sm font-semibold text-white" href="mailto:{{ $email }}">{{ $email }}</a>
                            </li>
                        @endforeach
                        @foreach ($channels as $contact)
                            <li>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-200">{{ match (true) {
                                    $contact->contact_type->value === 'phone' => 'Phone',
                                    $contact->contact_type->value === 'email' => 'Email',
                                    $contact->contact_type->value === 'facebook' => 'Facebook',
                                    $contact->contact_type->value === 'instagram' => 'Instagram',
                                    $contact->contact_type->value === 'youtube' => 'YouTube',
                                    str_contains(strtolower($contact->contact_value), 'linkedin') => 'LinkedIn',
                                    str_contains(strtolower($contact->contact_value), 'tiktok') => 'TikTok',
                                    default => ucfirst(str_replace('_', ' ', $contact->contact_type->value)),
                                } }}</p>
                                @if (str_starts_with($contact->contact_value, 'http'))
                                    <a class="mt-1 block break-all text-sm font-semibold text-white" href="{{ $contact->contact_value }}" rel="noopener noreferrer" target="_blank">{{ parse_url($contact->contact_value, PHP_URL_HOST) ?: 'Open link' }}</a>
                                @elseif ($contact->contact_type->value === 'email')
                                    <a class="mt-1 block break-all text-sm font-semibold text-white" href="mailto:{{ $contact->contact_value }}">{{ $contact->contact_value }}</a>
                                @elseif ($contact->contact_type->value === 'phone')
                                    <a class="mt-1 block text-sm font-semibold text-white" href="tel:{{ preg_replace('/\s+/', '', $contact->contact_value) }}">{{ $contact->contact_value }}</a>
                                @else
                                    <p class="mt-1 text-sm text-blue-100">{{ $contact->contact_value }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($institution->categories->isNotEmpty())
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">Categories</h2>
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach ($institution->categories as $category)
                            <li class="rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-800">{{ $category->name }}</li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </aside>
    </div>
@endsection
