@extends('layouts.public')

@section('title', config('app.name').' — Nepal Education Directory')
@section('meta_description', 'Search schools, colleges, universities, and training centers across Nepal\'s 7 provinces, 77 districts, and 753 local governments.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 1 — HERO
═══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 text-white">

    {{-- Animated gradient orbs --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="animate-pulse-slow absolute -top-32 -left-32 h-96 w-96 rounded-full bg-white/5 blur-3xl"></div>
        <div class="animate-pulse-slow absolute -bottom-32 -right-16 h-80 w-80 rounded-full bg-white/5 blur-3xl" style="animation-delay:1.5s"></div>
        <div class="absolute top-1/2 left-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-brand-600/20 blur-2xl"></div>
    </div>

    <div class="relative mx-auto max-w-6xl px-4 py-20 sm:py-28">
        {{-- Badge --}}
        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-100 backdrop-blur-sm">
            <span class="inline-block h-1.5 w-1.5 rounded-full bg-green-400"></span>
            Nepal's Free Education Directory
        </span>

        <h1 class="mt-5 max-w-3xl text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
            Find Schools, Colleges &amp; Universities in Nepal
        </h1>

        <p class="mt-5 max-w-2xl text-lg leading-relaxed text-brand-100">
            Browse {{ number_format($counts['institutions']) > 0 ? number_format($counts['institutions']).' published' : '' }} institutions across
            {{ $counts['provinces'] }} provinces, {{ $counts['districts'] }} districts, and
            {{ number_format($counts['municipalities']) }} local governments.
        </p>

        {{-- Search bar --}}
        <form action="{{ route('search') }}" method="get" role="search" class="mt-8 flex max-w-2xl flex-col gap-3 sm:flex-row">
            <label for="q" class="sr-only">Search institutions by name</label>
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                </span>
                <input id="q" name="q" type="search"
                    placeholder="Search by institution name…"
                    class="w-full rounded-xl border border-white/20 bg-white py-4 pl-12 pr-4 text-base text-slate-900 shadow-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-white/60">
            </div>
            <button type="submit"
                class="shrink-0 rounded-xl bg-white px-7 py-4 font-bold text-brand-800 shadow-lg transition hover:bg-brand-50 hover:shadow-xl active:scale-95">
                Search
            </button>
        </form>

        <p class="mt-4 text-sm text-brand-200">
            Only verified, published records appear in search results.
            <a href="{{ route('how-it-works') }}" class="ml-1 underline underline-offset-2 hover:text-white">How it works →</a>
        </p>

        {{-- Quick links --}}
        <div class="mt-8 flex flex-wrap gap-2">
            @forelse ($types->take(5) as $type)
                <a href="{{ route('types.show', $type) }}"
                    class="rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-sm font-semibold text-brand-100 backdrop-blur-sm transition hover:bg-white/20 hover:text-white">
                    {{ $type->name }}
                </a>
            @empty
            @endforelse
        </div>

    </div>
</section>

<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-12">
        <div class="flex flex-col gap-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-wider text-brand-800">Nepal Education Statistics</p>
                <h2 class="mt-2 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                    Nepal has a total of <span class="font-extrabold text-brand-700">35,951</span> schools across the country
                </h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600 sm:text-base">
                    According to official national education census records, this includes
                    <strong class="text-slate-800">25,623</strong> community (government) schools,
                    <strong class="text-slate-800">8,941</strong> institutional (private) schools, and
                    <strong class="text-slate-800">1,387</strong> traditional or religious schools.
                </p>
                <p class="mt-3 flex items-center gap-1.5 text-xs text-slate-500">
                    <span>Source:</span>
                    <a href="https://www.facebook.com/kathmandupost/posts/nepal-currently-has-35951-schools-of-these-25623-are-community-schools-8941-are-/1418073217020595/"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="font-semibold text-brand-700 underline underline-offset-2 hover:text-brand-900">
                        The Kathmandu Post
                    </a>
                </p>
            </div>
            <div class="grid shrink-0 grid-cols-3 gap-3 lg:w-96">
                <div class="rounded-xl border border-slate-200 bg-white p-3.5 text-center shadow-sm">
                    <p class="text-lg font-black text-brand-700 sm:text-xl">25,623</p>
                    <p class="mt-0.5 text-xs font-semibold uppercase tracking-tight text-slate-500">Community (Govt)</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-3.5 text-center shadow-sm">
                    <p class="text-lg font-black text-purple-700 sm:text-xl">8,941</p>
                    <p class="mt-0.5 text-xs font-semibold uppercase tracking-tight text-slate-500">Institutional (Pvt)</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-3.5 text-center shadow-sm">
                    <p class="text-lg font-black text-amber-700 sm:text-xl">1,387</p>
                    <p class="mt-0.5 text-xs font-semibold uppercase tracking-tight text-slate-500">Religious / Trad.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 2 — STATS BAR
═══════════════════════════════════════════════════════════════ --}}
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-12">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Provinces --}}
            <a href="{{ route('provinces.index') }}"
                class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-brand-600 hover:shadow-md">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700 ring-1 ring-brand-100 transition group-hover:bg-brand-700 group-hover:text-white">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497z"/>
                    </svg>
                </span>
                <div>
                    <p class="text-3xl font-extrabold text-slate-900">{{ number_format($counts['provinces']) }}</p>
                    <p class="mt-0.5 text-sm font-semibold text-slate-500">Provinces</p>
                </div>
            </a>

            {{-- Districts --}}
            <a href="{{ route('provinces.index') }}"
                class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-brand-600 hover:shadow-md">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600 ring-1 ring-purple-100 transition group-hover:bg-purple-600 group-hover:text-white">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                    </svg>
                </span>
                <div>
                    <p class="text-3xl font-extrabold text-slate-900">{{ number_format($counts['districts']) }}</p>
                    <p class="mt-0.5 text-sm font-semibold text-slate-500">Districts</p>
                </div>
            </a>

            {{-- Municipalities --}}
            <a href="{{ route('provinces.index') }}"
                class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-brand-600 hover:shadow-md">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600 ring-1 ring-green-100 transition group-hover:bg-green-600 group-hover:text-white">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
                    </svg>
                </span>
                <div>
                    <p class="text-3xl font-extrabold text-slate-900">{{ number_format($counts['municipalities']) }}</p>
                    <p class="mt-0.5 text-sm font-semibold text-slate-500">Local Governments</p>
                </div>
            </a>

            {{-- Institutions --}}
            <a href="{{ route('search') }}"
                class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-brand-600 hover:shadow-md">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-500 ring-1 ring-orange-100 transition group-hover:bg-orange-500 group-hover:text-white">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
                    </svg>
                </span>
                <div>
                    <p class="text-3xl font-extrabold text-slate-900">{{ number_format($counts['institutions']) }}</p>
                    <p class="mt-0.5 text-sm font-semibold text-slate-500">Published Institutions</p>
                </div>
            </a>
        </div>

    </div>
</section>

@if ($popularUniversities->isNotEmpty())
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">Higher education</p>
                <h2 class="mt-1 text-3xl font-extrabold text-slate-900">Popular universities</h2>
            </div>
            <a href="{{ route('universities.index') }}" class="shrink-0 text-sm font-semibold text-brand-700 hover:text-brand-800 hover:underline">
                View all universities
            </a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($popularUniversities as $university)
                <a href="{{ route('universities.show', $university) }}" class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-brand-300 hover:shadow-md">
                    @if ($university->logo_path)
                        <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white p-1.5">
                            <img src="{{ asset($university->logo_path) }}" alt="{{ $university->name }} logo" class="h-full w-full object-contain">
                        </div>
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-brand-700 to-brand-900 text-xl font-black text-white">
                            {{ strtoupper(substr($university->name, 0, 1)) }}
                        </div>
                    @endif
                    <h3 class="mt-4 text-base font-bold text-slate-900">{{ $university->name }}</h3>
                    <p class="mt-2 flex items-start gap-1.5 text-sm text-slate-500">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-700" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                        </svg>
                        <span>{{ $university->address ?: $university->location_label ?: 'Address not recorded' }}</span>
                    </p>
                    <dl class="mt-4 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-lg bg-slate-50 px-1 py-2">
                            <dt class="text-xs font-semibold uppercase tracking-tight text-slate-500">Faculties</dt>
                            <dd class="text-sm font-bold text-slate-900">{{ number_format($university->faculties_count) }}</dd>
                        </div>
                        <div class="rounded-lg bg-slate-50 px-1 py-2">
                            <dt class="text-xs font-semibold uppercase tracking-tight text-slate-500">Programs</dt>
                            <dd class="text-sm font-bold text-slate-900">{{ number_format($university->programs_count) }}</dd>
                        </div>
                        <div class="rounded-lg bg-slate-50 px-1 py-2">
                            <dt class="text-xs font-semibold uppercase tracking-tight text-slate-500">Colleges</dt>
                            <dd class="text-sm font-bold text-slate-900">{{ number_format($university->institutions_count) }}</dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-sm font-semibold text-brand-700">View details</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-6xl px-4">
        <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">School oversight</p>
        <h2 class="mt-1 text-3xl font-extrabold text-slate-900">NEB and PABSON</h2>
        <p class="mt-3 max-w-3xl text-slate-600">The National Examinations Board conducts school-level examinations. PABSON is the membership organization of private and boarding schools. They do different work, and neither replaces the local government that permits a school to operate.</p>
        <div class="mt-8 grid gap-6 sm:grid-cols-2">
            <article class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex h-24 w-full items-center justify-center rounded-xl bg-slate-50">
                    <img src="{{ asset('images/logos/neb.jpg') }}" alt="National Examinations Board logo" class="h-full w-full object-contain">
                </div>
                <h3 class="mt-4 h-14 text-xl font-bold text-slate-900">National Examinations Board</h3>
                <p class="mt-1 h-12 text-sm text-slate-500">राष्ट्रिय परीक्षा बोर्ड</p>
                <p class="mt-3 line-clamp-3 grow text-sm leading-relaxed text-slate-600">NEB runs the Grade 10 SEE and Grade 11–12 examinations. School permission and day-to-day operation sit with the local government.</p>
                <a href="{{ route('neb') }}" class="mt-5 inline-flex w-fit rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">About NEB</a>
            </article>
            <article class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex h-24 w-full items-center justify-center rounded-xl bg-slate-50">
                    <img src="{{ asset('images/logos/pabson.jpg') }}" alt="PABSON logo" class="h-full w-full object-contain">
                </div>
                <h3 class="mt-4 h-14 text-xl font-bold text-slate-900">PABSON</h3>
                <p class="mt-1 h-12 text-sm text-slate-500">Private and Boarding Schools' Organization Nepal</p>
                <p class="mt-3 line-clamp-3 grow text-sm leading-relaxed text-slate-600">PABSON is an umbrella body for member private and boarding schools. It works through its own constitution and elected committees.</p>
                <a href="{{ route('pabson') }}" class="mt-5 inline-flex w-fit rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">About PABSON</a>
            </article>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 3 — BROWSE BY INSTITUTION TYPE
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-brand-50 py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">What are you looking for?</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Browse by Institution Type</h2>
            <p class="mt-3 text-base text-slate-600">From pre-primary schools to research universities — find the right institution type.</p>
        </div>

        @php
        $typeIcons = [
            'school'       => ['emoji' => '🏫', 'bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'border' => 'border-blue-200'],
            'college'      => ['emoji' => '🎓', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
            'university'   => ['emoji' => '🏛️', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200'],
            'training'     => ['emoji' => '📚', 'bg' => 'bg-green-50',  'text' => 'text-green-700',  'border' => 'border-green-200'],
            'technical'    => ['emoji' => '⚙️', 'bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200'],
            'vocational'   => ['emoji' => '🔧', 'bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200'],
            'madrasa'      => ['emoji' => '📖', 'bg' => 'bg-teal-50',   'text' => 'text-teal-700',   'border' => 'border-teal-200'],
            'monastery'    => ['emoji' => '🕌', 'bg' => 'bg-rose-50',   'text' => 'text-rose-700',   'border' => 'border-rose-200'],
        ];
        $defaultIcon = ['emoji' => '🏢', 'bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200'];
        @endphp

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($featuredTypes as $type)
                @php
                    $key = strtolower(explode(' ', $type->name)[0]);
                    $icon = $typeIcons[$key] ?? $defaultIcon;
                @endphp
                <a href="{{ route('types.show', $type) }}"
                    class="group relative rounded-xl border {{ $icon['border'] }} {{ $icon['bg'] }} p-6 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                    <span class="text-4xl">{{ $icon['emoji'] }}</span>
                    <h3 class="mt-3 text-base font-bold {{ $icon['text'] }}">{{ $type->name }}</h3>
                    @if ($type->name_ne)
                        <p class="mt-0.5 text-sm text-slate-500">{{ $type->name_ne }}</p>
                    @endif
                    @if ($type->institutions_count > 0)
                        <p class="mt-2 text-xs font-semibold {{ $icon['text'] }} opacity-70">
                            {{ number_format($type->institutions_count) }} listed
                        </p>
                    @endif
                    <span class="absolute right-4 top-4 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-slate-500">→</span>
                </a>
            @empty
                <div class="col-span-4 rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                    <p class="text-2xl">📋</p>
                    <p class="mt-2 text-sm">Institution types have not been loaded yet. Run the classification seeder.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('types.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-brand-600 bg-white px-6 py-3 text-sm font-semibold text-brand-700 shadow-sm transition hover:bg-brand-50 hover:shadow-md">
                View all institution types
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 4 — BROWSE BY PROVINCE
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">All 7 Provinces</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Browse by Province</h2>
            <p class="mt-3 text-base text-slate-600">Nepal is divided into 7 provinces, each with multiple districts and local governments.</p>
        </div>

        @php
        $provinceGradients = [
            'from-blue-600 to-blue-800',
            'from-purple-600 to-purple-800',
            'from-green-600 to-green-800',
            'from-orange-500 to-orange-700',
            'from-red-600 to-red-800',
            'from-teal-600 to-teal-800',
            'from-indigo-600 to-indigo-800',
        ];
        @endphp

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($provinces as $index => $province)
                @php $gradient = $provinceGradients[$index % count($provinceGradients)]; @endphp
                <a href="{{ route('provinces.show', $province) }}"
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br {{ $gradient }} p-6 text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    {{-- Decorative circle --}}
                    <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10" aria-hidden="true"></div>

                    <span class="inline-block rounded-full border border-white/30 bg-white/20 px-2.5 py-0.5 text-xs font-bold tracking-widest">
                        P{{ $province->code }}
                    </span>
                    <h3 class="mt-3 text-lg font-bold leading-snug">{{ $province->name }}</h3>
                    @if ($province->name_ne)
                        <p class="mt-0.5 text-sm text-white/80">{{ $province->name_ne }}</p>
                    @endif
                    <p class="mt-3 text-sm font-semibold text-white/70">
                        {{ number_format($province->districts_count) }} {{ Str::plural('district', $province->districts_count) }}
                    </p>
                    <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-white/60 transition group-hover:gap-2 group-hover:text-white">
                        Explore <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </span>
                </a>
            @empty
                <div class="col-span-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-500">
                    <p class="text-2xl">🗺️</p>
                    <p class="mt-2 text-sm">Run the geography import to see provinces.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('provinces.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-brand-600 bg-white px-6 py-3 text-sm font-semibold text-brand-700 shadow-sm transition hover:bg-brand-50 hover:shadow-md">
                View all provinces &amp; districts
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 5 — EDUCATION LEVELS
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-brand-50 py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">From Nursery to Doctorate</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Education Levels in Nepal</h2>
            <p class="mt-3 text-base text-slate-600">Nepal's education system spans multiple levels following the national curriculum framework.</p>
        </div>

        <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @php
            $levels = [
                ['Pre-Primary (ECD)',      'बाल विकास केन्द्र',    '🌱', 'bg-pink-50 border-pink-200',    'text-pink-700',   'Age 3–5. Early childhood development centres across Nepal.'],
                ['Primary (Grades 1–5)',   'प्राथमिक तह',          '📗', 'bg-green-50 border-green-200',  'text-green-700',  'Foundation literacy and numeracy for young learners.'],
                ['Lower Secondary (6–8)',  'निम्न माध्यमिक तह',    '📘', 'bg-blue-50 border-blue-200',    'text-blue-700',   'Core subjects including science, math, social studies.'],
                ['Secondary (9–10)',       'माध्यमिक तह',          '📙', 'bg-orange-50 border-orange-200','text-orange-700', 'Leads to SEE (School Leaving Exam) certification.'],
                ['Higher Secondary (11–12)','उच्च माध्यमिक तह',   '📕', 'bg-red-50 border-red-200',      'text-red-700',    'Plus Two science, management, humanities, and education streams.'],
                ['Bachelor\'s Degree',    'स्नातक तह',             '🎓', 'bg-purple-50 border-purple-200','text-purple-700', '3–4 year undergraduate programs at colleges and campuses.'],
                ['Master\'s Degree',      'स्नातकोत्तर तह',        '🏅', 'bg-indigo-50 border-indigo-200','text-indigo-700', 'Postgraduate specialization across disciplines.'],
                ['Doctoral / M.Phil.',    'विद्यावारिधि तह',       '🔬', 'bg-teal-50 border-teal-200',   'text-teal-700',   'Advanced research degrees offered at universities.'],
                ['Technical / Vocational','प्राविधिक तह',          '⚙️', 'bg-yellow-50 border-yellow-200','text-yellow-700', 'TSLC, Diploma, and skill-based training programs.'],
            ];
            @endphp

            @foreach ($levels as [$name, $nepali, $emoji, $card, $text, $desc])
                <div class="flex items-start gap-4 rounded-xl border {{ $card }} bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                    <span class="mt-0.5 text-3xl">{{ $emoji }}</span>
                    <div>
                        <h3 class="font-bold {{ $text }}">{{ $name }}</h3>
                        <p class="text-xs text-slate-400">{{ $nepali }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('search') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 hover:shadow-md">
                Search institutions by level
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 6 — HOW IT WORKS
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">Simple to Use</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">How Shiksha Link Works</h2>
            <p class="mt-3 text-base text-slate-600">Find the right institution in three easy steps.</p>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-3">
            @php
            $steps = [
                [
                    'num'   => '01',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>',
                    'title' => 'Search or Browse',
                    'desc'  => 'Enter a school or college name in the search bar, or browse by institution type, province, district, or local government.',
                    'bg'    => 'bg-brand-50',
                    'ring'  => 'ring-brand-200',
                    'num_color' => 'text-brand-600',
                ],
                [
                    'num'   => '02',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>',
                    'title' => 'View Institution Details',
                    'desc'  => 'Each published institution record shows the full name, type, location (province, district, municipality), and contact details when available.',
                    'bg'    => 'bg-purple-50',
                    'ring'  => 'ring-purple-200',
                    'num_color' => 'text-purple-600',
                ],
                [
                    'num'   => '03',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>',
                    'title' => 'Explore by Location',
                    'desc'  => 'Use our geographic hierarchy — Province → District → Municipality — to discover all institutions in a specific area of Nepal.',
                    'bg'    => 'bg-green-50',
                    'ring'  => 'ring-green-200',
                    'num_color' => 'text-green-600',
                ],
            ];
            @endphp

            @foreach ($steps as $step)
                <div class="relative rounded-xl {{ $step['bg'] }} p-8 ring-1 {{ $step['ring'] }} shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                    <span class="text-5xl font-black {{ $step['num_color'] }} opacity-20 select-none">{{ $step['num'] }}</span>
                    <div class="mt-3 flex h-12 w-12 items-center justify-center rounded-xl bg-white shadow-sm ring-1 {{ $step['ring'] }}">
                        <svg class="h-6 w-6 {{ $step['num_color'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            {!! $step['icon'] !!}
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('how-it-works') }}"
                class="text-sm font-semibold text-brand-700 hover:text-brand-800 hover:underline">
                Read the full guide →
            </a>
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 7 — WHY USE SHIKSKHOJ
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-brand-50 py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">Why Shiksha Link?</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Built for Nepal's Education Community</h2>
            <p class="mt-3 text-base text-slate-600">A free, reliable, and comprehensive education directory for students, parents, and researchers.</p>
        </div>

        @php
        $features = [
            ['🗂️', 'Comprehensive Database',    'brand-50 border-brand-200',  'text-brand-700',   'Records covering schools, colleges, universities, technical institutes, and training centers from across all 7 provinces.'],
            ['✅', 'Verified Information',       'green-50 border-green-200',  'text-green-700',   'Each institution goes through a review and approval process before being published to the public directory.'],
            ['📍', 'Full Geographic Coverage',   'orange-50 border-orange-200','text-orange-700',  'Browse by province, district, or municipality. Every institution is tagged to Nepal\'s official geographic hierarchy.'],
            ['🆓', 'Completely Free to Access',  'blue-50 border-blue-200',    'text-blue-700',    'No registration required. All published institution records are freely accessible to anyone.'],
            ['🔄', 'Regularly Updated',          'purple-50 border-purple-200','text-purple-700',  'Our team continuously adds and updates institution records. New data is imported from official government sources.'],
            ['📊', 'Official Data Sources',      'teal-50 border-teal-200',   'text-teal-700',    'Geographic data sourced from the National Statistics Office. Institution data follows Ministry of Education classifications.'],
        ];
        @endphp

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($features as [$emoji, $title, $card, $text, $desc])
                <div class="rounded-xl border bg-{{ $card }} p-6 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                    <span class="text-3xl">{{ $emoji }}</span>
                    <h3 class="mt-3 text-base font-bold {{ $text }}">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 8 — FEATURED / RECENT INSTITUTIONS (conditional)
═══════════════════════════════════════════════════════════════ --}}
@if ($counts['institutions'] > 0)
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">Just Listed</p>
                <h2 class="mt-1 text-3xl font-extrabold text-slate-900">Published Institutions</h2>
            </div>
            <a href="{{ route('search') }}"
                class="shrink-0 text-sm font-semibold text-brand-700 hover:text-brand-800 hover:underline">
                Browse all →
            </a>
        </div>
        <p class="mt-2 text-sm text-slate-500">
            {{ number_format($counts['institutions']) }} institutions published across Nepal. Use search to find one near you.
        </p>
        <div class="mt-8 rounded-xl border border-brand-100 bg-brand-50 p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-brand-300" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
            </svg>
            <p class="mt-4 text-base font-semibold text-brand-800">
                {{ number_format($counts['institutions']) }} institutions ready to explore
            </p>
            <p class="mt-1 text-sm text-slate-500">Use the search to find schools, colleges, or universities by name.</p>
            <a href="{{ route('search') }}"
                class="mt-5 inline-flex items-center gap-2 rounded-lg bg-brand-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800">
                Search institutions
            </a>
        </div>

    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 9 — CALL TO ACTION
═══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 py-20 text-white">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -bottom-16 -right-16 h-72 w-72 rounded-full bg-white/5 blur-3xl"></div>
        <div class="absolute -top-16 -left-16 h-56 w-56 rounded-full bg-white/5 blur-3xl"></div>
    </div>
    <div class="relative mx-auto max-w-6xl px-4 text-center">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-100">
            Need Help?
        </span>
        <h2 class="mt-5 text-3xl font-extrabold sm:text-4xl">Can't find your institution?</h2>
        <p class="mt-4 text-lg text-brand-100">
            If a school, college, or training center is missing from our directory, let us know.
            We regularly add new institutions from official sources.
        </p>
        <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <a href="{{ route('contact') }}"
                class="rounded-xl bg-white px-8 py-4 font-bold text-brand-800 shadow-lg transition hover:bg-brand-50 hover:shadow-xl active:scale-95">
                Contact Us
            </a>
            <a href="{{ route('search') }}"
                class="rounded-xl border border-white/30 bg-white/10 px-8 py-4 font-semibold text-white backdrop-blur-sm transition hover:bg-white/20 hover:shadow-lg active:scale-95">
                Search All Institutions
            </a>
        </div>
        <p class="mt-6 text-sm text-brand-200">
            Data sourced from Nepal's National Statistics Office and Ministry of Education.
            <a href="{{ route('about') }}" class="ml-1 underline underline-offset-2 hover:text-white">Learn more →</a>
        </p>
    </div>
</section>

@push('styles')
<style>
@keyframes pulse-slow {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50%       { opacity: 1;   transform: scale(1.08); }
}
.animate-pulse-slow { animation: pulse-slow 6s ease-in-out infinite; }
</style>
@endpush

@endsection
