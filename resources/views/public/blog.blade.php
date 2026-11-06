@extends('layouts.public')

@section('title', 'Blog — '.config('app.name'))
@section('meta_description', 'Articles, guides, and updates about education in Nepal from the '.config('app.name').' team.')

@section('content')

{{-- ─── HERO ─────────────────────────────────────────────────────── --}}
<section class="bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 text-white">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-100">
            Blog
        </span>
        <h1 class="mt-4 max-w-3xl text-4xl font-extrabold tracking-tight sm:text-5xl">
            Education Insights & Updates
        </h1>
        <p class="mt-5 max-w-2xl text-lg leading-relaxed text-brand-100">
            Articles, guides, and news about schools, colleges, and universities across Nepal.
        </p>

        {{-- Stats strip --}}
        <div class="mt-10 flex flex-wrap gap-6">
            <div class="rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-center backdrop-blur">
                <p class="text-2xl font-extrabold">{{ number_format($blogs->total()) }}</p>
                <p class="mt-0.5 text-xs font-semibold text-brand-100">Articles</p>
            </div>
            <div class="rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-center backdrop-blur">
                <p class="text-2xl font-extrabold">{{ $categories->count() }}</p>
                <p class="mt-0.5 text-xs font-semibold text-brand-100">Categories</p>
            </div>
        </div>
    </div>
</section>

{{-- ─── CATEGORY FILTERS ─────────────────────────────────────────── --}}
<section class="sticky top-[61px] z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto max-w-6xl overflow-x-auto px-4 py-3">
        <div class="flex min-w-max items-center gap-2" id="category-filters">
            <button
                onclick="filterCategory('all')"
                data-cat="all"
                class="filter-btn active rounded-full px-4 py-1.5 text-xs font-semibold transition">
                All
            </button>
            @foreach($categories as $cat)
                <button
                    onclick="filterCategory('{{ $cat }}')"
                    data-cat="{{ $cat }}"
                    class="filter-btn rounded-full px-4 py-1.5 text-xs font-semibold transition">
                    {{ $cat }}
                </button>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── BLOG GRID ────────────────────────────────────────────────── --}}
<section class="bg-slate-50 py-12">
    <div class="mx-auto max-w-6xl px-4">

        {{-- Search --}}
        <div class="mb-8">
            <input
                type="text"
                id="blog-search"
                placeholder="Search articles…"
                oninput="filterBlogs()"
                class="w-full max-w-md rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
        </div>

        {{-- Cards --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" id="blog-grid">

            @foreach($blogs as $post)
            <article
                data-cat="{{ $post['category'] }}"
                data-title="{{ strtolower($post['title']) }}"
                data-excerpt="{{ strtolower($post['excerpt']) }}"
                class="blog-card group flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                {{-- Thumbnail --}}
                <div class="flex h-36 items-center justify-center rounded-t-2xl bg-gradient-to-br from-brand-50 to-brand-100 text-6xl">
                    {{ $post['image_emoji'] }}
                </div>

                {{-- Body --}}
                <div class="flex flex-1 flex-col p-5">
                    {{-- Category + read time --}}
                    <div class="mb-3 flex items-center gap-2">
                        <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-semibold text-brand-700">
                            {{ $post['category'] }}
                        </span>
                        <span class="text-xs text-slate-400">· {{ $post['read_time'] }} min read</span>
                    </div>

                    {{-- Title --}}
                    <h2 class="text-base font-bold leading-snug text-slate-800 group-hover:text-brand-700">
                        {{ $post['title'] }}
                    </h2>

                    {{-- Excerpt --}}
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-500 line-clamp-3">
                        {{ $post['excerpt'] }}
                    </p>

                    {{-- Footer --}}
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-700 text-xs font-bold text-white">
                                {{ strtoupper(substr($post['author'], 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-700">{{ $post['author'] }}</p>
                                <p class="text-xs text-slate-400">
                                    {{ \Carbon\Carbon::parse($post['published_at'])->format('M j, Y') }}
                                </p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-brand-600 group-hover:underline">Read →</span>
                    </div>
                </div>
            </article>
            @endforeach

        </div>

        <div class="mt-8">{{ $blogs->links() }}</div>

        {{-- Empty state --}}
        <div id="no-results" class="hidden py-20 text-center">
            <p class="text-5xl">🔍</p>
            <p class="mt-4 text-base font-semibold text-slate-700">No articles found</p>
            <p class="mt-1 text-sm text-slate-400">Try a different search term or category.</p>
        </div>

    </div>
</section>

@endsection

@push('styles')
<style>
    .filter-btn {
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
    }
    .filter-btn:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .filter-btn.active {
        background: #1d4ed8;
        color: white;
        border-color: #1d4ed8;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script>
    let activeCategory = 'all';

    function filterCategory(cat) {
        activeCategory = cat;

        // Update button styles
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.cat === cat);
        });

        filterBlogs();
    }

    function filterBlogs() {
        const query = document.getElementById('blog-search').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.blog-card');
        let visible = 0;

        cards.forEach(card => {
            const catMatch = activeCategory === 'all' || card.dataset.cat === activeCategory;
            const textMatch = !query ||
                card.dataset.title.includes(query) ||
                card.dataset.excerpt.includes(query);

            const show = catMatch && textMatch;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('no-results').classList.toggle('hidden', visible > 0);
    }
</script>
@endpush
