@extends('layouts.public')

@section('title', 'Colleges in Nepal — '.config('app.name'))
@section('meta_description', 'Browse colleges across Nepal by province, district, and local government.')
@section('canonical', route('colleges.index'))

@section('content')
<section class="border-b border-slate-200 bg-white py-12 text-slate-900">
    <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/10"></div>
    <div class="pointer-events-none absolute -bottom-24 left-10 h-48 w-48 rounded-full bg-brand-600/40"></div>
    <div class="relative mx-auto max-w-6xl px-4">
        <nav class="text-sm text-slate-500" aria-label="Breadcrumb">
            <a class="hover:text-brand-800" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true"> / </span>
            <span class="text-slate-900">Colleges</span>
        </nav>
        <div class="mt-6 grid items-end gap-8 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <p class="text-sm font-semibold text-brand-800">College directory</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Colleges in Nepal</h1>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">{{ number_format($totalColleges) }} colleges, each linked to a province, district, and local government.</p>
            </div>
            <form action="{{ route('colleges.index') }}" method="get" class="lg:col-span-2">
                @if ($selectedProvince)<input type="hidden" name="province" value="{{ $selectedProvince->id }}">@endif
                @if ($selectedDistrict)<input type="hidden" name="district" value="{{ $selectedDistrict->id }}">@endif
                @if ($selectedMunicipality)<input type="hidden" name="municipality" value="{{ $selectedMunicipality->id }}">@endif
                <label class="block text-sm font-semibold text-slate-700" for="college-search">Search by name</label>
                <div class="mt-2 flex overflow-hidden rounded-xl border border-slate-300 bg-white">
                    <input id="college-search" type="search" name="q" value="{{ $term }}" placeholder="College name" class="min-w-0 flex-1 px-4 py-3 text-sm text-slate-900 outline-none">
                    <button type="submit" class="bg-brand-700 px-5 text-sm font-bold text-white hover:bg-brand-800">Search</button>
                </div>
            </form>
        </div>
    </div>
</section>

<div class="mx-auto max-w-6xl px-4 py-10">
    <div class="mb-8">
        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-500">Provinces</h2>
        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7">
            @foreach ($provinces as $province)
                <a href="{{ route('colleges.index', array_filter(['province' => $province->id, 'q' => $term])) }}" class="rounded-2xl border bg-white px-3 py-3 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md {{ $selectedProvince?->id === $province->id ? 'border-brand-700 ring-2 ring-brand-200' : 'border-slate-200' }}">
                    <span class="block text-xs font-semibold leading-snug text-slate-500">{{ $province->name }}</span>
                    <span class="mt-2 block text-xl font-extrabold text-brand-800">{{ number_format($province->colleges_count) }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="mb-8">
        <button type="button" id="show-districts" class="btn-primary" aria-expanded="{{ $selectedDistrict ? 'true' : 'false' }}" aria-controls="district-panel">
            {{ $selectedDistrict ? 'Hide 77 districts' : 'Show 77 districts' }}
        </button>
        <div id="district-panel" class="mt-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedDistrict ? '' : 'hidden' }}">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-sm font-bold tracking-widest text-slate-500 uppercase">77 districts</h2>
                    <p class="mt-1 text-sm text-slate-600">Choose a district to see its colleges. Nawalpur is listed as Nawalparasi East.</p>
                </div>
                <label class="block text-sm font-semibold text-slate-700" for="district-map-search">Find a district
                    <input id="district-map-search" type="search" placeholder="Nawalpur" class="mt-1 w-full rounded-full border border-slate-200 px-4 py-3 text-base sm:w-64">
                </label>
            </div>
            <div id="district-map" class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($districtMap as $mapDistrict)
                    @php
                        $alias = str_contains(strtolower($mapDistrict->name), 'nawalparasi east') ? 'nawalpur nawalparasi east' : strtolower($mapDistrict->name);
                    @endphp
                    <a data-district="{{ $alias }}" href="{{ route('colleges.index', ['district' => $mapDistrict->id, 'province' => $mapDistrict->province_id]) }}" class="rounded-2xl border px-3 py-2 text-sm {{ $selectedDistrict?->id === $mapDistrict->id ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-200 bg-slate-50 text-slate-800 hover:border-indigo-300' }}">
                        <span class="block font-semibold">{{ $mapDistrict->name }}</span>
                        <span class="text-xs {{ $selectedDistrict?->id === $mapDistrict->id ? 'text-indigo-100' : 'text-slate-500' }}">{{ number_format($mapDistrict->colleges_count) }} colleges</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <form method="get" class="mb-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-3">
        @if ($term !== '')<input type="hidden" name="q" value="{{ $term }}">@endif
        <label class="block text-sm font-semibold text-slate-700">
            Province
            <select name="province" onchange="this.form.district.value=''; this.form.municipality.value=''; this.form.submit()" class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm font-normal text-slate-900">
                <option value="">All provinces</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" @selected($selectedProvince?->id === $province->id)>{{ $province->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold text-slate-700">
            District
            <select name="district" onchange="this.form.municipality.value=''; this.form.submit()" class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm font-normal text-slate-900">
                <option value="">All districts</option>
                @foreach ($districtMap as $mapDistrict)
                    <option value="{{ $mapDistrict->id }}" @selected($selectedDistrict?->id === $mapDistrict->id)>{{ $mapDistrict->name }}@if(str_contains(strtolower($mapDistrict->name), 'nawalparasi east')) (Nawalpur)@endif</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold text-slate-700">
            Local government
            <select name="municipality" onchange="this.form.submit()" class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm font-normal text-slate-900" @disabled(! $selectedDistrict)>
                <option value="">{{ $selectedDistrict ? 'All local governments' : 'Select a district first' }}</option>
                @foreach ($municipalities as $municipality)
                    <option value="{{ $municipality->id }}" @selected($selectedMunicipality?->id === $municipality->id)>{{ $municipality->name }}</option>
                @endforeach
            </select>
        </label>
    </form>

    @if ($colleges->isNotEmpty())
        <div class="space-y-4">
            @foreach ($colleges as $college)
                @php
                    $logo = $college->logo_path
                        ? (str_starts_with($college->logo_path, 'http') ? $college->logo_path : asset($college->logo_path))
                        : null;
                    $place = collect([$college->municipality?->name, $college->district?->name])->filter()->join(', ');
                    $verified = $college->verification_status !== \App\Enums\VerificationStatus::Unverified;
                @endphp
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="flex gap-4">
                        @if ($logo)
                            <img src="{{ $logo }}" alt="{{ $college->name }} logo" width="64" height="64" loading="lazy" decoding="async" class="h-16 w-16 shrink-0 rounded-full border border-slate-200 bg-white object-contain p-1">
                        @else
                            <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-blue-50 text-lg font-bold text-blue-900">{{ mb_strtoupper(mb_substr($college->name, 0, 1)) }}</span>
                        @endif
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold leading-snug text-slate-900 sm:text-xl">
                                <a href="{{ route('institutions.show', $college) }}" class="hover:text-blue-800">{{ $college->name }}</a>
                                @if ($verified)
                                    <svg class="ml-1 inline h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor" aria-label="Reviewed"><path d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5Zm4.28 7.53-5.25 6a.75.75 0 0 1-1.12.03l-2.25-2.25a.75.75 0 1 1 1.06-1.06l1.67 1.67 4.71-5.38a.75.75 0 1 1 1.18.99Z"/></svg>
                                @endif
                            </h2>
                            @if ($place !== '')
                                <p class="mt-1 flex items-center gap-1 text-sm text-slate-600">
                                    <svg class="h-4 w-4 text-slate-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25a7.5 7.5 0 0 0-7.5 7.5c0 5.25 7.5 12 7.5 12s7.5-6.75 7.5-12a7.5 7.5 0 0 0-7.5-7.5Zm0 10.125a2.625 2.625 0 1 1 0-5.25 2.625 2.625 0 0 1 0 5.25Z"/></svg>
                                    {{ $place }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @if ($college->programs->isNotEmpty())
                        <ul class="mt-3 space-y-1">
                            @foreach ($college->programs->take(4) as $program)
                                <li>
                                    <a href="{{ route('institutions.show', $college) }}" class="text-sm font-medium text-sky-700 hover:underline">{{ $program->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold text-sky-800">
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 hover:underline">Ask question</a>
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 hover:underline">Write review</a>
                        <a href="{{ route('institutions.show', $college) }}" class="inline-flex items-center gap-1.5 hover:underline">Courses</a>
                        <a href="{{ route('institutions.show', $college) }}" class="inline-flex items-center gap-1.5 hover:underline">Contact</a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $colleges->links() }}</div>
    @else
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <p class="text-lg font-bold text-slate-900">No colleges match this view</p>
            <p class="mt-2 text-sm text-slate-500">Try another name, province, or local government.</p>
            <a href="{{ route('colleges.index') }}" class="mt-5 inline-flex rounded-xl bg-brand-700 px-4 py-2 text-sm font-bold text-white hover:bg-brand-800">Clear filters</a>
        </div>
    @endif
</div>
<script>
    document.getElementById('show-districts')?.addEventListener('click', (event) => {
        const panel = document.getElementById('district-panel');
        const open = panel.classList.toggle('hidden') === false;
        event.currentTarget.setAttribute('aria-expanded', open ? 'true' : 'false');
        event.currentTarget.textContent = open ? 'Hide 77 districts' : 'Show 77 districts';
    });
    document.getElementById('district-map-search')?.addEventListener('input', (event) => {
        const term = event.target.value.toLowerCase().trim();
        document.querySelectorAll('#district-map a').forEach((link) => {
            link.classList.toggle('hidden', term !== '' && !link.dataset.district.includes(term));
        });
    });
</script>
@endsection
