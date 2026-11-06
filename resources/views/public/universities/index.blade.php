@extends('layouts.public')

@section('title', 'Universities in Nepal — '.config('app.name'))
@section('meta_description', 'Directory of universities and health academies in Nepal, with location, type, establishment year, and links to verified official websites.')
@section('canonical', route('universities.index'))

@section('content')
<section class="border-b border-slate-200 bg-white py-12">
    <div class="mx-auto max-w-6xl px-4">
        <nav class="text-sm text-slate-500" aria-label="Breadcrumb">
            <a class="hover:text-brand-700" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true"> / </span>
            <span class="text-slate-800">Universities</span>
        </nav>
        <div class="mt-4 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Universities in Nepal</h1>
                <p class="mt-2 max-w-2xl text-base text-slate-600">
                    Records start from the University Grants Commission Nepal list already used by Shiksha Link. Profiles add only details that have a stored source.
                </p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-center">
                <p class="text-2xl font-black text-brand-800">{{ $totalUniversities }}</p>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Listed institutions</p>
            </div>
        </div>
    </div>
</section>

<div class="mx-auto max-w-6xl px-4 py-10">
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($universities as $university)
            <a href="{{ route('universities.show', $university) }}" class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-brand-300 hover:shadow-md">
                <div>
                    <div class="flex items-start gap-3">
                        @if ($university->logo_path)
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-white p-1.5">
                                <img src="{{ asset($university->logo_path) }}" alt="{{ $university->name }} logo" class="h-full w-full object-contain">
                            </div>
                        @else
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-700 to-brand-900 text-xl font-black text-white">
                                {{ strtoupper(substr($university->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $university->name }}</h2>
                            @if ($university->name_ne)
                                <p class="text-sm text-slate-500">{{ $university->name_ne }}</p>
                            @endif
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-600">{{ $university->location_label ?: 'Location not matched to a district yet' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $university->university_type }} @if($university->established_year_bs) · Est. {{ $university->established_year_bs }} BS @endif</p>
                    <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-slate-600">{{ $university->description }}</p>
                </div>
                <div class="mt-4 flex gap-3 text-xs font-semibold uppercase tracking-wide text-brand-800">
                    <span>{{ $university->programs_count }} programs</span>
                    <span>{{ $university->institutions_count }} linked colleges</span>
                </div>
            </a>
        @empty
            <p class="text-slate-600">No universities match these filters. Import the catalog with <code>php artisan universities:research --import</code>.</p>
        @endforelse
    </div>

    <div class="mt-8">{{ $universities->links() }}</div>

    <p class="mt-8 text-sm">
        <a class="font-semibold text-brand-700 hover:text-brand-900" href="{{ route('university-programs.index') }}">Browse academic programs</a>
    </p>
</div>
@endsection
