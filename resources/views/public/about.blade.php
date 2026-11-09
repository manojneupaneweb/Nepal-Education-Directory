@extends('layouts.public')

@section('title', 'About — '.config('app.name'))
@section('meta_description', 'Shiksha Link is an independent education directory for Nepal — covering schools, colleges, universities, and training centers across all 7 provinces.')

@section('content')

{{-- ── PAGE HEADER ──────────────────────────────────────────────────────────── --}}
<section class="border-b border-slate-200 bg-white py-10">
    <div class="section-container">
        <nav aria-label="Breadcrumb" class="mb-4 flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-blue-900 hover:underline">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-slate-700">About</span>
        </nav>
        <h1 class="text-3xl font-bold text-slate-900 sm:text-4xl">About {{ config('app.name') }}</h1>
        <p class="mt-3 max-w-2xl text-base text-slate-600">
            Nepal's free, independent education directory — connecting students, parents, and researchers with verified institution data across all 7 provinces.
        </p>
    </div>
</section>

{{-- ── MISSION ──────────────────────────────────────────────────────────────── --}}
<section class="py-14">
    <div class="section-container">
        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <h2 class="section-title">Our Mission</h2>
                <div class="mt-4 space-y-4 text-base leading-relaxed text-slate-600">
                    <p>
                        {{ config('app.name') }} exists to make information about Nepal's educational institutions freely accessible to everyone.
                        Whether you are a student researching colleges, a parent looking for a school near your municipality, or a researcher
                        studying the distribution of education infrastructure across provinces — this directory is for you.
                    </p>
                    <p>
                        We organize publicly available information about schools, early childhood development centres, colleges, universities,
                        technical institutes, and vocational training centers. Every published record has been reviewed before it appears on the site.
                    </p>
                    <p>
                        Nepal has thousands of schools, universities, and colleges spread across 7 provinces, 77 districts, and 753 local governments.
                        Before {{ config('app.name') }}, this data was fragmented across government PDFs and reports.
                        We have brought it together in one searchable, accessible platform — free for everyone.
                    </p>
                </div>
            </div>
            <div class="space-y-4">
                @foreach ([
                    ['Free & Open',          'No registration, no paywalls, no paid rankings.'],
                    ['No Endorsements',      'A listing is not an accreditation. We organize public data only.'],
                    ['Reviewed Records',     'Draft and pending records are never shown publicly.'],
                    ['Official Sources',     'Data from Nepal\'s National Statistics Office and Ministry of Education.'],
                ] as [$title, $desc])
                    <div class="card p-4">
                        <p class="text-sm font-semibold text-slate-900">{{ $title }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ── DATA SOURCES ─────────────────────────────────────────────────────────── --}}
<section class="border-t border-slate-200 bg-white py-14">
    <div class="section-container">
        <h2 class="section-title">Data Sources</h2>
        <p class="section-desc">All institution data is sourced from publicly available, official Nepali government publications.</p>
        <ul class="mt-6 space-y-3">
            @foreach ([
                ['National Statistics Office of Nepal',                       'Geographic data: provinces, districts, municipalities, and wards.'],
                ['Ministry of Education, Science and Technology (MoEST)',     'School and college classifications, institution codes, and affiliation data.'],
                ['University Grants Commission (UGC) Nepal',                  'University records and their affiliated college lists.'],
                ['Technical Education and Vocational Training Council (CTEVT)', 'Technical and vocational institute registry.'],
            ] as [$source, $scope])
                <li class="flex gap-4 rounded border border-slate-200 bg-white p-4">
                    <div class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-blue-900"></div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $source }}</p>
                        <p class="mt-0.5 text-sm text-slate-600">{{ $scope }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</section>

{{-- ── DISCLAIMER ───────────────────────────────────────────────────────────── --}}
<section class="border-t border-slate-200 py-14">
    <div class="section-container">
        <div class="mx-auto max-w-3xl rounded border border-amber-200 bg-amber-50 p-6">
            <div class="flex gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-700" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                </svg>
                <div>
                    <h2 class="text-base font-bold text-amber-800">Important Disclaimer</h2>
                    <div class="mt-3 space-y-2 text-sm leading-relaxed text-amber-900">
                        <p>{{ config('app.name') }} is an <strong>independent directory</strong>. It is not affiliated with any school, college, university, government body, or the Government of Nepal.</p>
                        <p>A listing in this directory is <strong>not an endorsement</strong> or accreditation of any institution. Always verify institution details directly with the school or relevant government authorities.</p>
                        <p>Institution data may be incomplete or out of date. If you find an error, please <a href="{{ route('contact') }}" class="font-semibold underline hover:text-amber-700">contact us</a> so we can correct it.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
            <a href="{{ route('contact') }}" class="btn-primary">Contact Us</a>
            <a href="{{ route('how-it-works') }}" class="btn-outline">How it Works</a>
        </div>
    </div>
</section>

@endsection
