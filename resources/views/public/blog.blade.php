@extends('layouts.public')

@section('title', 'Blog — '.config('app.name'))
@section('meta_description', 'Articles, guides, and updates about education in Nepal from the '.config('app.name').' team.')

@section('content')
@php
    $toneClass = [
        'Overview'          => 'bg-blue-50 text-blue-800',
        'Universities'      => 'bg-violet-50 text-violet-800',
        'Schools'           => 'bg-teal-50 text-teal-800',
        'Exams'             => 'bg-amber-50 text-amber-800',
        'Medical'           => 'bg-rose-50 text-rose-800',
        'Policy'            => 'bg-slate-100 text-slate-800',
        'Scholarships'      => 'bg-emerald-50 text-emerald-800',
        'Data & Statistics' => 'bg-sky-50 text-sky-800',
        'Digital Education' => 'bg-cyan-50 text-cyan-800',
        'Study Abroad'      => 'bg-orange-50 text-orange-800',
        'Teachers'          => 'bg-pink-50 text-pink-800',
        'Vocational'        => 'bg-lime-50 text-lime-800',
    ];
    $featured = $blogs->currentPage() === 1 ? $blogs->first() : null;
@endphp

{{-- ─── PAGE HEADER ──────────────────────────────────────────────────────── --}}
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
            <a class="transition hover:text-blue-700" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-slate-900">Blog</span>
        </nav>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Education Articles &amp; Updates</h1>
        <p class="mt-2 max-w-2xl text-base text-slate-600">Articles, guides, and news about schools, colleges, and universities across Nepal.</p>
        <div class="mt-5 flex gap-4">
            <div class="rounded border border-slate-200 bg-slate-50 px-4 py-3 text-center">
                <p class="count-up text-xl font-bold text-blue-800" data-count="{{ $blogs->total() }}">{{ number_format($blogs->total()) }}</p>
                <p class="mt-0.5 text-xs font-semibold text-slate-500">Articles</p>
            </div>
            <div class="rounded border border-slate-200 bg-slate-50 px-4 py-3 text-center">
                <p class="count-up text-xl font-bold text-blue-800" data-count="{{ $categories->count() }}">{{ $categories->count() }}</p>
                <p class="mt-0.5 text-xs font-semibold text-slate-500">Categories</p>
            </div>
        </div>
    </div>
</section>

{{-- ─── CATEGORY FILTER BAR ───────────────────────────────────────────────── --}}
<section class="sticky top-[61px] z-30 border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl overflow-x-auto px-4 py-2.5 sm:px-6 lg:px-8">
        <div class="flex min-w-max items-center gap-2" id="category-filters">
            <button type="button" onclick="filterCategory('all')" data-cat="all"
                    class="filter-btn active inline-flex min-h-9 items-center rounded px-3 text-xs font-semibold">All</button>
            @foreach($categories as $cat)
                <button type="button" onclick="filterCategory('{{ $cat }}')" data-cat="{{ $cat }}"
                        class="filter-btn inline-flex min-h-9 items-center gap-2 rounded px-3 text-xs font-semibold">
                    {{ $cat }}
                    <span class="rounded bg-slate-100 px-1.5 text-[10px] text-slate-600">{{ $categoryCounts[$cat] ?? 0 }}</span>
                </button>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── ARTICLES ──────────────────────────────────────────────────────────── --}}
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center gap-4">
            <label class="sr-only" for="blog-search">Search articles</label>
            <input type="search" id="blog-search" placeholder="Search articles…"
                   oninput="filterBlogs()"
                   class="form-input max-w-sm">
            <p id="result-count" class="text-sm font-semibold text-slate-600" aria-live="polite">{{ number_format($blogs->total()) }} articles</p>
        </div>

        {{-- Featured article --}}
        @if ($featured)
            <article id="featured" data-cat="{{ $featured['category'] }}"
                     data-title="{{ strtolower($featured['title']) }}"
                     data-excerpt="{{ strtolower($featured['excerpt']) }}"
                     class="mb-8 overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
                <div class="grid lg:grid-cols-2">
                    <div class="flex h-48 items-center justify-center text-5xl {{ $toneClass[$featured['category']] ?? 'bg-slate-50 text-slate-700' }} lg:h-full">
                        {{ $featured['image_emoji'] }}
                    </div>
                    <div class="p-6">
                        <span class="rounded bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">Featured</span>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="rounded bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">{{ $featured['category'] }}</span>
                            <span class="text-xs text-slate-400">· {{ $featured['read_time'] }} min read</span>
                        </div>
                        <h2 class="mt-3 text-xl font-bold text-slate-900">{{ $featured['title'] }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $featured['excerpt'] }}</p>
                        <p class="mt-4 text-xs font-semibold text-slate-700">
                            {{ $featured['author'] }} · <time datetime="{{ $featured['published_at'] }}">{{ \Carbon\Carbon::parse($featured['published_at'])->format('M j, Y') }}</time>
                        </p>
                        <p class="mt-3 text-sm font-semibold text-blue-700">Read →</p>
                    </div>
                </div>
            </article>
        @endif

        {{-- Article grid --}}
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3" id="blog-grid">
            @foreach($blogs as $post)
                <article data-cat="{{ $post['category'] }}"
                         data-title="{{ strtolower($post['title']) }}"
                         data-excerpt="{{ strtolower($post['excerpt']) }}"
                         data-read="{{ $post['read_time'] }}"
                         data-date="{{ $post['published_at'] }}"
                         @class(['blog-card flex flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm transition duration-150 hover:border-blue-300 hover:shadow-md', 'featured-copy' => $featured && $post['id'] === $featured['id']])>
                    <div class="flex h-32 items-center justify-center text-5xl {{ $toneClass[$post['category']] ?? 'bg-slate-50 text-slate-700' }}">
                        {{ $post['image_emoji'] }}
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="mb-3 flex items-center gap-2">
                            <span class="rounded bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">{{ $post['category'] }}</span>
                            <span class="text-xs text-slate-400">· {{ $post['read_time'] }} min read</span>
                        </div>
                        <h2 class="line-clamp-2 text-sm font-bold leading-snug text-slate-900">{{ $post['title'] }}</h2>
                        <p class="mt-2 line-clamp-3 flex-1 text-sm leading-relaxed text-slate-500">{{ $post['excerpt'] }}</p>
                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                            <div>
                                <p class="text-xs font-semibold text-slate-700">{{ $post['author'] }}</p>
                                <p class="text-xs text-slate-400">
                                    <time datetime="{{ $post['published_at'] }}">{{ \Carbon\Carbon::parse($post['published_at'])->format('M j, Y') }}</time>
                                </p>
                            </div>
                            <span class="text-xs font-semibold text-blue-700">Read →</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-600">Showing {{ $blogs->firstItem() }} to {{ $blogs->lastItem() }} of {{ number_format($blogs->total()) }}</p>
            @if ($blogs->hasPages())
                <nav class="flex flex-wrap items-center gap-2" aria-label="Pagination">
                    @if ($blogs->onFirstPage())
                        <span class="inline-flex min-h-9 items-center px-3 text-sm text-slate-400">Previous</span>
                    @else
                        <a class="inline-flex min-h-9 items-center rounded border border-slate-200 bg-white px-3 text-sm font-semibold hover:bg-slate-50"
                           href="{{ $blogs->previousPageUrl() }}">Previous</a>
                    @endif
                    @foreach ($blogs->getUrlRange(max(1, $blogs->currentPage() - 1), min($blogs->lastPage(), $blogs->currentPage() + 1)) as $page => $url)
                        <a class="inline-flex h-9 min-w-9 items-center justify-center rounded text-sm font-semibold
                            {{ $page === $blogs->currentPage() ? 'bg-blue-700 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                           href="{{ $url }}"
                           @if ($page === $blogs->currentPage()) aria-current="page" @endif>{{ $page }}</a>
                    @endforeach
                    @if ($blogs->hasMorePages())
                        <a class="inline-flex min-h-9 items-center rounded border border-slate-200 bg-white px-3 text-sm font-semibold hover:bg-slate-50"
                           href="{{ $blogs->nextPageUrl() }}">Next</a>
                    @else
                        <span class="inline-flex min-h-9 items-center px-3 text-sm text-slate-400">Next</span>
                    @endif
                </nav>
            @endif
        </div>

        {{-- No results --}}
        <div id="no-results" class="hidden py-20 text-center">
            <p class="text-base font-semibold text-slate-700">No articles found</p>
            <p class="mt-1 text-sm text-slate-500">Try a different search term or category.</p>
            <button type="button"
                    onclick="filterCategory('all'); document.getElementById('blog-search').value=''; filterBlogs();"
                    class="btn-primary mt-4 inline-flex">Clear filters</button>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    .filter-btn { border: 1px solid #e2e8f0; background: white; color: #475569; }
    .filter-btn.active { background: #1E3A8A; color: white; border-color: #1E3A8A; }
    .featured-copy { display: none; }
</style>
@endpush

@push('scripts')
<script>
    let activeCategory = 'all';
    function filterCategory(cat) {
        activeCategory = cat;
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.toggle('active', btn.dataset.cat === cat));
        filterBlogs();
    }
    function filterBlogs() {
        const query = document.getElementById('blog-search').value.toLowerCase().trim();
        const featured = document.getElementById('featured');
        const showFeatured = featured && activeCategory === 'all' && query === '' && (featured.dataset.cat);
        if (featured) featured.style.display = showFeatured ? '' : 'none';
        let visible = 0;
        document.querySelectorAll('.blog-card').forEach(card => {
            const catMatch = activeCategory === 'all' || card.dataset.cat === activeCategory;
            const textMatch = !query || card.dataset.title.includes(query) || card.dataset.excerpt.includes(query);
            const hideDuplicate = showFeatured && card.classList.contains('featured-copy');
            const show = catMatch && textMatch && !hideDuplicate;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (showFeatured) visible++;
        document.getElementById('no-results').classList.toggle('hidden', visible > 0);
        document.getElementById('result-count').textContent = visible + ' articles on this page';
    }
</script>
@endpush
