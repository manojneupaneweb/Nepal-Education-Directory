@extends('layouts.public')

@php
    $pathKey = str_ends_with($university->slug, '-university')
        ? \Illuminate\Support\Str::beforeLast($university->slug, '-university')
        : $university->slug;
@endphp

@section('title', $university->name.' academic programs — '.config('app.name'))
@section('meta_description', 'Faculties and academic programs of '.$university->name.' collected from its official website.')
@section('canonical', route('university-programs.university', $pathKey))

@section('content')
<section class="bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 py-10 text-white">
    <div class="mx-auto max-w-6xl px-4">
        <nav class="text-sm text-brand-100" aria-label="Breadcrumb">
            <a class="hover:text-white" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true"> / </span>
            <a class="hover:text-white" href="{{ route('universities.show', $university) }}">{{ $university->name }}</a>
            <span aria-hidden="true"> / </span>
            <span class="text-white">Programs</span>
        </nav>
        <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center">
            @if ($university->logo_path)
                <img src="{{ asset($university->logo_path) }}" alt="" class="h-24 w-24 shrink-0 rounded-2xl border border-white bg-white object-contain p-2 shadow-md">
            @endif
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $university->name }} programs</h1>
                <p class="mt-2 max-w-3xl text-brand-100">Faculties and programs named on a public source. Duration is shown only when that source states it.</p>
            </div>
        </div>
        <div class="mt-6 flex flex-wrap gap-3">
            <span class="rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-brand-800 shadow-md">{{ $university->programs_count }} programs</span>
            <span class="rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-brand-800 shadow-md">{{ $university->faculties_count }} faculties</span>
            @if ($university->official_website)
                <a href="{{ $university->official_website }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-lg border border-white px-4 py-2 text-sm font-semibold text-white">Visit Official Website</a>
            @endif
        </div>
    </div>
</section>

<div class="mx-auto max-w-6xl space-y-8 px-4 py-10">
    @if ($programs->onFirstPage() && $faculties->isNotEmpty())
        <section>
            <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>Faculties</h2>
            <ul class="mt-3 flex flex-wrap gap-2">
                @foreach ($faculties as $faculty)
                    <li class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-slate-700 shadow-sm">{{ $faculty->name }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @forelse ($programs->groupBy(fn ($program) => $program->faculty?->name ?? 'Other programs') as $facultyName => $group)
        <section>
            <h2 class="text-xl font-bold text-slate-900">{{ $facultyName }}</h2>
            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                @foreach ($group as $program)
                    <a href="{{ route('university-programs.show', $program) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:border-brand-300">
                        <span class="font-semibold text-slate-900">{{ $program->name }}</span>
                        @if ($program->duration)
                            <span class="mt-1 block text-sm text-slate-500">{{ $program->duration }}</span>
                        @endif
                        @if ($program->program_status === 'not_offered')
                            <span class="mt-2 inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Not currently offered</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @empty
        <p class="text-slate-600">No programs are stored for this university yet.</p>
    @endforelse

    <div>{{ $programs->links() }}</div>
</div>
@endsection
