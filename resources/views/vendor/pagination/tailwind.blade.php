@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $pages = collect([1, $last])
            ->merge(range(max(1, $current - 1), min($last, $current + 1)))
            ->unique()
            ->sort()
            ->values();
    @endphp
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col gap-3">
        <p class="text-center text-sm text-slate-600 sm:text-left">
            Showing
            <span class="font-semibold text-slate-900">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-semibold text-slate-900">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold text-slate-900">{{ number_format($displayTotal ?? $paginator->total()) }}</span>
        </p>

        <div class="flex items-center justify-between gap-2 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-400">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-brand-800">Previous</a>
            @endif
            <span class="text-sm font-semibold text-slate-700">{{ $current }} / {{ $last }}</span>
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex rounded-lg bg-brand-700 px-3 py-2 text-sm font-semibold text-white">Next</a>
            @else
                <span class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-400">Next</span>
            @endif
        </div>

        <div class="hidden flex-wrap items-center gap-1 sm:flex">
            @if ($paginator->onFirstPage())
                <span class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-400">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-brand-800 hover:bg-brand-50">Previous</a>
            @endif

            @foreach ($pages as $page)
                @if (! $loop->first && $page - $pages[$loop->index - 1] > 1)
                    <span class="px-1 text-sm text-slate-400">…</span>
                @endif
                @if ($page === $current)
                    <span aria-current="page" class="inline-flex min-w-9 justify-center rounded-lg bg-brand-700 px-3 py-2 text-sm font-semibold text-white">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="inline-flex min-w-9 justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-brand-50">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-brand-800 hover:bg-brand-50">Next</a>
            @else
                <span class="inline-flex rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-400">Next</span>
            @endif
        </div>
    </nav>
@endif
