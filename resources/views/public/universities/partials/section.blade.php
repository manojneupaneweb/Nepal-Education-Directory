@if (filled($body))
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900"><span class="h-6 w-4 rounded bg-brand-700" aria-hidden="true"></span>{{ $title }}</h2>
        <div class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $body }}</div>
    </section>
@endif
