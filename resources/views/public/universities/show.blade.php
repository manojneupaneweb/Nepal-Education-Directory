@extends('layouts.public')

@php
    $programPath = str_ends_with($university->slug, '-university')
        ? \Illuminate\Support\Str::beforeLast($university->slug, '-university')
        : $university->slug;
    $place = collect([$university->address, $university->municipality?->name, $university->district?->name, $university->province?->name])->filter()->unique()->implode(', ');
    $established = collect([
        $university->established_year_ad ? $university->established_year_ad.' AD' : null,
        $university->established_year_bs ? $university->established_year_bs.' BS' : null,
    ])->filter()->implode(' · ');
@endphp

@section('title', ($university->seo_title ?: $university->name).' — '.config('app.name'))
@section('meta_description', $university->meta_description ?: $university->description)
@section('canonical', route('universities.show', $university))

@push('meta')
    <meta property="og:title" content="{{ $university->seo_title ?: $university->name }}">
    <meta property="og:description" content="{{ $university->meta_description ?: $university->description }}">
    <meta property="og:url" content="{{ route('universities.show', $university) }}">
    <meta property="og:type" content="website">
    @if ($university->logo_path)
        <meta property="og:image" content="{{ asset($university->logo_path) }}">
    @endif
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'CollegeOrUniversity',
            'name' => $university->name,
            'url' => $university->official_website,
            'address' => $university->address ?: $university->location_label,
            'logo' => $university->logo_path ? asset($university->logo_path) : null,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')
<section class="bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 py-10 text-white">
    <div class="mx-auto max-w-6xl px-4">
        <nav class="text-sm text-brand-100" aria-label="Breadcrumb">
            <a class="hover:text-white" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true"> / </span>
            <a class="hover:text-white" href="{{ route('universities.index') }}">Universities</a>
            <span aria-hidden="true"> / </span>
            <span class="text-white">{{ $university->name }}</span>
        </nav>

        <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center">
            @if ($university->logo_path)
                <img src="{{ asset($university->logo_path) }}" alt="{{ $university->name }} logo" class="h-24 w-24 shrink-0 rounded-2xl border border-white bg-white object-contain p-2 shadow-md">
            @else
                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-2xl bg-white text-3xl font-black text-brand-800" aria-hidden="true">{{ strtoupper(substr($university->name, 0, 1)) }}</div>
            @endif
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-brand-100">{{ $university->abbreviation ?: $university->university_type }}</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">{{ $university->name }}</h1>
                @if ($university->name_ne)
                    <p class="mt-1 text-lg text-brand-100">{{ $university->name_ne }}</p>
                @endif
            </div>
        </div>

        @if ($university->description)
            <p class="mt-5 max-w-3xl text-base leading-relaxed text-brand-100">{{ $university->description }}</p>
        @endif

        <div class="mt-6 flex flex-wrap gap-3">
            @if ($university->official_website)
                <a href="{{ $university->official_website }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-brand-800 shadow-sm hover:bg-brand-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c2.5-2.5 3.5-5.5 3.5-9S14.5 5.5 12 3m0 18c-2.5-2.5-3.5-5.5-3.5-9S9.5 5.5 12 3m-9 9h18"/></svg>
                    Visit Official Website
                </a>
            @endif
            <a href="{{ route('university-programs.university', $programPath) }}" class="inline-flex items-center gap-2 rounded-lg border border-white px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75v10.5m5.25-5.25H6.75"/></svg>
                View Academic Programs
            </a>
        </div>

        <section class="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([
                ['Faculties', $university->faculties->count()],
                ['Programs', $university->programs->count()],
                ['Colleges', $university->institutions->count()],
                ['Established', $established ?: 'Not recorded'],
            ] as [$label, $value])
                <div class="rounded-2xl bg-white p-4 shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-extrabold text-brand-800">{{ $value }}</p>
                </div>
            @endforeach
        </section>
    </div>
</section>

<div class="mx-auto grid max-w-6xl items-start gap-8 px-4 py-10 lg:grid-cols-3">
    <div class="space-y-8 lg:col-span-2">
    @if (filled($university->history))
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>About</h2>
            <div class="mt-3 space-y-3 text-sm leading-relaxed text-slate-700">
                @foreach (preg_split("/\r\n|\n/", $university->history) as $line)
                    @if (filled(trim($line)))
                        <p>{{ trim($line) }}</p>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
    @include('public.universities.partials.section', ['title' => 'Vision and mission', 'body' => collect([$university->vision, $university->mission, $university->objectives])->filter()->implode("\n\n")])

    @if ($university->leaders->isNotEmpty())
        <section>
            <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Leadership</h2>
            <ul class="mt-3 grid gap-3 sm:grid-cols-2">
                @foreach ($university->leaders as $leader)
                    <li class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                        <p class="font-semibold text-slate-900">{{ $leader->name }}</p>
                        <p class="text-slate-500">{{ $leader->designation }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section>
        <div class="flex items-end justify-between gap-3">
            <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Faculties</h2>
            <a class="text-sm font-semibold text-brand-700" href="{{ route('university-programs.university', $programPath) }}">All programs</a>
        </div>
        @if ($university->faculties->isEmpty())
            <p class="mt-2 text-sm text-slate-600">No faculty list is stored yet.</p>
        @else
            <ul class="mt-3 grid gap-3 sm:grid-cols-2">
                @foreach ($university->faculties as $faculty)
                    <li class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="font-semibold text-slate-900">{{ $faculty->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $university->programs->where('university_faculty_id', $faculty->id)->count() }} programs</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section id="programs">
        <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Academic programs</h2>
        @if ($university->programs->isEmpty())
            <p class="mt-2 text-sm text-slate-600">No academic programs are stored yet. A program is added only when a public source names it.</p>
        @else
            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                @foreach ($university->programs->take(12) as $program)
                    <a class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:border-brand-300" href="{{ route('university-programs.show', $program) }}">
                        <span class="font-semibold text-slate-900">{{ $program->name }}</span>
                        <span class="mt-1 block text-sm text-slate-500">{{ $program->faculty?->name ?: 'Faculty not recorded' }}@if ($program->duration) · {{ $program->duration }}@endif</span>
                        @if ($program->program_status === 'not_offered')
                            <span class="mt-2 inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Not currently offered</span>
                        @endif
                    </a>
                @endforeach
            </div>
            @if ($university->programs->count() > 12)
                <a class="mt-4 inline-flex text-sm font-semibold text-brand-700" href="{{ route('university-programs.university', $programPath) }}">View all {{ $university->programs->count() }} programs</a>
            @endif
        @endif
    </section>

    @if ($university->institutions_count || $university->ugc_constituent_colleges || $university->ugc_affiliated_colleges)
        <section>
            <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Campuses and colleges</h2>
            @php
                $collegeCounts = collect([
                    $university->ugc_constituent_colleges ? $university->ugc_constituent_colleges.' constituent' : null,
                    $university->ugc_affiliated_colleges ? $university->ugc_affiliated_colleges.' affiliated' : null,
                    $university->ugc_community_colleges ? $university->ugc_community_colleges.' community' : null,
                ])->filter();
            @endphp
            @if ($collegeCounts->isNotEmpty())
                <p class="mt-2 text-sm text-slate-600">UGC count on file: {{ $collegeCounts->implode(', ') }}.</p>
            @endif
            <p class="mt-2 text-sm text-slate-600">{{ number_format($university->institutions_count) }} colleges in this directory name {{ $university->name }}.</p>
            @if ($university->institutions_count)
                <a href="{{ route('universities.colleges', $university) }}" class="mt-4 inline-flex items-center rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white">View campuses and colleges</a>
            @endif
        </section>
    @endif

    @include('public.universities.partials.section', ['title' => 'Admission', 'body' => $university->admission_information])
    @include('public.universities.partials.section', ['title' => 'Research', 'body' => $university->research_summary])

    @if ($university->facilities)
        <section>
            <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Campus contacts</h2>
            <ul class="mt-3 grid gap-3 sm:grid-cols-2">
                @foreach (preg_split("/\r\n|\n/", $university->facilities) as $line)
                    @if (filled(trim($line)))
                        <li class="rounded-2xl border border-slate-200 bg-white p-4 text-sm shadow-sm">
                            <p class="font-semibold text-slate-900">{{ \Illuminate\Support\Str::before($line, ' — ') }}</p>
                            @if (str_contains($line, ' — '))
                                <p class="mt-1 text-slate-600">{{ \Illuminate\Support\Str::after($line, ' — ') }}</p>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </section>
    @endif
    </div>
    <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:sticky lg:top-24">
        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">At a glance</h2>
        <dl class="mt-4 space-y-4 text-sm">
            @if ($place)
                <div>
                    <dt class="font-semibold text-slate-500">Address</dt>
                    <dd class="mt-1 text-slate-900">{{ $place }}</dd>
                </div>
            @endif
            @if ($university->phone)
                <div>
                    <dt class="font-semibold text-slate-500">Phone</dt>
                    <dd class="mt-1 text-slate-900">{{ $university->phone }}</dd>
                </div>
            @endif
            @if ($university->email)
                <div>
                    <dt class="font-semibold text-slate-500">Email</dt>
                    <dd class="mt-1"><a class="font-semibold text-brand-700" href="mailto:{{ $university->email }}">{{ $university->email }}</a></dd>
                </div>
            @endif
            @if ($university->university_type)
                <div>
                    <dt class="font-semibold text-slate-500">Type</dt>
                    <dd class="mt-1 text-slate-900">{{ $university->university_type }}@if ($university->ownership) · {{ ucfirst($university->ownership) }}@endif</dd>
                </div>
            @endif
            @if ($university->official_website)
                <div>
                    <dt class="font-semibold text-slate-500">Website</dt>
                    <dd class="mt-1 break-all"><a class="font-semibold text-brand-700" href="{{ $university->official_website }}" target="_blank" rel="noopener noreferrer">{{ parse_url($university->official_website, PHP_URL_HOST) }}</a></dd>
                </div>
            @endif
        </dl>
    </aside>
</div>
@endsection
