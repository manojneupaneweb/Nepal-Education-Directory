@extends('layouts.public')

@section('title', ($term !== '' ? '"'.$term.'" — ' : '').'Search Institutions — '.config('app.name'))
@section('meta_description', 'Search published schools, colleges, universities, and training centers in Nepal.')

@section('content')

{{-- Search header --}}
<section class="border-b border-slate-200 bg-blue-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
            <a class="transition hover:text-blue-700" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-slate-900">Search</span>
        </nav>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Search Institutions</h1>
        <p class="mt-2 text-slate-600">Find schools, colleges, universities, and training centers across Nepal.</p>

        <form action="{{ route('search') }}" method="get" role="search" class="mt-6 flex max-w-2xl flex-col gap-3 sm:flex-row">
            <label for="q" class="sr-only">Institution name</label>
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                </span>
                <input id="q" name="q" type="search" value="{{ $term }}"
                    placeholder="Institution name…"
                    autofocus
                    class="form-input pl-10">
            </div>
            <button type="submit" class="btn-primary shrink-0">Search</button>
        </form>

        @if ($term !== '')
            <p class="mt-3 text-sm text-slate-600">
                Showing results for <strong class="text-slate-900">"{{ $term }}"</strong>
                — <a href="{{ route('search') }}" class="text-blue-700 underline hover:no-underline">Clear search</a>
            </p>
        @endif
    </div>
</section>

{{-- Results area --}}
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-8 lg:flex-row">

        {{-- Sidebar — filter hints --}}
        <aside class="shrink-0 lg:w-60">
            <div class="sticky top-20 rounded border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xs font-bold uppercase tracking-widest text-slate-500">Browse Instead</h2>

                <div class="mt-4 space-y-1">
                    <p class="mb-1 mt-3 text-xs font-semibold uppercase tracking-widest text-slate-400">By Type</p>
                    <a href="{{ route('types.index') }}"
                        class="flex items-center gap-2 rounded px-3 py-2 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z"/></svg>
                        All institution types
                        <svg class="ml-auto h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>

                <div class="mt-3 space-y-1">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-slate-400">By Location</p>
                    <a href="{{ route('provinces.index') }}"
                        class="flex items-center gap-2 rounded px-3 py-2 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
                        Browse provinces
                        <svg class="ml-auto h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>

                <div class="mt-5 rounded border border-blue-100 bg-blue-50 p-4 text-sm">
                    <p class="font-semibold text-blue-800">Search Tips</p>
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
                <div class="rounded border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                    <h2 class="mt-4 text-lg font-bold text-slate-800">
                        @if ($term === '')
                            No institutions have been published yet.
                        @else
                            No results for "{{ $term }}"
                        @endif
                    </h2>
                    <p class="mt-2 text-sm text-slate-500">
                        @if ($term === '')
                            Run the institution import to populate the directory.
                        @else
                            Try a different spelling, or browse by
                            <a href="{{ route('types.index') }}" class="font-semibold text-blue-700 hover:underline">institution type</a>
                            or
                            <a href="{{ route('provinces.index') }}" class="font-semibold text-blue-700 hover:underline">province</a>.
                        @endif
                    </p>
                </div>
            @else
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm text-slate-600">
                        Found <strong class="text-slate-900">{{ number_format($institutions->total()) }}</strong> institution{{ $institutions->total() === 1 ? '' : 's' }}
                        @if ($term !== '') for <span class="text-blue-700">"{{ $term }}"</span>@endif
                    </p>
                </div>

                <ul class="space-y-3">
                    @foreach ($institutions as $institution)
                        <li>
                            <a href="{{ route('institutions.show', $institution) }}"
                                class="group flex items-start justify-between gap-4 rounded border border-slate-200 bg-white p-5 shadow-sm transition duration-150 hover:border-blue-300 hover:shadow-md">
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-semibold text-blue-800 group-hover:text-blue-700 truncate">
                                        {{ $institution->name }}
                                    </h3>
                                    <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-sm text-slate-500">
                                        @if ($institution->type)
                                            <span class="inline-flex items-center rounded bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">
                                                {{ $institution->type->name }}
                                            </span>
                                        @endif
                                        @if ($institution->municipality)
                                            <span>{{ $institution->municipality->name }}</span>
                                        @endif
                                        @if ($institution->district)
                                            <span class="text-slate-400">{{ $institution->district->name }}</span>
                                        @endif
                                    </p>
                                </div>
                                <svg class="mt-1 h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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
