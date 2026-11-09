@extends('layouts.public')

@section('title', 'University academic programs in Nepal — '.config('app.name'))
@section('meta_description', 'Academic programs offered by universities in Nepal, with degree level, university, and the official source used to record each program.')
@section('canonical', route('university-programs.index'))

@section('content')
@php
    $palette = ['bg-indigo-600', 'bg-teal-600', 'bg-violet-600', 'bg-amber-600', 'bg-rose-600', 'bg-sky-600', 'bg-emerald-600'];
    $keep = array_filter([
        'q' => request('q'),
        'university' => $selectedUniversity,
        'faculty' => $selectedFaculty,
        'province' => request('province'),
        'level' => request('level'),
        'sort' => $sort !== 'az' ? $sort : null,
    ]);
@endphp
<div class="bg-[#F8FAFC] text-slate-900 dark:bg-[#0B1020] dark:text-slate-200">
    <div class="pointer-events-none fixed inset-x-0 top-0 z-50 h-1 bg-slate-200/70" aria-hidden="true"><div id="scroll-progress" class="h-full w-0 bg-gradient-to-r from-indigo-600 to-teal-500"></div></div>
    <section class="relative overflow-hidden border-b border-slate-200 dark:border-white/10">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-[#F8FAFC] via-[#EEF2FF] to-white dark:from-[#0B1020] dark:to-[#101628]" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -top-16 right-0 h-56 w-56 rounded-full bg-indigo-200/30 blur-3xl" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-6xl px-4 py-10">
            <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
                <a class="hover:text-indigo-700" href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <a class="hover:text-indigo-700" href="{{ route('universities.index') }}">Universities</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-slate-900 dark:text-white">Academic programs</span>
            </nav>
            <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-bold tracking-[0.16em] text-indigo-700 uppercase">Academic programs</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl dark:text-white">University <span class="bg-gradient-to-r from-indigo-600 to-teal-500 bg-clip-text text-transparent">academic programs</span></h1>
                    <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate-600 dark:text-slate-300"><span class="count-up" data-count="{{ $programs->total() }}">{{ number_format($programs->total()) }}</span> programs in this view. A program is listed only when a public source names it.</p>
                </div>
                <form method="get" action="{{ route('university-programs.index') }}" class="w-full lg:w-96" role="search">
                    @foreach (['university', 'faculty', 'province', 'level', 'sort'] as $field)
                        @if (request($field) && ! ($field === 'sort' && $sort === 'az'))<input type="hidden" name="{{ $field }}" value="{{ request($field) }}">@endif
                    @endforeach
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200" for="program-search">Search by name</label>
                    <div class="mt-2 flex gap-2">
                        <input id="program-search" type="search" name="q" value="{{ request('q') }}" placeholder="Program or subject" class="w-full rounded-full border border-slate-200 bg-white px-4 py-3 text-base text-slate-900 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 focus:outline-none">
                        <button class="inline-flex min-h-11 items-center rounded-full bg-gradient-to-r from-indigo-600 to-indigo-500 px-4 text-sm font-bold text-white">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-4 py-10">
        <button type="button" id="open-filters" class="mb-3 inline-flex min-h-11 items-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold md:hidden" aria-expanded="false" aria-controls="program-filters">Filters</button>
        <form id="program-filters" method="get" class="mb-6 grid max-md:hidden items-end gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:grid lg:sticky lg:top-20 lg:z-30 lg:grid-cols-6 dark:border-white/10 dark:bg-[#151b30]">
            @if (request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
            <label class="block text-sm font-semibold text-slate-700 lg:col-span-1 dark:text-slate-200">University
                <select name="university" onchange="this.form.faculty.value=''; this.form.submit()" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-base font-normal">
                    <option value="">All universities</option>
                    @foreach ($universities as $university)
                        <option value="{{ $university->id }}" @selected($selectedUniversity === $university->id)>{{ $university->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Faculty
                <select name="faculty" onchange="this.form.submit()" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-base font-normal">
                    <option value="">All faculties</option>
                    @if ($selectedUniversity)
                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty->id }}" @selected($selectedFaculty === $faculty->id)>{{ $faculty->name }}</option>
                        @endforeach
                    @else
                        @foreach ($faculties->groupBy('university_id') as $group)
                            <optgroup label="{{ $group->first()->university?->name }}">
                                @foreach ($group as $faculty)
                                    <option value="{{ $faculty->id }}" @selected($selectedFaculty === $faculty->id)>{{ $faculty->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @endif
                </select>
            </label>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Province
                <select name="province" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-base font-normal">
                    <option value="">All provinces</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->id }}" @selected(request('province') == $province->id)>{{ $province->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Degree
                <select name="level" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-base font-normal">
                    <option value="">All degree levels</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" @selected(request('level') == $level->id)>{{ $level->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Sort
                <select name="sort" onchange="this.form.submit()" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-base font-normal">
                    <option value="az" @selected($sort === 'az')>A–Z</option>
                    <option value="za" @selected($sort === 'za')>Z–A</option>
                    <option value="university" @selected($sort === 'university')>University</option>
                </select>
            </label>
            <button class="min-h-11 rounded-full bg-indigo-600 px-4 text-sm font-semibold text-white">Search</button>
        </form>
        <p class="mb-4 text-sm font-semibold text-slate-600" aria-live="polite">{{ number_format($programs->total()) }} programs</p>

        <div class="grid items-start gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div id="program-grid" class="grid gap-4 sm:grid-cols-2">
                    @forelse ($programs as $program)
                        @php
                            $universityName = $program->university?->name;
                            $tone = $universityName ? $palette[abs(crc32($universityName)) % count($palette)] : 'bg-slate-600';
                            $levelName = $program->educationLevel?->name;
                            $levelKey = strtolower((string) $levelName);
                            $badge = str_contains($levelKey, 'bachelor') ? 'bg-indigo-50 text-indigo-800' : (str_contains($levelKey, 'master') ? 'bg-violet-50 text-violet-800' : (str_contains($levelKey, 'phd') || str_contains($levelKey, 'm.phil') || str_contains($levelKey, 'mphil') ? 'bg-rose-50 text-rose-800' : (str_contains($levelKey, 'diploma') || str_contains($levelKey, 'certificate') ? 'bg-sky-50 text-sky-800' : ($levelName ? 'bg-amber-50 text-amber-800' : 'bg-slate-100 text-slate-600'))));
                            $description = (string) $program->description;
                            $source = null;
                            if (preg_match('/https?:\/\/\S+/', $description, $found) === 1) {
                                $source = rtrim($found[0], '.,)');
                            }
                            $plain = trim(preg_replace('/https?:\/\/\S+/', '', $description) ?? '');
                        @endphp
                        <article class="relative flex flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-white/10 dark:bg-white/5">
                            <a href="{{ route('university-programs.show', $program) }}" class="absolute inset-0 z-0" aria-label="{{ $program->name }}"></a>
                            <span class="relative flex items-center gap-2 {{ $tone }} px-4 py-3 text-white">
                                @if ($program->university?->logo_path)
                                    <img src="{{ asset($program->university->logo_path) }}" alt="{{ $universityName }} logo" class="h-8 w-8 shrink-0 rounded-lg bg-white object-contain p-0.5">
                                @else
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20 text-xs font-black">{{ $universityName ? strtoupper(substr($universityName, 0, 1)) : 'P' }}</span>
                                @endif
                                <span class="truncate text-sm font-semibold">{{ $universityName ?: 'Independent program' }}</span>
                            </span>
                            <span class="relative flex flex-1 flex-col p-4">
                                <span class="inline-flex w-fit rounded-full px-2 py-0.5 text-xs font-bold tracking-wide uppercase {{ $badge }}">{{ $levelName ?: 'Program' }}</span>
                                <h2 class="mt-2 line-clamp-2 text-lg font-bold text-slate-900 dark:text-white">{{ $program->name }}</h2>
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ collect([$program->faculty?->name, $program->duration])->filter()->implode(' · ') }}</p>
                                @if ($plain !== '')
                                    <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300" title="{{ $description }}">{{ \Illuminate\Support\Str::limit($plain, 120) }}</p>
                                @endif
                                @if ($source)
                                    <a href="{{ $source }}" target="_blank" rel="noopener noreferrer" class="relative z-10 mt-3 inline-flex w-fit rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">Source</a>
                                @endif
                                <p class="mt-4 text-sm font-semibold text-indigo-700">View details →</p>
                            </span>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center sm:col-span-2">
                            <p class="text-slate-600">No academic programs match this search.</p>
                            <a href="{{ route('university-programs.index') }}" class="mt-4 inline-flex min-h-11 items-center rounded-full bg-indigo-600 px-4 text-sm font-bold text-white">Clear filters</a>
                        </div>
                    @endforelse
                </div>
                @if ($programs->isNotEmpty())
                    <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-slate-600">Showing {{ $programs->firstItem() }} to {{ $programs->lastItem() }} of {{ number_format($programs->total()) }}</p>
                        <nav class="flex flex-wrap items-center gap-2" aria-label="Pagination">
                            @if ($programs->onFirstPage())
                                <span class="inline-flex min-h-11 items-center px-3 text-sm text-slate-400">Previous</span>
                            @else
                                <a class="inline-flex min-h-11 items-center rounded-full border border-slate-200 bg-white px-3 text-sm font-semibold" href="{{ $programs->previousPageUrl() }}">Previous</a>
                            @endif
                            @foreach ($programs->getUrlRange(max(1, $programs->currentPage() - 1), min($programs->lastPage(), $programs->currentPage() + 1)) as $page => $url)
                                <a class="inline-flex h-11 min-w-11 items-center justify-center rounded-full text-sm font-semibold {{ $page === $programs->currentPage() ? 'bg-gradient-to-r from-indigo-600 to-teal-500 text-white' : 'border border-slate-200 bg-white' }}" href="{{ $url }}" @if ($page === $programs->currentPage()) aria-current="page" @endif>{{ $page }}</a>
                            @endforeach
                            @if ($programs->hasMorePages())
                                <a class="inline-flex min-h-11 items-center rounded-full border border-slate-200 bg-white px-3 text-sm font-semibold" href="{{ $programs->nextPageUrl() }}">Next</a>
                            @else
                                <span class="inline-flex min-h-11 items-center px-3 text-sm text-slate-400">Next</span>
                            @endif
                        </nav>
                        <form method="get" class="flex items-center gap-2">
                            @foreach ($keep as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach
                            <label class="text-sm text-slate-600" for="go-page">Go to page</label>
                            <input id="go-page" name="page" type="number" min="1" max="{{ $programs->lastPage() }}" value="{{ $programs->currentPage() }}" class="h-11 w-20 rounded-full border border-slate-200 px-3 text-base">
                        </form>
                    </div>
                @endif
            </div>
            <aside class="lg:sticky lg:top-24">
                <div class="rounded-3xl border border-slate-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-[#151b30]">
                    <h2 class="px-2 text-sm font-bold tracking-wide text-slate-500 uppercase">Universities</h2>
                    <label class="mt-2 block px-2 text-sm font-semibold text-slate-700" for="university-filter">Find a university</label>
                    <input id="university-filter" type="search" placeholder="University name" class="mt-1 w-full rounded-full border border-slate-200 px-3 py-2 text-base">
                    <ul id="university-list" class="mt-2 max-h-96 overflow-y-auto lg:max-h-[calc(100vh-12rem)]">
                        @foreach ($universities->sortByDesc('programs_count') as $university)
                            @continue($university->programs_count === 0)
                            <li data-name="{{ strtolower($university->name) }}">
                                <a href="{{ route('university-programs.index', array_filter(['university' => $university->id, 'q' => request('q')])) }}" title="{{ $university->name }}" class="flex items-center justify-between gap-3 rounded-xl px-2 py-2 {{ $selectedUniversity === $university->id ? 'bg-indigo-50' : 'hover:bg-slate-50' }}">
                                    <span class="flex min-w-0 items-center gap-2">
                                        @if ($university->logo_path)
                                            <img src="{{ asset($university->logo_path) }}" alt="{{ $university->name }} logo" class="h-8 w-8 shrink-0 rounded-lg border border-slate-200 bg-white object-contain p-0.5">
                                        @else
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xs font-black text-indigo-800">{{ strtoupper(substr($university->abbreviation ?: $university->name, 0, 1)) }}</span>
                                        @endif
                                        <span class="truncate text-sm font-semibold text-slate-800">{{ $university->name }}</span>
                                    </span>
                                    <span class="shrink-0 rounded-full bg-indigo-50 px-2 py-0.5 text-sm font-extrabold text-indigo-800">{{ number_format($university->programs_count) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
    <button type="button" id="back-to-top" aria-label="Back to top" class="fixed right-4 bottom-4 inline-flex h-11 w-11 items-center justify-center rounded-full bg-indigo-600 text-white shadow-lg">↑</button>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('open-filters')?.addEventListener('click', (event) => {
        const form = document.getElementById('program-filters');
        const hidden = form.classList.toggle('max-md:hidden');
        event.currentTarget.setAttribute('aria-expanded', hidden ? 'false' : 'true');
    });
    document.getElementById('university-filter')?.addEventListener('input', (event) => {
        const term = event.target.value.toLowerCase();
        document.querySelectorAll('#university-list li').forEach((item) => item.classList.toggle('hidden', term !== '' && !item.dataset.name.includes(term)));
    });
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const bar = document.getElementById('scroll-progress');
    const updateBar = () => { const height = document.documentElement.scrollHeight - window.innerHeight; if (bar) bar.style.width = (height > 0 ? (window.scrollY / height) * 100 : 0) + '%'; };
    updateBar();
    window.addEventListener('scroll', updateBar, { passive: true });
    document.getElementById('back-to-top')?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' }));
    document.querySelectorAll('.count-up').forEach((el) => {
        const target = Number(el.dataset.count || 0);
        if (reduce) return;
        const start = performance.now();
        const step = (now) => { const progress = Math.min(1, (now - start) / 700); el.textContent = Math.round(target * progress).toLocaleString(); if (progress < 1) requestAnimationFrame(step); };
        requestAnimationFrame(step);
    });
</script>
@endpush
