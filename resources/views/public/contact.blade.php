@extends('layouts.public')

@section('title', 'Contact — '.config('app.name'))
@section('meta_description', 'Contact the Shiksha Link team to report missing institutions, correct errors, or submit questions about Nepal\'s education directory.')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 text-white">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-100">
            Get in Touch
        </span>
        <h1 class="mt-4 max-w-3xl text-4xl font-extrabold tracking-tight sm:text-5xl">Contact Us</h1>
        <p class="mt-5 max-w-2xl text-lg leading-relaxed text-brand-100">
            Found an error in a listing? Missing institution? Or just have a question?
            We'd love to hear from you.
        </p>
    </div>
</section>

{{-- Contact info cards --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="grid gap-5 sm:grid-cols-3">
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-6 text-center shadow-sm">
                <span class="text-4xl">🏫</span>
                <h3 class="mt-3 font-bold text-blue-800">Report a Missing Institution</h3>
                <p class="mt-2 text-sm text-slate-600">If a school, college, or university is not listed, tell us and we'll add it.</p>
            </div>
            <div class="rounded-xl border border-orange-200 bg-orange-50 p-6 text-center shadow-sm">
                <span class="text-4xl">✏️</span>
                <h3 class="mt-3 font-bold text-orange-800">Correct an Error</h3>
                <p class="mt-2 text-sm text-slate-600">Found wrong information in a listing? Let us know what needs to be corrected.</p>
            </div>
            <div class="rounded-xl border border-green-200 bg-green-50 p-6 text-center shadow-sm">
                <span class="text-4xl">💬</span>
                <h3 class="mt-3 font-bold text-green-800">General Enquiries</h3>
                <p class="mt-2 text-sm text-slate-600">Questions about how the directory works, data sources, or anything else.</p>
            </div>
        </div>
    </div>
</section>

{{-- Contact form --}}
<section class="bg-brand-50 py-16">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-extrabold text-slate-900">Send a Message</h2>
        <p class="mt-2 text-slate-600">
            Fill in the form below. We aim to respond within 2–3 working days.
            All fields marked <span class="text-red-500">*</span> are required.
        </p>

        <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <form action="#" method="POST" novalidate>
                @csrf
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700">
                            Your Name <span class="text-red-500">*</span>
                        </label>
                        <input id="name" name="name" type="text" required
                            placeholder="Ramesh Sharma"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm placeholder-slate-400 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-100">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input id="email" name="email" type="email" required
                            placeholder="ramesh@example.com"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm placeholder-slate-400 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-100">
                    </div>
                </div>

                <div class="mt-5">
                    <label for="subject" class="block text-sm font-semibold text-slate-700">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select id="subject" name="subject" required
                        class="mt-1.5 w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        <option value="">— Select a reason —</option>
                        <option value="missing">Report a missing institution</option>
                        <option value="correction">Correct existing information</option>
                        <option value="duplicate">Report a duplicate listing</option>
                        <option value="enquiry">General enquiry</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="mt-5">
                    <label for="institution" class="block text-sm font-semibold text-slate-700">
                        Institution Name (if applicable)
                    </label>
                    <input id="institution" name="institution" type="text"
                        placeholder="e.g. Tribhuvan University, Kirtipur Campus"
                        class="mt-1.5 w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm placeholder-slate-400 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>

                <div class="mt-5">
                    <label for="message" class="block text-sm font-semibold text-slate-700">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea id="message" name="message" rows="5" required
                        placeholder="Please describe the issue or question in detail..."
                        class="mt-1.5 w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm placeholder-slate-400 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-100"></textarea>
                </div>

                <div class="mt-6 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                    <strong>Note:</strong> This contact form does not currently send emails. A contact channel is being set up. For urgent corrections, please check back soon.
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="w-full rounded-xl bg-brand-700 px-6 py-4 font-bold text-white shadow-sm transition hover:bg-brand-800 hover:shadow-md active:scale-[0.99] sm:w-auto sm:px-10">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Map placeholder --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-extrabold text-slate-900">Our Coverage Area</h2>
        <p class="mt-2 text-slate-600">Shiksha Link covers educational institutions across all of Nepal.</p>

        <div class="mt-6 flex h-64 items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 text-center">
            <div>
                <span class="text-5xl">🗺️</span>
                <p class="mt-3 font-semibold text-slate-500">Nepal — All 7 Provinces</p>
                <p class="mt-1 text-sm text-slate-400">77 Districts · 753 Local Governments</p>
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-3 text-center text-sm">
            <div class="rounded-xl border border-brand-100 bg-brand-50 p-4">
                <p class="text-2xl font-extrabold text-brand-800">7</p>
                <p class="text-slate-600">Provinces covered</p>
            </div>
            <div class="rounded-xl border border-brand-100 bg-brand-50 p-4">
                <p class="text-2xl font-extrabold text-brand-800">77</p>
                <p class="text-slate-600">Districts covered</p>
            </div>
            <div class="rounded-xl border border-brand-100 bg-brand-50 p-4">
                <p class="text-2xl font-extrabold text-brand-800">753</p>
                <p class="text-slate-600">Local governments</p>
            </div>
        </div>
    </div>
</section>

@endsection
