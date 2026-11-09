@extends('layouts.public')

@section('title', 'Universities in Nepal — '.config('app.name'))
@section('meta_description', 'Directory of universities and health academies in Nepal, with location, type, establishment year, and links to verified official websites.')
@section('canonical', route('universities.index'))

@section('content')
@php
    $tone = function (?string $type): string {
        $value = strtolower((string) $type);
        if (str_contains($value, 'federal')) {
            return 'bg-blue-700';
        }
        if (str_contains($value, 'medical') && str_contains($value, 'provincial')) {
            return 'bg-rose-600';
        }
        if (str_contains($value, 'medical')) {
            return 'bg-rose-500';
        }
        if (str_contains($value, 'agriculture') || str_contains($value, 'technical')) {
            return 'bg-emerald-600';
        }
        if (str_contains($value, 'provincial')) {
            return 'bg-teal-600';
        }

        return 'bg-slate-500';
    };
@endphp

{{-- ─── PAGE HEADER ──────────────────────────────────────────────────────── --}}
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
            <a class="transition hover:text-blue-700" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-slate-900">Universities</span>
        </nav>
        <div class="mt-4 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Universities in Nepal</h1>
                <p class="mt-2 max-w-2xl text-base text-slate-600">
                    Records sourced from the University Grants Commission Nepal list. Profiles include only details that have a verified source.
                </p>
            </div>
            <div class="grid grid-cols-3 gap-3">
                @foreach ([[$totalUniversities, 'Listed'], [$totalPrograms, 'Programs'], [$totalColleges, 'Linked colleges']] as [$count, $label])
                    <div class="rounded border border-slate-200 bg-slate-50 px-3 py-3 text-center">
                        <p class="count-up text-2xl font-bold text-blue-800" data-count="{{ $count }}">{{ number_format($count) }}</p>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Mobile filter toggle --}}
    <button type="button" id="open-filters"
            class="mb-3 inline-flex min-h-11 items-center rounded border border-slate-200 bg-white px-4 text-sm font-semibold md:hidden"
            aria-expanded="false" aria-controls="university-filters">Filters</button>

    {{-- Filter bar --}}
    <form id="university-filters" method="get"
          class="mb-6 grid max-md:hidden items-end gap-3 rounded border border-slate-200 bg-white p-4 shadow-sm md:grid lg:sticky lg:top-20 lg:z-30 lg:grid-cols-4">
        <label class="block text-sm font-semibold text-slate-700">Search
            <input type="search" name="q" value="{{ request('q') }}" placeholder="University name"
                   class="form-input mt-1 font-normal">
        </label>
        <label class="block text-sm font-semibold text-slate-700">Type
            <select name="type" class="form-select mt-1 font-normal">
                <option value="">All types</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold text-slate-700">Province
            <select name="province" class="form-select mt-1 font-normal">
                <option value="">All provinces</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" @selected(request('province') == $province->id)>{{ $province->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold text-slate-700">Sort
            <select name="sort" class="form-select mt-1 font-normal">
                <option value="az" @selected($sort === 'az')>A–Z</option>
                <option value="programs" @selected($sort === 'programs')>Most programs</option>
                <option value="colleges" @selected($sort === 'colleges')>Most linked colleges</option>
                <option value="oldest" @selected($sort === 'oldest')>Oldest established</option>
            </select>
        </label>
        <button class="btn-primary lg:col-span-4 lg:w-fit">Search</button>
    </form>

    <p class="mb-5 text-sm font-semibold text-slate-600" aria-live="polite">{{ number_format($universities->total()) }} listed institutions</p>

    {{-- University cards --}}
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($universities as $university)
            <a href="{{ route('universities.show', $university) }}"
               class="flex flex-col justify-between overflow-hidden rounded border border-slate-200 bg-white shadow-sm transition duration-150 hover:border-blue-300 hover:shadow-md">
                <span class="h-1 {{ $tone($university->university_type) }}"></span>
                <div class="flex flex-1 flex-col p-5">
                    <div class="flex items-start gap-3">
                        @if ($university->logo_path)
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded border border-slate-200 bg-white p-1.5">
                                <img src="{{ asset($university->logo_path) }}" alt="{{ $university->name }} logo" class="h-full w-full object-contain">
                            </div>
                        @else
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded {{ $tone($university->university_type) }} text-lg font-black text-white">
                                {{ strtoupper(substr($university->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h2 class="line-clamp-2 text-base font-semibold text-slate-900">{{ $university->name }}</h2>
                            <p class="min-h-5 text-sm text-slate-500" @if($university->name_ne) lang="ne" @endif>{{ $university->name_ne }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-600">{{ $university->location_label ?: 'Location not matched to a district yet' }}</p>
                    <p class="mt-2 flex flex-wrap gap-2 text-xs font-semibold">
                        @if ($university->university_type)
                            <span class="rounded bg-slate-100 px-2 py-1 text-slate-700">{{ $university->university_type }}</span>
                        @endif
                        @if ($university->established_year_bs)
                            <span class="rounded bg-amber-50 px-2 py-1 text-amber-800">Est. {{ $university->established_year_bs }} BS</span>
                        @endif
                    </p>
                    <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-slate-600">{{ $university->description }}</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded bg-blue-50 px-2 py-1 text-blue-800">{{ $university->programs_count }} programs</span>
                        <span class="rounded bg-slate-100 px-2 py-1 text-slate-600">{{ $university->institutions_count > 0 ? $university->institutions_count.' linked colleges' : 'No linked colleges yet' }}</span>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-blue-700">View profile →</p>
                </div>
            </a>
        @empty
            <p class="text-slate-600">No universities match these filters. Import the catalog with <code>php artisan universities:research --import</code>.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($universities->isNotEmpty())
        <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-600">Showing {{ $universities->firstItem() }} to {{ $universities->lastItem() }} of {{ number_format($universities->total()) }}</p>
            <nav class="flex flex-wrap items-center gap-2" aria-label="Pagination">
                @if ($universities->onFirstPage())
                    <span class="inline-flex min-h-9 items-center px-3 text-sm text-slate-400">Previous</span>
                @else
                    <a class="inline-flex min-h-9 items-center rounded border border-slate-200 bg-white px-3 text-sm font-semibold hover:bg-slate-50"
                       href="{{ $universities->previousPageUrl() }}">Previous</a>
                @endif
                @foreach ($universities->getUrlRange(max(1, $universities->currentPage() - 1), min($universities->lastPage(), $universities->currentPage() + 1)) as $page => $url)
                    <a class="inline-flex h-9 min-w-9 items-center justify-center rounded text-sm font-semibold
                        {{ $page === $universities->currentPage() ? 'bg-blue-700 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                       href="{{ $url }}"
                       @if ($page === $universities->currentPage()) aria-current="page" @endif>{{ $page }}</a>
                @endforeach
                @if ($universities->hasMorePages())
                    <a class="inline-flex min-h-9 items-center rounded border border-slate-200 bg-white px-3 text-sm font-semibold hover:bg-slate-50"
                       href="{{ $universities->nextPageUrl() }}">Next</a>
                @else
                    <span class="inline-flex min-h-9 items-center px-3 text-sm text-slate-400">Next</span>
                @endif
            </nav>
        </div>
    @endif

    {{-- Browse programs CTA --}}
    <a href="{{ route('university-programs.index') }}"
       class="mt-8 flex items-center justify-between rounded border border-blue-200 bg-blue-50 px-6 py-5 transition hover:bg-blue-100">
        <span class="text-base font-semibold text-blue-900">Browse academic programs</span>
        <span class="text-blue-700" aria-hidden="true">→</span>
    </a>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('open-filters')?.addEventListener('click', (event) => {
        const form = document.getElementById('university-filters');
        const hidden = form.classList.toggle('max-md:hidden');
        event.currentTarget.setAttribute('aria-expanded', hidden ? 'false' : 'true');
    });
</script>
@endpush
