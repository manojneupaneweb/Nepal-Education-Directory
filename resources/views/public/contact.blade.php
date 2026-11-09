@extends('layouts.public')

@section('title', 'Contact — '.config('app.name'))
@section('meta_description', 'Contact the Shiksha Link team to report missing institutions, correct errors, or submit questions about Nepal\'s education directory.')

@section('content')

{{-- ─── PAGE HEADER ──────────────────────────────────────────────────────── --}}
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
            <a class="transition hover:text-blue-700" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-slate-900">Contact</span>
        </nav>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Contact Us</h1>
        <p class="mt-2 max-w-2xl text-base text-slate-600">
            Found an error in a listing? Missing institution? Or just have a question? We'd love to hear from you.
        </p>
    </div>
</section>

{{-- ─── REASON CARDS ──────────────────────────────────────────────────────── --}}
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-widest text-slate-500">What's your reason for reaching out?</p>
        <div class="mt-5 grid gap-4 sm:grid-cols-3">
            @foreach ([
                ['missing',    'Report a Missing Institution', 'If a school, college, or university is not listed, tell us and we\'ll add it.',      'border-blue-100 bg-blue-50 hover:border-blue-300'],
                ['correction', 'Correct an Error',             'Found wrong information in a listing? Let us know what needs to be corrected.',      'border-amber-100 bg-amber-50 hover:border-amber-300'],
                ['enquiry',    'General Enquiries',            'Questions about how the directory works, data sources, or anything else.',           'border-emerald-100 bg-emerald-50 hover:border-emerald-300'],
            ] as [$value, $title, $text, $colors])
                <button type="button"
                        data-reason="{{ $value }}"
                        class="reason-card rounded border {{ $colors }} p-5 text-left transition hover:shadow-sm">
                    <h3 class="font-semibold text-slate-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $text }}</p>
                    <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-blue-700">
                        Select this reason →
                    </span>
                </button>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── CONTACT FORM ──────────────────────────────────────────────────────── --}}
<section id="contact-form" class="bg-white py-12 sm:py-16">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[2fr_1fr] lg:px-8">

        {{-- Form --}}
        <div>
            <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">Send a Message</h2>
            <p class="mt-2 max-w-[65ch] text-slate-600">
                Fill in the form below. We aim to respond within 2–3 working days.
                Fields marked <span class="text-red-600">*</span> are required.
            </p>

            {{-- Temp notice --}}
            <div class="mt-5 flex items-start gap-3 rounded border border-amber-200 bg-amber-50 p-4">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                <p class="text-sm text-amber-900">
                    <strong>Note:</strong> Messages are saved for the directory team to review.
                    Email replies are not sent automatically yet.
                </p>
            </div>

            <div id="form-card" class="mt-6 rounded border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                @if (session('status'))
                    <div class="mb-5 rounded border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900" role="status">
                        {{ session('status') }}
                    </div>
                @endif
                <form id="contact-msg" action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700">
                                Your Name <span class="text-red-600">*</span>
                            </label>
                            <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="Ramesh Sharma"
                                   class="form-input mt-2">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700">
                                Email Address <span class="text-red-600">*</span>
                            </label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}" placeholder="ramesh@example.com"
                                   class="form-input mt-2">
                        </div>
                    </div>

                    <div class="mt-5">
                        <label for="subject" class="block text-sm font-semibold text-slate-700">
                            Subject <span class="text-red-600">*</span>
                        </label>
                        <select id="subject" name="subject" required class="form-select mt-2">
                            <option value="">— Select a reason —</option>
                            <option value="missing" @selected(old('subject') === 'missing')>Report a missing institution</option>
                            <option value="correction" @selected(old('subject') === 'correction')>Correct existing information</option>
                            <option value="duplicate" @selected(old('subject') === 'duplicate')>Report a duplicate listing</option>
                            <option value="enquiry" @selected(old('subject') === 'enquiry')>General enquiry</option>
                            <option value="other" @selected(old('subject') === 'other')>Other</option>
                        </select>
                    </div>

                    <div class="mt-5">
                        <label for="institution" class="block text-sm font-semibold text-slate-700">
                            Institution Name <span class="text-xs font-normal text-slate-400">(if applicable)</span>
                        </label>
                        <input id="institution" name="institution" type="text" value="{{ old('institution') }}"
                               placeholder="e.g. Tribhuvan University, Kirtipur Campus"
                               class="form-input mt-2">
                    </div>

                    <div class="mt-5">
                        <label for="message" class="block text-sm font-semibold text-slate-700">
                            Message <span class="text-red-600">*</span>
                        </label>
                        <textarea id="message" name="message" rows="5" required
                                  placeholder="Please describe the issue or question in detail..."
                                  class="form-input mt-2 resize-none">{{ old('message') }}</textarea>
                        <p id="message-count" class="mt-1 text-right text-xs text-slate-400">0 / 1000 characters</p>
                    </div>

                    <p id="form-error" class="mt-4 hidden text-sm font-semibold text-red-700" role="alert"></p>

                    <div class="mt-6">
                        <button id="send" type="submit" class="btn-primary">
                            Send Message →
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="h-fit rounded border border-slate-200 bg-white p-6 shadow-sm lg:sticky lg:top-20">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">What happens next</p>
            <div class="mt-5 space-y-5">
                @foreach ([
                    ['1', 'You submit the form', 'Your message is received by our team.'],
                    ['2', 'We review it', 'We check the data against official sources.'],
                    ['3', 'We update the record', 'Published changes appear in the directory within 2–3 days.'],
                ] as [$step, $title, $desc])
                    <div class="flex items-start gap-4">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-800">
                            {{ $step }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $title }}</p>
                            <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 rounded border border-blue-100 bg-blue-50 p-4">
                <p class="text-xs font-semibold text-blue-800">Response time</p>
                <p class="mt-1 text-2xl font-bold text-blue-900">2–3 days</p>
                <p class="mt-0.5 text-xs text-slate-500">Working days</p>
            </div>
        </aside>
    </div>
</section>

{{-- ─── COVERAGE AREA ─────────────────────────────────────────────────────── --}}
<section class="border-t border-slate-200 bg-slate-50 py-12 sm:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">Our Coverage Area</h2>
            <p class="mt-2 text-slate-600">{{ config('app.name') }} covers educational institutions across all of Nepal.</p>
        </div>

        <div class="mt-8 flex flex-col items-center justify-center rounded border border-slate-200 bg-white py-12 text-center shadow-sm">
            <svg class="h-12 w-12 text-blue-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
            <p class="mt-4 text-lg font-bold text-slate-900">Nepal — All 7 Provinces</p>
            <p class="mt-1 text-sm text-slate-500">77 Districts · 753 Local Governments</p>
        </div>

        <div class="mt-5 grid gap-4 text-center sm:grid-cols-3">
            @foreach ([[7, 'Provinces covered'], [77, 'Districts covered'], [753, 'Local governments']] as [$count, $label])
                <div class="rounded border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="count-up text-3xl font-bold text-blue-800" data-count="{{ $count }}">{{ $count }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
.reason-card.is-selected {
    outline: 2px solid #1E3A8A;
    outline-offset: 2px;
    background: #EFF6FF;
}
</style>
@endpush

@push('scripts')
<script>
    // Reason card → auto-fill subject select
    const subject = document.getElementById('subject');
    document.querySelectorAll('.reason-card').forEach((card) => {
        card.addEventListener('click', () => {
            if (subject) subject.value = card.dataset.reason;
            document.querySelectorAll('.reason-card').forEach(c => c.classList.remove('is-selected'));
            card.classList.add('is-selected');
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            document.getElementById('contact-form')?.scrollIntoView({
                behavior: reduce ? 'auto' : 'smooth', block: 'start'
            });
        });
    });

    // Character counter
    const msgEl  = document.getElementById('message');
    const counter = document.getElementById('message-count');
    msgEl?.addEventListener('input', () => {
        counter.textContent = msgEl.value.length + ' / 1000 characters';
    });
</script>
@endpush
