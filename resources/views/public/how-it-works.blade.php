@extends('layouts.public')

@section('title', 'How it Works — '.config('app.name'))
@section('meta_description', 'Learn how Shiksha Link collects, reviews, and publishes Nepal institution data — and how to search, browse, and use the directory.')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 text-white">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-100">
            Guide
        </span>
        <h1 class="mt-4 max-w-3xl text-4xl font-extrabold tracking-tight sm:text-5xl">How the Directory Works</h1>
        <p class="mt-5 max-w-2xl text-lg leading-relaxed text-brand-100">
            Understand how institution records are collected, reviewed, and published — and how to get the most out of Shiksha Link.
        </p>
    </div>
</section>

{{-- How to Use --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-extrabold text-slate-900">Using the Directory</h2>
        <p class="mt-2 text-slate-600">Three ways to find the institution you're looking for.</p>

        <div class="mt-8 space-y-5">
            @php
            $usageSteps = [
                ['🔍', 'Search by Name',         'Use the search bar on the homepage or the Search page to find an institution by its name. Type a partial name and browse the results.'],
                ['🏫', 'Browse by Type',          'Go to Institution Types to see all categories — schools, colleges, universities, training centers, and more. Each type page lists all published institutions in that category.'],
                ['🗺️', 'Browse by Location',      'Use the geographic hierarchy: Provinces → Districts → Local Governments. Each location page shows all institutions in that area. This is useful for finding schools near a specific municipality.'],
            ];
            @endphp

            @foreach ($usageSteps as [$emoji, $title, $desc])
                <div class="flex gap-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <span class="shrink-0 text-3xl">{{ $emoji }}</span>
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $title }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- How Records Work --}}
<section class="bg-brand-50 py-16">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-extrabold text-slate-900">How Institution Records Work</h2>
        <p class="mt-2 text-slate-600">Every published institution goes through a structured review workflow.</p>

        <ol class="mt-8 space-y-5">
            @php
            $steps = [
                ['01', 'bg-blue-50 border-blue-200 text-blue-700',   '📝', 'Record Created as Draft',
                    'Staff enter institution details — name, type, location, contact — into the system. At this stage the record is a private draft. Incomplete fields (such as registration numbers or websites) are allowed.'],
                ['02', 'bg-yellow-50 border-yellow-200 text-yellow-700', '📤', 'Submitted for Review',
                    'Once a draft is ready, it is submitted for review. Status changes to Pending. The record is still not visible to the public at this point.'],
                ['03', 'bg-green-50 border-green-200 text-green-700', '✅', 'Reviewed &amp; Approved',
                    'An administrator reviews the pending record. If the information meets publication standards, it is approved and published. Approval by the directory does not constitute accreditation.'],
                ['04', 'bg-red-50 border-red-200 text-red-700',      '❌', 'Rejected Records Stay Private',
                    'If a record fails review (incorrect data, duplicate entry, etc.) it is rejected. Rejected records remain private and may be corrected and re-submitted.'],
                ['05', 'bg-slate-50 border-slate-200 text-slate-700', '🗄️', 'Archiving',
                    'Published records that become outdated or if an institution closes can be archived. Archived records are removed from public search results but kept for historical reference.'],
            ];
            @endphp

            @foreach ($steps as [$num, $style, $emoji, $title, $desc])
                <li class="flex gap-5 rounded-xl border {{ $style }} p-6 shadow-sm">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white font-black text-sm shadow-sm ring-1 ring-current">
                        {{ $num }}
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $emoji }} {!! $title !!}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $desc }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- FAQ --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-extrabold text-slate-900">Frequently Asked Questions</h2>
        <p class="mt-2 text-slate-600">Common questions about Shiksha Link and how to use it.</p>

        <div class="mt-8 space-y-4">
            @php
            $faqs = [
                ['Is Shiksha Link an official government website?',
                    'No. Shiksha Link is an independent, non-commercial directory. It is not affiliated with the Government of Nepal, the Ministry of Education, or any school or university. Geographic data is sourced from the National Statistics Office, but the site itself has no official status.'],
                ['How do I know an institution listing is accurate?',
                    'All published records have been reviewed by our team before publication. However, institution details can change. Always verify critical information (fees, programs, contact) directly with the institution.'],
                ['My school/college is missing from the directory. What should I do?',
                    'Contact us via the Contact page. Provide the institution name, type, location (province, district, municipality), and any other relevant details. Our team will review and add it.'],
                ['Can I report incorrect information?',
                    'Yes — please use the Contact page to report errors. Describe the institution and what information is wrong. We will investigate and update or correct the record.'],
                ['Which province names are shown?',
                    'The directory shows the current names: Koshi, Madhesh, Bagmati, Gandaki, Lumbini, Karnali, and Sudurpashchim. The National Statistics Office code file still labels them State 1 through State 7, and those codes remain on the records.'],
                ['Is this site free to use?',
                    'Yes. Shiksha Link is completely free. No registration is required to search or browse institution records.'],
            ];
            @endphp

            @foreach ($faqs as [$q, $a])
                <details class="group rounded-xl border border-slate-200 bg-white shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-5 font-semibold text-slate-900 hover:text-brand-700">
                        {{ $q }}
                        <svg class="h-5 w-5 shrink-0 text-slate-400 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </summary>
                    <p class="border-t border-slate-100 px-6 py-5 text-sm leading-relaxed text-slate-600">{{ $a }}</p>
                </details>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <p class="text-slate-600">Still have questions?</p>
            <a href="{{ route('contact') }}"
                class="mt-3 inline-flex items-center gap-2 rounded-xl bg-brand-700 px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-800">
                Contact Us
            </a>
        </div>
    </div>
</section>

@endsection
