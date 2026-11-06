@extends('layouts.public')

@section('title', 'Schools in Nepal — '.config('app.name'))
@section('meta_description', 'Discover government, community, private, and boarding schools across all 7 provinces and 77 districts of Nepal.')

@section('content')

{{-- Header Banner --}}
<section class="border-b border-slate-200 bg-white py-12">
    <div class="mx-auto max-w-6xl px-4">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-brand-700">
                    🏫 School Directory
                </span>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    Schools in Nepal
                </h1>
                <p class="mt-2 max-w-2xl text-base text-slate-600">
                    Browse government, community, private, and residential boarding schools across all 77 districts and 753 local municipalities of Nepal.
                </p>
            </div>

            {{-- Quick Search Form --}}
            <div class="w-full md:w-80">
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input type="hidden" name="type" value="school">
                    <input type="search" name="q" placeholder="Search school name..."
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-10 pr-4 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </span>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Main Content --}}
<div class="mx-auto max-w-6xl px-4 py-12">

    <form method="get" class="mb-10 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-3">
        <label class="block text-sm font-semibold text-slate-700">
            Province
            <select name="province" onchange="this.form.district.value=''; this.form.municipality.value=''; this.form.submit()" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal text-slate-900">
                <option value="">All provinces</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" @selected($selectedProvince?->id === $province->id)>{{ $province->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold text-slate-700">
            District
            <select name="district" onchange="this.form.municipality.value=''; this.form.submit()" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal text-slate-900">
                <option value="">All districts</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}" @selected($selectedDistrict?->id === $district->id)>{{ $district->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold text-slate-700">
            Local government
            <select name="municipality" onchange="this.form.submit()" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal text-slate-900" @disabled(! $selectedDistrict)>
                <option value="">{{ $selectedDistrict ? 'All local governments' : 'Select a district first' }}</option>
                @foreach ($municipalities as $municipality)
                    <option value="{{ $municipality->id }}" @selected($selectedMunicipality?->id === $municipality->id)>{{ $municipality->name }}</option>
                @endforeach
            </select>
        </label>
    </form>

    {{-- Categories Overview --}}
    @if ($categories->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">School Classifications</h2>
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                @foreach ($categories as $cat)
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-brand-300 hover:shadow-md">
                        <span class="text-xs font-semibold text-brand-700">Category</span>
                        <h3 class="mt-1 font-bold text-slate-900">{{ $cat->name }}</h3>
                        @if ($cat->name_ne)
                            <p class="text-xs text-slate-500">{{ $cat->name_ne }}</p>
                        @endif
                        <p class="mt-2 text-xs font-semibold text-slate-400">{{ $cat->institutions_count }} registered</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Schools Listings --}}
    <div>
        <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-3">
            <h2 class="text-xl font-bold text-slate-900">
                School Directory
                <span class="ml-2 text-sm font-normal text-slate-500">({{ number_format($totalSchools) }} verified)</span>
            </h2>
        </div>

        @if ($schools->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($schools as $school)
                    <a href="{{ route('institutions.show', $school) }}"
                       class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <span class="rounded-md bg-brand-50 px-2 py-1 text-[11px] font-bold text-brand-700">
                                    {{ $school->ownership?->label() ?? 'School' }}
                                </span>
                                @if ($school->established_year)
                                    <span class="text-xs text-slate-400">Est. {{ $school->established_year }}</span>
                                @endif
                            </div>

                            <h3 class="mt-3 font-bold leading-snug text-slate-900 hover:text-brand-700">
                                {{ $school->name }}
                            </h3>
                            @if ($school->name_ne)
                                <p class="mt-0.5 text-xs text-slate-500">{{ $school->name_ne }}</p>
                            @endif

                            <p class="mt-2 text-xs text-slate-500">
                                📍 {{ $school->municipality?->name }}, {{ $school->district?->name }}
                            </p>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-xs font-semibold text-brand-700">
                            <span>View Profile</span>
                            <span>→</span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $schools->links() }}
            </div>
        @else
            <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-2xl text-brand-700">
                    🏫
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">Schools being indexed across 77 districts</h3>
                <p class="mx-auto mt-2 max-w-md text-sm text-slate-600">
                    We are verifying school registrations and government census datasets. Use the search or explore by district to view verified records.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('search') }}?type=school"
                       class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800">
                        Search by Name
                    </a>
                    <a href="{{ route('provinces.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Browse by Province
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Explore By Province --}}
    @if ($provinces->isNotEmpty())
        <div class="mt-14">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Explore Schools by Province</h2>
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-7">
                @foreach ($provinces as $prov)
                    <a href="{{ route('provinces.show', $prov) }}"
                       class="flex flex-col items-center rounded-xl border border-slate-200 bg-white p-3 text-center transition hover:border-brand-600 hover:shadow-sm">
                        <span class="text-xs font-semibold text-slate-400">P{{ $prov->code }}</span>
                        <span class="mt-1 font-bold text-slate-800 text-xs">{{ $prov->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>

@endsection

