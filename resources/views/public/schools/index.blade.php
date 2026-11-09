@extends('layouts.public')

@section('title', 'Schools in Nepal — '.config('app.name'))
@section('meta_description', 'Browse government and private schools across Nepal by province, district, and local government.')
@section('canonical', route('schools.index'))

@section('content')
@php
    $queryBase = array_filter([
        'q' => $term !== '' ? $term : null,
        'category' => $selectedCategory?->slug,
        'province' => $selectedProvince?->id,
        'district' => $selectedDistrict?->id,
        'municipality' => $selectedMunicipality?->id,
        'sort' => $sort !== 'az' ? $sort : null,
    ]);
@endphp

{{-- ─── PAGE HEADER ──────────────────────────────────────────────────────── --}}
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
            <a class="transition hover:text-blue-700" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-slate-900">Schools</span>
        </nav>
        <div class="mt-4 grid items-end gap-6 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Schools in Nepal</h1>
                <p class="mt-3 max-w-xl text-base leading-relaxed text-slate-600">
                    <span class="count-up font-semibold text-slate-900" data-count="{{ $totalSchools }}">{{ number_format($totalSchools) }}</span>
                    schools in this view, linked to a province, district, and local government.
                </p>
            </div>
            <form action="{{ route('schools.index') }}" method="get" class="lg:col-span-2" role="search">
                @foreach (['province' => $selectedProvince?->id, 'district' => $selectedDistrict?->id, 'municipality' => $selectedMunicipality?->id, 'category' => $selectedCategory?->slug, 'sort' => $sort !== 'az' ? $sort : null] as $name => $value)
                    @if ($value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endif
                @endforeach
                <label class="block text-sm font-semibold text-slate-700" for="school-search">Search by name</label>
                <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                    <div class="relative min-w-0 flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-5 w-5" /></span>
                        <input id="school-search" type="search" name="q" value="{{ $term }}" placeholder="School name"
                               class="form-input pl-10">
                    </div>
                    <button type="submit" class="btn-primary shrink-0">Search</button>
                </div>
            </form>
        </div>

        {{-- Category tiles --}}
        <div class="mt-6 grid gap-3 sm:grid-cols-2">
            @foreach ($categories as $category)
                @php
                    $active = $selectedCategory?->id === $category->id;
                    $isPrivate = $category->slug === 'private-school';
                @endphp
                <a href="{{ route('schools.index', array_filter(['category' => $active ? null : $category->slug, 'province' => $selectedProvince?->id, 'district' => $selectedDistrict?->id, 'municipality' => $selectedMunicipality?->id, 'q' => $term ?: null, 'sort' => $sort !== 'az' ? $sort : null])) }}"
                   class="flex items-center justify-between rounded border px-5 py-4 shadow-sm transition hover:shadow-md
                   {{ $isPrivate ? 'border-amber-100 bg-amber-50 text-amber-950' : 'border-emerald-100 bg-emerald-50 text-emerald-950' }}
                   {{ $active ? 'ring-2 ring-blue-600' : '' }}">
                    <span>
                        <span class="block text-xs font-bold uppercase tracking-wide {{ $isPrivate ? 'text-amber-700' : 'text-emerald-700' }}">{{ $category->name }}</span>
                        <span class="count-up mt-1 block text-2xl font-bold" data-count="{{ $category->institutions_count }}">{{ number_format($category->institutions_count) }}</span>
                    </span>
                    <span class="rounded px-3 py-1 text-xs font-semibold
                        {{ $active ? 'bg-slate-900 text-white' : ($isPrivate ? 'bg-amber-200 text-amber-900' : 'bg-emerald-200 text-emerald-900') }}">
                        {{ $active ? 'Showing' : 'View' }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Province pills --}}
    <div class="mb-6">
        <h2 class="text-xs font-bold uppercase tracking-widest text-slate-500">Filter by Province</h2>
        <div class="mt-3 flex snap-x gap-2 overflow-x-auto pb-2 sm:flex-wrap sm:overflow-visible">
            @foreach ($provinces as $province)
                @php $selected = $selectedProvince?->id === $province->id; @endphp
                <a href="{{ route('schools.index', array_filter(['province' => $province->id, 'category' => $selectedCategory?->slug, 'q' => $term ?: null, 'sort' => $sort !== 'az' ? $sort : null])) }}"
                   class="min-w-max shrink-0 snap-start rounded border px-3 py-2 text-xs font-semibold transition hover:shadow-sm
                   {{ $selected ? 'border-blue-700 bg-blue-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-blue-300' }}">
                    <span class="flex items-center gap-1"><x-icon name="pin" class="h-3.5 w-3.5" />{{ $province->name }}</span>
                    <span class="count-up mt-1 block text-sm font-bold" data-count="{{ $province->schools_count }}">{{ number_format($province->schools_count) }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Mobile filter toggle --}}
    <div class="mb-4 flex items-center justify-between gap-3 md:hidden">
        <button type="button" id="open-filters"
                class="inline-flex min-h-11 items-center rounded border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-800"
                aria-controls="school-filters" aria-expanded="false">Filters</button>
        <p class="text-sm text-slate-600" aria-live="polite">Showing {{ number_format($totalSchools) }} schools</p>
    </div>

    {{-- Filter bar --}}
    <form id="school-filters" method="get"
          class="mb-5 grid max-md:hidden gap-3 rounded border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:sticky lg:top-20 lg:z-30 lg:grid-cols-4">
        @if ($term !== '')<input type="hidden" name="q" value="{{ $term }}">@endif
        @if ($selectedCategory)<input type="hidden" name="category" value="{{ $selectedCategory->slug }}">@endif
        <label class="block text-sm font-semibold text-slate-700">
            Province
            <select name="province" onchange="this.form.district.value=''; this.form.municipality.value=''; this.form.submit()"
                    class="form-select mt-1 font-normal">
                <option value="">All provinces</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" @selected($selectedProvince?->id === $province->id)>{{ $province->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold text-slate-700">
            District
            <select name="district" onchange="this.form.municipality.value=''; this.form.submit()"
                    class="form-select mt-1 font-normal">
                <option value="">All districts</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}" @selected($selectedDistrict?->id === $district->id)>{{ $district->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold text-slate-700">
            Local government
            <select name="municipality" onchange="this.form.submit()"
                    class="form-select mt-1 font-normal disabled:cursor-not-allowed disabled:opacity-60"
                    @disabled(! $selectedDistrict)>
                <option value="">{{ $selectedDistrict ? 'All local governments' : 'Select a district first' }}</option>
                @foreach ($municipalities as $municipality)
                    <option value="{{ $municipality->id }}" @selected($selectedMunicipality?->id === $municipality->id)>{{ $municipality->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold text-slate-700">
            Sort
            <select name="sort" onchange="this.form.submit()" class="form-select mt-1 font-normal">
                <option value="az" @selected($sort === 'az')>A–Z</option>
                <option value="za" @selected($sort === 'za')>Z–A</option>
                <option value="new" @selected($sort === 'new')>Newest</option>
            </select>
        </label>
    </form>

    {{-- Active filter chips + view toggle --}}
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            <p class="text-sm font-semibold text-slate-600" aria-live="polite">Showing {{ number_format($totalSchools) }} schools</p>
            @if ($selectedCategory)
                <a class="rounded bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 hover:bg-blue-100"
                   href="{{ route('schools.index', array_filter(['q' => $term ?: null, 'province' => $selectedProvince?->id, 'district' => $selectedDistrict?->id, 'municipality' => $selectedMunicipality?->id, 'sort' => $sort !== 'az' ? $sort : null])) }}">{{ $selectedCategory->name }} ×</a>
            @endif
            @if ($selectedProvince)
                <a class="rounded bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 hover:bg-blue-100"
                   href="{{ route('schools.index', array_filter(['q' => $term ?: null, 'category' => $selectedCategory?->slug, 'sort' => $sort !== 'az' ? $sort : null])) }}">{{ $selectedProvince->name }} ×</a>
            @endif
            @if ($selectedDistrict)
                <a class="rounded bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 hover:bg-blue-100"
                   href="{{ route('schools.index', array_filter(['q' => $term ?: null, 'category' => $selectedCategory?->slug, 'province' => $selectedProvince?->id, 'sort' => $sort !== 'az' ? $sort : null])) }}">{{ $selectedDistrict->name }} ×</a>
            @endif
            @if ($selectedMunicipality)
                <a class="rounded bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 hover:bg-blue-100"
                   href="{{ route('schools.index', array_filter(['q' => $term ?: null, 'category' => $selectedCategory?->slug, 'province' => $selectedProvince?->id, 'district' => $selectedDistrict?->id, 'sort' => $sort !== 'az' ? $sort : null])) }}">{{ $selectedMunicipality->name }} ×</a>
            @endif
            @if ($queryBase !== [])
                <a class="text-xs font-semibold text-blue-700 underline" href="{{ route('schools.index') }}">Clear all</a>
            @endif
        </div>
        <div class="flex rounded border border-slate-200 bg-white p-1" role="group" aria-label="Result layout">
            <button type="button" id="view-grid"
                    class="min-h-9 rounded bg-blue-700 px-3 text-xs font-semibold text-white"
                    aria-pressed="true">Grid</button>
            <button type="button" id="view-list"
                    class="min-h-9 rounded px-3 text-xs font-semibold text-slate-600"
                    aria-pressed="false">List</button>
        </div>
    </div>

    {{-- School cards --}}
    @if ($schools->isNotEmpty())
        <div id="school-grid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($schools as $school)
                @php
                    $kind = $school->categories->first(fn ($item) => in_array($item->slug, ['government-school', 'private-school'], true));
                    $private = $kind?->slug === 'private-school';
                    $initial = mb_strtoupper(mb_substr(trim((string) preg_replace('/\p{C}+/u', '', $school->name)), 0, 1));
                    $initial = $initial !== '' ? $initial : 'S';
                @endphp
                <a href="{{ route('institutions.show', $school) }}"
                   class="school-card group flex gap-4 rounded border border-slate-200 bg-white p-4 shadow-sm transition duration-150 hover:border-blue-300 hover:shadow-md focus-visible:ring-2 focus-visible:ring-blue-600">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded text-lg font-black text-white
                        {{ $private ? 'bg-amber-500' : ($kind ? 'bg-emerald-600' : 'bg-blue-700') }}">{{ $initial }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block font-semibold leading-snug text-slate-900 group-hover:text-blue-700">{{ $school->name }}</span>
                        <span class="mt-1 flex items-start gap-1 text-xs text-slate-500">
                            <x-icon name="pin" class="mt-0.5 h-3.5 w-3.5 shrink-0" />
                            <span>{{ $school->municipality?->name }}, {{ $school->district?->name }}</span>
                        </span>
                        <span class="mt-2 inline-flex rounded px-2 py-0.5 text-xs font-semibold
                            {{ $private ? 'bg-amber-50 text-amber-800' : ($kind ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-600') }}">
                            {{ $kind?->name ?? 'Unclassified' }}
                        </span>
                    </span>
                    <span class="self-center text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-blue-600" aria-hidden="true">→</span>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-600">Showing {{ $schools->firstItem() }} to {{ $schools->lastItem() }} of {{ number_format($schools->total()) }}</p>
            <nav class="flex flex-wrap items-center gap-2" aria-label="Pagination">
                @if ($schools->onFirstPage())
                    <span class="inline-flex min-h-9 items-center rounded px-3 text-sm text-slate-400">Previous</span>
                @else
                    <a class="inline-flex min-h-9 items-center rounded border border-slate-200 bg-white px-3 text-sm font-semibold hover:bg-slate-50"
                       href="{{ $schools->previousPageUrl() }}">Previous</a>
                @endif
                @foreach ($schools->getUrlRange(max(1, $schools->currentPage() - 1), min($schools->lastPage(), $schools->currentPage() + 1)) as $page => $url)
                    <a class="inline-flex h-9 min-w-9 items-center justify-center rounded text-sm font-semibold
                        {{ $page === $schools->currentPage() ? 'bg-blue-700 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                       href="{{ $url }}"
                       @if ($page === $schools->currentPage()) aria-current="page" @endif>{{ $page }}</a>
                @endforeach
                @if ($schools->hasMorePages())
                    <a class="inline-flex min-h-9 items-center rounded border border-slate-200 bg-white px-3 text-sm font-semibold hover:bg-slate-50"
                       href="{{ $schools->nextPageUrl() }}">Next</a>
                @else
                    <span class="inline-flex min-h-9 items-center rounded px-3 text-sm text-slate-400">Next</span>
                @endif
            </nav>
            <form method="get" action="{{ route('schools.index') }}" class="flex items-center gap-2">
                @foreach ($queryBase as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach
                <label class="text-sm text-slate-600" for="go-page">Go to page</label>
                <input id="go-page" name="page" type="number" min="1" max="{{ $schools->lastPage() }}"
                       value="{{ $schools->currentPage() }}"
                       class="h-9 w-20 rounded border border-slate-200 px-3 text-sm">
            </form>
        </div>
    @else
        <div class="rounded border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-700">
                <x-icon name="search" class="h-6 w-6" />
            </span>
            <p class="mt-4 text-lg font-semibold text-slate-900">No schools match this view</p>
            <p class="mt-2 text-sm text-slate-500">Try another name, province, or school type.</p>
            <a href="{{ route('schools.index') }}" class="btn-primary mt-5 inline-flex">Clear filters</a>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    const filters = document.getElementById('school-filters');
    const openFilters = document.getElementById('open-filters');
    openFilters?.addEventListener('click', () => {
        const hidden = filters.classList.toggle('max-md:hidden');
        openFilters.setAttribute('aria-expanded', hidden ? 'false' : 'true');
    });
    const grid = document.getElementById('school-grid');
    const gridBtn = document.getElementById('view-grid');
    const listBtn = document.getElementById('view-list');
    const setView = (list) => {
        grid?.classList.toggle('lg:grid-cols-3', !list);
        grid?.classList.toggle('sm:grid-cols-2', !list);
        grid?.classList.toggle('grid-cols-1', list);
        gridBtn?.classList.toggle('bg-blue-700', !list);
        gridBtn?.classList.toggle('text-white', !list);
        listBtn?.classList.toggle('bg-blue-700', list);
        listBtn?.classList.toggle('text-white', list);
        gridBtn?.setAttribute('aria-pressed', String(!list));
        listBtn?.setAttribute('aria-pressed', String(list));
    };
    gridBtn?.addEventListener('click', () => setView(false));
    listBtn?.addEventListener('click', () => setView(true));
</script>
@endpush
