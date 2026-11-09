@extends('layouts.admin')

@section('title', 'Keywords')
@section('heading', 'Keywords')

@section('content')
    <h1 class="text-2xl font-bold text-slate-950">Search keywords</h1>
    <p class="mt-2 text-sm text-slate-600">Terms people typed into search on the public site.</p>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-[640px] w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Keyword</th>
                        <th class="px-4 py-3">Results</th>
                        <th class="px-4 py-3">Searched</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($keywords as $keyword)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $keyword->term }}</td>
                            <td class="px-4 py-3">{{ number_format($keyword->results_count) }}</td>
                            <td class="px-4 py-3 text-slate-500"><time class="ago" datetime="{{ $keyword->created_at->toIso8601String() }}">{{ $keyword->created_at->diffForHumans() }}</time></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-slate-500">No public searches have been recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $keywords->links() }}</div>
@endsection

@push('scripts')
<script>
    const formatAgo = (iso) => {
        const seconds = Math.max(0, Math.floor((Date.now() - new Date(iso).getTime()) / 1000));
        if (seconds < 60) {
            return seconds + ' s';
        }
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) {
            return minutes + ' min';
        }
        const hours = Math.floor(minutes / 60);
        if (hours < 24) {
            return hours + ' hr';
        }
        const days = Math.floor(hours / 24);
        if (days < 30) {
            return days + ' d';
        }
        const months = Math.floor(days / 30);
        if (months < 12) {
            return months + ' mo';
        }
        return Math.floor(months / 12) + ' yr';
    };

    const tickAgo = () => {
        document.querySelectorAll('time.ago').forEach((node) => {
            node.textContent = formatAgo(node.dateTime);
        });
    };
    tickAgo();
    setInterval(tickAgo, 1000);
</script>
@endpush
