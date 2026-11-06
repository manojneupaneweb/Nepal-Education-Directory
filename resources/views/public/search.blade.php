@extends('layouts.public')

@section('title', ($term !== '' ? '"'.$term.'" — ' : '').'Search Institutions — '.config('app.name'))
@section('meta_description', 'Search published schools, colleges, universities, and training centers in Nepal.')

@section('content')

{{-- Search header --}}
<section class="border-b border-slate-200 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 text-white">
    <div class="mx-auto max-w-6xl px-4 py-14">
        <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Search Institutions</h1>
        <p class="mt-2 text-brand-100">Find schools, colleges, universities, and training centers across Nepal.</p>

        <form action="{{ route('search') }}" method="get" role="search" class="mt-6 flex max-w-2xl flex-col gap-3 sm:flex-row">
            <label for="q" class="sr-only">Institution name</label>
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                </span>
                <input id="q" name="q" type="search" value="{{ $term }}"
                    placeholder="Institution name…"
                    autofocus
                    class="w-full rounded-xl border border-white/20 bg-white py-4 pl-12 pr-4 text-base text-slate-900 shadow-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-white/60">
            </div>
            <button type="submit"
                class="shrink-0 rounded-xl bg-white px-7 py-4 font-bold text-brand-800 shadow-lg transition hover:bg-brand-50 active:scale-95">
                Search
            </button>
        </form>

        @if ($term !== '')
            <p class="mt-3 text-sm text-brand-200">
                Showing results for <strong class="text-white">"{{ $term }}"</strong>
                — <a href="{{ route('search') }}" class="underline underline-offset-2 hover:text-white">Clear search</a>
            </p>
        @endif
    </div>
</section>

{{-- Results area --}}
<div class="mx-auto max-w-6xl px-4 py-10">
    <div class="flex flex-col gap-8 lg:flex-row">

        {{-- Sidebar — filter hints --}}
        <aside class="shrink-0 lg:w-64">
            <div class="sticky top-20 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-widest text-slate-500">Browse Instead</h2>

                <div class="mt-4 space-y-1">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mt-3 mb-1">By Type</p>
                    <a href="{{ route('types.index') }}"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-brand-50 hover:text-brand-700">
                        🏫 All institution types
                        <svg class="ml-auto h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>

                <div class="mt-4 space-y-1">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1">By Location</p>
                    <a href="{{ route('provinces.index') }}"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-brand-50 hover:text-brand-700">
                        🗺️ Browse provinces
                        <svg class="ml-auto h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>

                <div class="mt-5 rounded-lg border border-brand-100 bg-brand-50 p-4 text-sm">
                    <p class="font-semibold text-brand-800">Search Tips</p>
                    <ul class="mt-2 space-y-1.5 text-xs text-slate-600">
                        <li>• Try partial names (e.g., "Tribhuvan")</li>
                        <li>• Only published records appear</li>
                        <li>• Browse by type or province if search shows no results</li>
                    </ul>
                </div>
            </div>
        </aside>

        {{-- Results --}}
        <section class="flex-1" aria-label="Search results">
            @if ($institutions->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                    <span class="text-5xl">🔍</span>
                    <h2 class="mt-4 text-xl font-bold text-slate-800">
                        @if ($term === '')
                            No institutions published yet.
                        @else
                            No results for "{{ $term }}"
                        @endif
                    </h2>
                    <p class="mt-2 text-sm text-slate-500">
                        @if ($term === '')
                            Run the institution import to populate the directory.
                        @else
                            Try a different spelling, or browse by
                            <a href="{{ route('types.index') }}" class="font-semibold text-brand-700 hover:underline">institution type</a>
                            or
                            <a href="{{ route('provinces.index') }}" class="font-semibold text-brand-700 hover:underline">province</a>.
                        @endif
                    </p>
                </div>
            @else
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm text-slate-500">
                        Found <strong class="text-slate-800">{{ number_format($institutions->total()) }}</strong> institution{{ $institutions->total() === 1 ? '' : 's' }}
                        @if ($term !== '') for <span class="text-brand-700">"{{ $term }}"</span>@endif
                    </p>
                </div>

                <ul class="space-y-3">
                    @foreach ($institutions as $institution)
                        <li>
                            <a href="{{ route('institutions.show', $institution) }}"
                                class="group flex items-start justify-between gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md">
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-brand-800 group-hover:text-brand-700 truncate">
                                        {{ $institution->name }}
                                    </h3>
                                    <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-sm text-slate-500">
                                        @if ($institution->type)
                                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700">
                                                {{ $institution->type->name }}
                                            </span>
                                        @endif
                                        @if ($institution->municipality)
                                            <span>📍 {{ $institution->municipality->name }}</span>
                                        @endif
                                        @if ($institution->district)
                                            <span class="text-slate-400">{{ $institution->district->name }}</span>
                                        @endif
                                    </p>
                                </div>
                                <svg class="mt-1 h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                                </svg>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-8">{{ $institutions->withQueryString()->links() }}</div>
            @endif
        </section>

    </div>
</div>

@endsection
