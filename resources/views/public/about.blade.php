@extends('layouts.public')

@section('title', 'About — '.config('app.name'))
@section('meta_description', 'Shiksha Link is an independent education directory for Nepal — covering schools, colleges, universities, and training centers across all 7 provinces.')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 text-white">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-100">
            About
        </span>
        <h1 class="mt-4 max-w-3xl text-4xl font-extrabold tracking-tight sm:text-5xl">
            About {{ config('app.name') }}
        </h1>
        <p class="mt-5 max-w-2xl text-lg leading-relaxed text-brand-100">
            Nepal's free, independent education directory — connecting students, parents, and researchers with verified institution data.
        </p>
    </div>
</section>

{{-- Mission --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="rounded-2xl border border-brand-100 bg-brand-50 p-8 sm:p-12">
            <h2 class="text-2xl font-extrabold text-brand-800">Our Mission</h2>
            <p class="mt-4 text-base leading-relaxed text-slate-700">
                {{ config('app.name') }} exists to make information about Nepal's educational institutions freely accessible to everyone.
                Whether you are a student researching colleges, a parent looking for a school near your municipality, or a researcher
                studying the distribution of education infrastructure across provinces — this directory is for you.
            </p>
            <p class="mt-4 text-base leading-relaxed text-slate-700">
                We organize publicly available information about schools, early childhood development centres, colleges, universities,
                technical institutes, and vocational training centers. Every published record has been reviewed before it appears on the site.
            </p>
        </div>

        <div class="mt-12 grid gap-6 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm text-center">
                <span class="text-4xl">🎯</span>
                <h3 class="mt-3 font-bold text-slate-900">Accurate</h3>
                <p class="mt-2 text-sm text-slate-600">Every institution record is reviewed and approved before publication. Outdated or incorrect records can be reported for correction.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm text-center">
                <span class="text-4xl">🌐</span>
                <h3 class="mt-3 font-bold text-slate-900">Comprehensive</h3>
                <p class="mt-2 text-sm text-slate-600">From pre-primary schools to doctoral universities — across all 7 provinces, 77 districts, and 753 local governments.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm text-center">
                <span class="text-4xl">🆓</span>
                <h3 class="mt-3 font-bold text-slate-900">Free</h3>
                <p class="mt-2 text-sm text-slate-600">No registration, no subscription. All published institution records are freely accessible to the public.</p>
            </div>
        </div>
    </div>
</section>

{{-- Data Sources --}}
<section class="bg-brand-50 py-16">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-extrabold text-slate-900">Data Sources</h2>
        <p class="mt-3 text-slate-600">{{ config('app.name') }} draws on official Nepali government data to ensure accuracy.</p>

        <div class="mt-8 space-y-4">
            <div class="flex gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <span class="mt-0.5 text-3xl">🗺️</span>
                <div>
                    <h3 class="font-bold text-slate-900">National Statistics Office — Geographic Data</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        Province, district, and municipality names, codes, and hierarchy are sourced from the official
                        CBS/NSO geographic code file. Province labels use the coded names from that source (e.g., Province No. 1)
                        and may differ from later renamed versions.
                    </p>
                </div>
            </div>

            <div class="flex gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <span class="mt-0.5 text-3xl">🎓</span>
                <div>
                    <h3 class="font-bold text-slate-900">Ministry of Education — Classification System</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        Institution types (school, college, university, technical, vocational, etc.) follow the
                        classification framework used by Nepal's Ministry of Education, Science and Technology (MOEST).
                    </p>
                </div>
            </div>

            <div class="flex gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <span class="mt-0.5 text-3xl">📋</span>
                <div>
                    <h3 class="font-bold text-slate-900">Staff-Entered &amp; Verified Records</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        Individual institution records are entered by directory staff and go through a review and approval
                        workflow before being published. Draft, pending, and rejected records are never shown publicly.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Disclaimer --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-8">
            <h2 class="flex items-center gap-2 text-xl font-extrabold text-yellow-800">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                </svg>
                Important Disclaimer
            </h2>
            <div class="mt-4 space-y-3 text-sm leading-relaxed text-yellow-900">
                <p>{{ config('app.name') }} is an <strong>independent directory</strong>. It is not affiliated with any school, college, university, government body, or the Government of Nepal.</p>
                <p>A listing in this directory is <strong>not an endorsement</strong> or accreditation of any institution. Always verify institution details directly with the school or relevant government authorities.</p>
                <p>Institution data may be incomplete or out of date. If you find an error, please <a href="{{ route('contact') }}" class="font-semibold underline hover:text-yellow-800">contact us</a> so we can correct it.</p>
            </div>
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('contact') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-800">
                Get in Touch
            </a>
            <span class="mx-3 text-slate-300">or</span>
            <a href="{{ route('how-it-works') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-brand-600 bg-white px-6 py-3 font-semibold text-brand-700 shadow-sm transition hover:bg-brand-50">
                How it Works →
            </a>
        </div>
    </div>
</section>

@endsection
