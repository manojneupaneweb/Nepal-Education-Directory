@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    @php
        $conic = function (array $slices): string {
            $total = max(1, array_sum(array_column($slices, 'value')));
            $cursor = 0;
            $stops = [];
            foreach ($slices as $slice) {
                $end = $cursor + ($slice['value'] / $total * 100);
                $stops[] = $slice['color'].' '.$cursor.'% '.$end.'%';
                $cursor = $end;
            }

            return 'conic-gradient('.implode(', ', $stops).')';
        };
        $maxOf = fn (array $rows) => max(1, ...array_column($rows, 'value'));
    @endphp

    <section>
        <h1 class="text-2xl font-bold text-navy">Dashboard</h1>
        <p class="mt-1 text-sm text-slate-500">A live view of the directory, public activity, and items that need attention.</p>
    </section>

    <section class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Key numbers">
        @foreach ([
            ['Institutions', $stats['institutions'], route('admin.institutions.index')],
            ['Published', $stats['published'], route('admin.institutions.index')],
            ['Pending', $stats['pending'], route('admin.institutions.index')],
            ['Searches', $stats['keywords'], route('admin.keywords.index')],
            ['Messages', $stats['messages'], route('admin.messages.index')],
            ['Suggestions', $stats['suggestions'], route('admin.dashboard')],
            ['Open errors', $stats['errors'], route('admin.developer.index')],
            ['Places', $stats['provinces'].' / '.$stats['districts'].' / '.$stats['municipalities'], route('admin.dashboard')],
        ] as [$label, $value, $href])
            <a href="{{ $href }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-bold text-navy">{{ is_numeric($value) ? number_format($value) : $value }}</p>
            </a>
        @endforeach
    </section>

    <section class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3" aria-label="Public page views">
        @foreach ([
            ['School views', $stats['school_views']],
            ['College views', $stats['college_views']],
            ['University views', $stats['university_views']],
        ] as [$label, $value])
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-bold text-navy">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </section>

    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-navy">Status donut</h2>
            <div class="mt-4 flex flex-col items-center gap-4">
                <div class="relative h-36 w-36 shrink-0 rounded-full" style="background: {{ $conic($status) }}">
                    <div class="absolute inset-6 rounded-full bg-white"></div>
                </div>
                <ul class="grid w-full gap-2 text-sm text-slate-600">
                    @foreach ($status as $slice)
                        <li class="flex items-center justify-between gap-3">
                            <span class="flex min-w-0 items-center gap-2"><span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $slice['color'] }}"></span><span class="truncate">{{ $slice['label'] }}</span></span>
                            <span class="shrink-0 tabular-nums">{{ number_format($slice['value']) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-navy">Verification pie</h2>
            <div class="mt-4 flex flex-col items-center gap-4">
                <div class="h-36 w-36 shrink-0 rounded-full" style="background: {{ $conic($verification) }}"></div>
                <ul class="grid w-full gap-2 text-sm text-slate-600">
                    @foreach ($verification as $slice)
                        <li class="flex items-center justify-between gap-3">
                            <span class="flex min-w-0 items-center gap-2"><span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $slice['color'] }}"></span><span class="truncate">{{ $slice['label'] }}</span></span>
                            <span class="shrink-0 tabular-nums">{{ number_format($slice['value']) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-navy">Ownership donut</h2>
            <div class="mt-4 flex flex-col items-center gap-4">
                <div class="relative h-36 w-36 shrink-0 rounded-full" style="background: {{ $conic($ownership) }}">
                    <div class="absolute inset-6 rounded-full bg-white"></div>
                </div>
                <ul class="grid w-full gap-2 text-sm text-slate-600">
                    @foreach ($ownership as $slice)
                        <li class="flex items-center justify-between gap-3">
                            <span class="flex min-w-0 items-center gap-2"><span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $slice['color'] }}"></span><span class="truncate">{{ $slice['label'] }}</span></span>
                            <span class="shrink-0 tabular-nums">{{ number_format($slice['value']) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-navy">Institutions by type</h2>
            @php $typeMax = max(1, $types->max('institutions_count') ?? 1); @endphp
            <div class="mt-4 flex h-48 items-end gap-3">
                @forelse ($types as $type)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="w-full rounded-t-lg bg-blue-600" style="height: {{ max(4, ($type->institutions_count / $typeMax) * 100) }}%"></div>
                        <p class="max-w-full truncate text-[11px] text-slate-500">{{ $type->name }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No institution types yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-navy">Institutions by province</h2>
            @php $provinceMax = max(1, $provinces->max('institutions_count') ?? 1); @endphp
            <ul class="mt-4 space-y-2">
                @forelse ($provinces as $province)
                    <li>
                        <div class="mb-1 flex justify-between text-xs text-slate-600"><span>{{ $province->name }}</span><span>{{ number_format($province->institutions_count) }}</span></div>
                        <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-navy" style="width: {{ ($province->institutions_count / $provinceMax) * 100 }}%"></div></div>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">No provinces yet.</li>
                @endforelse
            </ul>
        </section>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-navy">Searches this week</h2>
            @php
                $searchMax = $maxOf($searches);
                $points = collect($searches)->values()->map(function ($point, $index) use ($searches, $searchMax) {
                    $x = count($searches) === 1 ? 0 : ($index / (count($searches) - 1)) * 100;
                    $y = 100 - (($point['value'] / $searchMax) * 100);

                    return $x.','.$y;
                })->implode(' ');
            @endphp
            <svg viewBox="0 0 100 100" class="mt-4 h-40 w-full" preserveAspectRatio="none" role="img" aria-label="Search line chart">
                <polyline fill="none" stroke="#2563EB" stroke-width="2" points="{{ $points }}" vector-effect="non-scaling-stroke" />
            </svg>
            <div class="mt-2 flex justify-between text-[11px] text-slate-500">
                @foreach ($searches as $point)<span>{{ $point['label'] }}</span>@endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-navy">New records by month</h2>
            @php $monthMax = $maxOf($monthly); @endphp
            <div class="mt-4 flex h-40 items-end gap-3">
                @foreach ($monthly as $point)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="w-full rounded-t-md bg-red-500/80" style="height: {{ max(4, ($point['value'] / $monthMax) * 100) }}%"></div>
                        <span class="text-[11px] text-slate-500">{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5">
        <h2 class="font-semibold text-navy">Inbox columns</h2>
        @php $inboxMax = max(1, ...array_column($inbox, 1)); @endphp
        <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach ($inbox as [$label, $value, $color])
                <div>
                    <div class="flex h-32 items-end rounded-xl bg-slate-50 p-2">
                        <div class="w-full rounded-md" style="height: {{ max(8, ($value / $inboxMax) * 100) }}%; background: {{ $color }}"></div>
                    </div>
                    <p class="mt-2 text-sm font-semibold text-slate-800">{{ number_format($value) }}</p>
                    <p class="text-xs text-slate-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between"><h2 class="font-semibold text-navy">Recent searches</h2><a href="{{ route('admin.keywords.index') }}" class="text-sm text-blue-700">View all</a></div>
            <ul class="mt-3 divide-y divide-slate-100 text-sm">
                @forelse ($recentKeywords as $keyword)
                    <li class="flex justify-between gap-3 py-2"><span>{{ $keyword->term }}</span><span class="text-slate-400">{{ $keyword->created_at->diffForHumans() }}</span></li>
                @empty
                    <li class="py-2 text-slate-500">No searches yet.</li>
                @endforelse
            </ul>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between"><h2 class="font-semibold text-navy">Recent messages</h2><a href="{{ route('admin.messages.index') }}" class="text-sm text-blue-700">View all</a></div>
            <ul class="mt-3 divide-y divide-slate-100 text-sm">
                @forelse ($recentMessages as $message)
                    <li class="py-2"><p class="font-semibold">{{ $message->name }}</p><p class="truncate text-slate-500">{{ $message->message }}</p></li>
                @empty
                    <li class="py-2 text-slate-500">No messages yet.</li>
                @endforelse
            </ul>
        </section>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between"><h2 class="font-semibold text-navy">Open errors</h2><a href="{{ route('admin.developer.index') }}" class="text-sm text-blue-700">Developer</a></div>
            <ul class="mt-3 space-y-2 text-sm">
                @forelse ($openErrors as $error)
                    <li class="rounded-xl bg-red-50 px-3 py-2 text-red-900">{{ $error->message }}</li>
                @empty
                    <li class="text-slate-500">No open errors.</li>
                @endforelse
            </ul>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="font-semibold text-navy">Latest suggestions</h2>
            <ul class="mt-3 divide-y divide-slate-100 text-sm">
                @forelse ($suggestions as $suggestion)
                    <li class="py-2"><p class="font-semibold">{{ $suggestion->institution_name }}</p><p class="text-slate-500">{{ $suggestion->submitter_email }}</p></li>
                @empty
                    <li class="py-2 text-slate-500">No suggestions yet.</li>
                @endforelse
            </ul>
        </section>
    </div>

    <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5">
        <div class="flex items-center justify-between"><h2 class="font-semibold text-navy">Newest institutions</h2><a href="{{ route('admin.institutions.index') }}" class="text-sm text-blue-700">Manage</a></div>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-[640px] w-full text-sm">
                <thead class="text-left text-xs uppercase text-slate-500"><tr><th class="py-2">Name</th><th>Type</th><th>Status</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentInstitutions as $institution)
                        <tr>
                            <td class="py-2 font-semibold"><a href="{{ route('admin.institutions.show', $institution) }}" class="text-blue-800">{{ $institution->name }}</a></td>
                            <td>{{ $institution->type?->name ?? '—' }}</td>
                            <td>{{ $institution->status->value }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-3 text-slate-500">No institutions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-4 rounded-2xl border border-blue-100 bg-blue-50 p-5">
        <h2 class="font-semibold text-navy">Directory coverage</h2>
        <p class="mt-2 text-sm text-slate-700">{{ number_format($stats['institutions']) }} institution records across {{ number_format($stats['provinces']) }} provinces, {{ number_format($stats['districts']) }} districts, and {{ number_format($stats['municipalities']) }} local governments. Published information stays free to search.</p>
    </section>
@endsection
