@extends('layouts.admin')

@section('title', 'Universities')
@section('heading', 'Universities')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-navy">Universities</h1>
            <p class="mt-1 text-sm text-slate-500">{{ number_format($total) }} universities in the catalog, separate from school and college records.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input name="q" value="{{ $term }}" placeholder="Search universities" class="form-input sm:w-64">
            <button class="btn-primary" type="submit">Search</button>
        </form>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-[760px] w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-center">University</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Location</th>
                        <th class="px-4 py-3">Colleges</th>
                        <th class="px-4 py-3">Programs</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($universities as $university)
                        <tr>
                            <td class="px-4 py-3">
                                @php
                                    $logo = $university->logo_path
                                        ? (str_starts_with($university->logo_path, 'http') ? $university->logo_path : asset($university->logo_path))
                                        : null;
                                @endphp
                                <div class="flex items-center justify-center gap-3 text-center">
                                    @if ($logo)
                                        <img src="{{ $logo }}" alt="" class="h-10 w-10 shrink-0 rounded-lg border border-slate-200 bg-white object-contain p-0.5">
                                    @else
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-blue-900">{{ strtoupper(substr($university->abbreviation ?: $university->name, 0, 1)) }}</span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-900">{{ $university->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $university->abbreviation }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $university->university_type ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $university->province?->name ?? $university->location_label ?? '—' }}</td>
                            <td class="px-4 py-3">{{ number_format($university->institutions_count) }}</td>
                            <td class="px-4 py-3">{{ number_format($university->programs_count) }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('universities.show', $university) }}" class="text-sm font-semibold text-blue-700">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">No universities match this search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $universities->links() }}</div>
@endsection
