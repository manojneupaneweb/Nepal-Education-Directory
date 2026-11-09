@extends('layouts.public')

@section('title', config('app.name').' — Nepal Education Directory')
@section('meta_description', 'Search schools, colleges, universities, and training centers across Nepal\'s 7 provinces, 77 districts, and 753 local governments.')

@section('content')

{{-- ── HERO ────────────────────────────────────────────────────────────────── --}}
<section class="border-b border-blue-100 bg-surface-blue py-14 sm:py-20">
    <div class="section-container">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-700">Your Education Directory</p>
            <h1 class="mt-3 text-3xl font-bold leading-tight text-navy sm:text-5xl">
                Discover Education Opportunities Across Nepal
            </h1>
            <p class="mt-4 text-base leading-relaxed text-slate-600 sm:text-lg">
                Find schools, colleges, universities, and training centers in one place.
                @if ($counts['institutions'] > 0)
                    {{ number_format($counts['institutions']) }} published institutions
                @endif
                across {{ $counts['provinces'] }} provinces and {{ $counts['districts'] }} districts.
            </p>

            {{-- Search form --}}
            <form action="{{ route('search') }}" method="get" role="search" class="mt-8">
                <div class="flex max-w-xl gap-2">
                    <label for="q" class="sr-only">Search institutions by name</label>
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m20 20-4.2-4.2M16 10.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>
                        </span>
                        <input id="q" name="q" type="search"
                            placeholder="Search by institution name…"
                            class="form-input pl-9">
                    </div>
                    <button type="submit" class="btn-primary shrink-0">Search</button>
                </div>

                {{-- Quick type filters --}}
                @if ($types->isNotEmpty())
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($types->take(5) as $type)
                            <a href="{{ route('types.show', $type) }}"
                               class="inline-flex items-center rounded border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 transition hover:border-blue-900 hover:text-blue-900">
                                {{ $type->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </form>

            <p class="mt-4 text-sm text-slate-500">
                Published records appear in search results.
                <a href="{{ route('how-it-works') }}" class="ml-1 font-medium text-blue-900 underline underline-offset-2">How it works</a>
            </p>
        </div>
    </div>
</section>

{{-- ── STATS STRIP ─────────────────────────────────────────────────────────── --}}
<section class="border-b border-slate-200 bg-white" aria-label="Directory statistics">
    <div class="section-container">
        <dl class="grid grid-cols-2 divide-x divide-y divide-slate-200 sm:grid-cols-4 sm:divide-y-0">
            @php
                $stats = [
                    [$counts['institutions'], 'Published Institutions', route('search')],
                    [$counts['provinces'],    'Provinces',              route('provinces.index')],
                    [$counts['districts'],    'Districts',              route('provinces.index')],
                    [$counts['municipalities'], 'Local Governments',   route('provinces.index')],
                ];
            @endphp
            @foreach ($stats as [$num, $lbl, $link])
                <div class="px-6 py-6 text-center">
                    <dt class="stat-label text-xs font-medium uppercase tracking-wider text-slate-500">{{ $lbl }}</dt>
                    <dd>
                        <a href="{{ $link }}" class="stat-number count-up" data-count="{{ $num }}">
                            {{ number_format($num) }}
                        </a>
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>

{{-- ── FEATURED TYPES ───────────────────────────────────────────────────────── --}}
@if (isset($featuredTypes) && $featuredTypes->isNotEmpty())
<section class="py-14">
    <div class="section-container">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="section-title">Browse by Institution Type</h2>
                <p class="section-desc">Find schools, colleges, universities, and specialized institutes across Nepal.</p>
            </div>
            <a href="{{ route('types.index') }}" class="shrink-0 text-sm font-medium text-blue-900 underline-offset-2 hover:underline">View all institution types</a>
        </div>
        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            @foreach ($featuredTypes as $type)
                @php
                    $tone = match ($type->slug) {
                        'college' => '#EA580C',
                        'university' => '#7C3AED',
                        default => '#0F766E',
                    };
                @endphp
                <a href="{{ $type->slug === 'university' ? route('universities.index') : route('types.show', $type) }}" class="card group flex min-h-52 flex-col justify-between border-transparent p-6" style="background: {{ $tone }}14">
                    <div>
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl text-white transition group-hover:scale-105" style="background: {{ $tone }}">
                            @if ($type->slug === 'college')
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                            @elseif ($type->slug === 'university')
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                            @else
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18"/></svg>
                            @endif
                        </div>
                        <p class="mt-5 text-lg font-semibold text-slate-900">{{ $type->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">
                            @if ($type->slug === 'college')
                                Higher secondary and bachelor colleges.
                            @elseif ($type->slug === 'university')
                                Universities, faculties, and their programs.
                            @else
                                Primary and secondary schools across Nepal.
                            @endif
                        </p>
                    </div>
                    <p class="mt-4 text-sm font-semibold" style="color: {{ $tone }}">{{ number_format($type->institutions_count ?? 0) }} {{ $type->slug === 'university' ? 'universities' : 'institutions' }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── POPULAR UNIVERSITIES ─────────────────────────────────────────────────── --}}
@if (isset($popularUniversities) && $popularUniversities->isNotEmpty())
<section class="border-t border-slate-200 bg-white py-14">
    <div class="section-container">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="section-title">Universities</h2>
                <p class="section-desc">Nepal's major universities and their affiliated colleges.</p>
            </div>
            <a href="{{ route('universities.index') }}" class="shrink-0 text-sm font-medium text-blue-900 underline-offset-2 hover:underline">View all universities</a>
        </div>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($popularUniversities as $university)
                @php
                    $logo = $university->logo_path
                        ? (str_starts_with($university->logo_path, 'http') ? $university->logo_path : asset($university->logo_path))
                        : null;
                @endphp
                <a href="{{ route('universities.show', $university) }}" class="card group p-5">
                    @if ($logo)
                        <img src="{{ $logo }}" alt="" width="48" height="48" loading="lazy" decoding="async" class="h-12 w-12 rounded-xl border border-slate-200 bg-white object-contain p-1">
                    @else
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-sm font-bold text-blue-900">{{ strtoupper(substr($university->abbreviation ?: $university->name, 0, 1)) }}</span>
                    @endif
                    <p class="mt-3 font-semibold text-slate-900 leading-snug">{{ $university->name }}</p>
                    @if ($university->abbreviation)
                        <p class="mt-1 text-xs font-medium text-slate-500">{{ $university->abbreviation }}</p>
                    @endif
                    @if ($university->faculties->isNotEmpty())
                        <p class="mt-2 text-xs leading-5 text-slate-600">{{ $university->faculties->take(3)->pluck('name')->join(', ') }}@if ($university->faculties_count > 3), +{{ $university->faculties_count - 3 }} more @endif</p>
                    @endif
                </a>
            @endforeach
        </div>
        @if (isset($featuredColleges) && $featuredColleges->isNotEmpty())
            <div class="mt-12 text-center">
                <h3 class="text-2xl font-bold text-slate-900">Featured Colleges</h3>
                <span class="mx-auto mt-2 block h-0.5 w-10 rounded bg-blue-600"></span>
                <p class="mt-3 text-sm text-slate-500">Hand-picked institutions standing out for quality and reputation.</p>
            </div>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredColleges as $college)
                    @php
                        $logo = $college->logo_path
                            ? (str_starts_with($college->logo_path, 'http') ? $college->logo_path : asset($college->logo_path))
                            : null;
                        $place = collect([$college->municipality?->name, $college->district?->name])->filter()->join(', ');
                    @endphp
                    <a href="{{ route('institutions.show', $college) }}" class="card group flex flex-col p-4">
                        <span class="flex items-center gap-3">
                            @if ($logo)
                                <img src="{{ $logo }}" alt="" width="48" height="48" loading="lazy" decoding="async" class="h-12 w-12 shrink-0 rounded-lg border border-slate-100 bg-white object-contain p-1">
                            @else
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-blue-900">{{ strtoupper(substr($college->name, 0, 1)) }}</span>
                            @endif
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-slate-900">{{ $college->name }}</span>
                                @if ($place !== '')
                                    <span class="mt-1 flex items-center gap-1 text-xs text-slate-500">
                                        <svg class="h-3.5 w-3.5 shrink-0 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25a7.5 7.5 0 0 0-7.5 7.5c0 5.25 7.5 12 7.5 12s7.5-6.75 7.5-12a7.5 7.5 0 0 0-7.5-7.5Zm0 10.125a2.625 2.625 0 1 1 0-5.25 2.625 2.625 0 0 1 0 5.25Z"/></svg>
                                        <span class="truncate">{{ $place }}</span>
                                    </span>
                                @endif
                            </span>
                        </span>
                        <span class="mt-4 flex items-center justify-between">
                            <span class="flex gap-0.5 text-slate-300" aria-hidden="true">
                                @for ($star = 0; $star < 5; $star++)
                                    <svg class="h-4 w-4 {{ $college->is_featured ? 'text-blue-600' : '' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.28 3.95a1 1 0 0 0 .95.69h4.15c.97 0 1.37 1.24.59 1.81l-3.36 2.44a1 1 0 0 0-.36 1.12l1.28 3.95c.3.92-.76 1.69-1.54 1.12l-3.36-2.44a1 1 0 0 0-1.18 0l-3.36 2.44c-.78.57-1.84-.2-1.54-1.12l1.28-3.95a1 1 0 0 0-.36-1.12L2.08 9.38c-.78-.57-.38-1.81.59-1.81h4.15a1 1 0 0 0 .95-.69l1.28-3.95Z"/></svg>
                                @endfor
                            </span>
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0-2.186m0 2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/></svg>
                        </span>
                    </a>
                @endforeach
            </div>
            <p class="mt-4 text-center">
                <a href="{{ route('colleges.index') }}" class="text-sm font-medium text-blue-900 underline-offset-2 hover:underline">View all colleges →</a>
            </p>
        @endif
    </div>
</section>
@endif

{{-- ── PROVINCES ────────────────────────────────────────────────────────────── --}}
@if (isset($provinces) && $provinces->isNotEmpty())
<section class="border-t border-slate-200 bg-[#f7f4ef] py-14">
    <div class="section-container">
        <div class="overflow-hidden rounded-2xl border border-[#123B70]/15 bg-white shadow-sm">
            <div class="flex flex-col gap-4 px-5 py-6 sm:flex-row sm:items-end sm:justify-between sm:px-7">
                <div>
                    <h2 class="text-2xl font-bold text-[#123B70]">Browse by Province</h2>
                    <p class="mt-1 text-sm text-slate-600">सात प्रदेश — Koshi, Madhesh, Bagmati, Gandaki, Lumbini, Karnali, and Sudurpashchim.</p>
                </div>
                <a href="{{ route('provinces.index') }}" class="shrink-0 text-sm font-semibold text-[#003893] underline-offset-2 hover:underline">सबै प्रदेश · View all →</a>
            </div>
            <div class="grid gap-3 px-5 pb-6 sm:grid-cols-2 sm:px-7 md:grid-cols-3 lg:grid-cols-7">
                @forelse ($provinces as $province)
                    <a href="{{ route('provinces.show', $province) }}" class="group overflow-hidden rounded-xl border border-slate-200 bg-[#fbfaf8] text-center transition hover:border-[#003893]/40 hover:shadow-sm">
                        <div class="px-3 py-4">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-[#003893]">Province</p>
                            <p class="mt-1 font-serif text-2xl font-bold text-[#123B70]">{{ $loop->iteration }}</p>
                            <p class="mt-1 text-sm font-semibold leading-snug text-slate-900">{{ $province->name }}</p>
                            @if ($province->name_ne)
                                <p class="mt-0.5 text-xs text-slate-600 ne">{{ $province->name_ne }}</p>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                        Run the geography import to see provinces.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── COLLEGE STREAMS ──────────────────────────────────────────────────────── --}}
@if (isset($collegeStreams) && $collegeStreams->isNotEmpty())
<section class="border-t border-slate-200 bg-white py-14">
    <div class="section-container">
        <h2 class="section-title">All Colleges in Nepal</h2>
        <p class="section-desc">Browse colleges by stream and discover the right path for your future.</p>
        <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($collegeStreams as $stream)
                <a href="{{ route('search', ['q' => $stream->name]) }}" class="card group flex items-center gap-3 p-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl" style="background: {{ $stream->color }}1a; color: {{ $stream->color }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $stream->icon_path }}"/></svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate font-semibold text-slate-900">{{ $stream->name }}</span>
                        <span class="mt-0.5 block text-xs font-medium" style="color: {{ $stream->color }}">{{ number_format($stream->matched_colleges_count) }} Colleges</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── FEATURES ─────────────────────────────────────────────────────────────── --}}
<section class="relative overflow-hidden border-t border-slate-200 bg-white py-16">
    <div class="pointer-events-none absolute -left-16 top-10 h-40 w-40 rounded-full bg-blue-100/70 blur-2xl"></div>
    <div class="pointer-events-none absolute -right-10 bottom-0 h-48 w-48 rounded-full bg-red-100/60 blur-2xl"></div>
    <div class="section-container relative">
        <h2 class="section-title">Why Use Shiksha Link?</h2>
        <p class="section-desc">A comprehensive, unbiased directory built for students, parents, and researchers.</p>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['Free & Open Access',      'No registration, no paywalls. All institution data is freely accessible to everyone.',           'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z'],
                ['No Paid Rankings',         'Institutions are listed on factual data only. No sponsored placements or paid promotions.',     'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                ['Reviewed Records',         'Every published record has been reviewed. Draft and pending entries are never shown publicly.',  'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z'],
                ['Complete Coverage',        'Schools, colleges, universities, technical institutes, and training centers from all 7 provinces.', 'M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z'],
                ['Geographic Depth',         'Browse by province, district, or local government to find institutions near any location.',      'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z'],
                ['Official Data Sources',    'Data sourced from the National Statistics Office and Ministry of Education, Science and Technology.', 'M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z'],
            ] as [$title, $desc, $icon])
                <div class="why-card rounded-2xl border border-slate-200 bg-white p-5" style="animation-delay: {{ $loop->index * 90 }}ms">
                    <div class="why-icon flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-900 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-slate-900">{{ $title }}</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── CTA ───────────────────────────────────────────────────────────────────── --}}
@if ($counts['institutions'] > 0)
<section class="border-t border-blue-100 bg-blue-50 py-14">
    <div class="section-container text-center">
        <h2 class="section-title">Can't find your institution?</h2>
        <p class="section-desc mx-auto max-w-2xl">
            If a school, college, or training center is missing, let us know. We regularly add new institutions from official sources.
        </p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a href="{{ route('suggestions.create') }}" class="btn-primary">Suggest an institution</a>
            <a href="{{ route('search') }}" class="btn-outline">Search all {{ number_format($counts['institutions']) }} institutions</a>
        </div>
        <p class="mt-6 text-xs text-slate-500">
            Data sourced from Nepal's National Statistics Office and Ministry of Education.
            <a href="{{ route('about') }}" class="ml-1 underline underline-offset-2 hover:text-slate-700">Learn more →</a>
        </p>
    </div>
</section>
@endif

@endsection
