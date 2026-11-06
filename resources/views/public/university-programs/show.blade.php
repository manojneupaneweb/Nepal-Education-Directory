@extends('layouts.public')

@section('title', ($program->seo_title ?: $program->name).' — '.config('app.name'))
@section('meta_description', $program->meta_description ?: $program->description)
@section('canonical', route('university-programs.show', $program))

@push('meta')
    <meta property="og:title" content="{{ $program->seo_title ?: $program->name }}">
    <meta property="og:description" content="{{ $program->meta_description ?: $program->description }}">
    <meta property="og:url" content="{{ route('university-programs.show', $program) }}">
    <meta property="og:type" content="website">
@endpush

@section('content')
<section class="bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 py-10 text-white">
    <div class="mx-auto max-w-6xl px-4">
        <nav class="text-sm text-brand-100" aria-label="Breadcrumb">
            <a class="hover:text-white" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true"> / </span>
            <a class="hover:text-white" href="{{ route('universities.show', $program->university) }}">{{ $program->university->name }}</a>
            <span aria-hidden="true"> / </span>
            <a class="hover:text-white" href="{{ route('university-programs.index') }}">Programs</a>
            <span aria-hidden="true"> / </span>
            <span class="text-white">{{ $program->name }}</span>
        </nav>
        <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center">
            @if ($program->university->logo_path)
                <img src="{{ asset($program->university->logo_path) }}" alt="" class="h-24 w-24 shrink-0 rounded-2xl border border-white bg-white object-contain p-2 shadow-md">
            @endif
            <div>
                <p class="text-sm font-semibold text-brand-100">{{ $program->university->name }}</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $program->name }}</h1>
                @if ($program->name_ne)
                    <p class="mt-1 text-lg text-brand-100">{{ $program->name_ne }}</p>
                @endif
            </div>
        </div>
        @if ($program->description)
            <p class="mt-5 max-w-3xl leading-relaxed text-brand-100">{{ $program->description }}</p>
        @endif
    </div>
</section>

<div class="mx-auto max-w-6xl space-y-8 px-4 py-10">
    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['University', $program->university->name, route('universities.show', $program->university)],
            ['Faculty', $program->faculty?->name],
            ['Degree level', $program->educationLevel?->name],
            ['Duration', $program->duration],
            ['Discipline', $program->discipline],
            ['Eligibility', $program->eligibility],
        ] as $fact)
            @if (filled($fact[1] ?? null))
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $fact[0] }}</p>
                    @if (isset($fact[2]))
                        <a class="mt-1 block font-semibold text-brand-800" href="{{ $fact[2] }}">{{ $fact[1] }}</a>
                    @else
                        <p class="mt-1 font-semibold text-slate-900">{{ $fact[1] }}</p>
                    @endif
                </div>
            @endif
        @endforeach
    </section>

    @if ($program->admission_requirements)
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900">Admission</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $program->admission_requirements }}</p>
        </section>
    @endif

    @if ($program->institutions->isNotEmpty())
        <section>
            <h2 class="text-xl font-bold text-slate-900">Colleges and campuses</h2>
            <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                @foreach ($program->institutions as $institution)
                    <li><a class="text-sm font-semibold text-brand-700" href="{{ route('institutions.show', $institution) }}">{{ $institution->name }}</a></li>
                @endforeach
            </ul>
        </section>
    @endif

    <div class="flex flex-wrap gap-3">
        @if ($program->source_url)
            <a class="inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white" href="{{ $program->source_url }}" target="_blank" rel="noopener noreferrer">Official page</a>
        @endif
        @if ($program->curriculum_url)
            <a class="inline-flex rounded-lg border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-800" href="{{ $program->curriculum_url }}" target="_blank" rel="noopener noreferrer">Curriculum</a>
        @endif
        @if ($program->admission_url)
            <a class="inline-flex rounded-lg border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-800" href="{{ $program->admission_url }}" target="_blank" rel="noopener noreferrer">Admission information</a>
        @endif
    </div>
</div>
@endsection
