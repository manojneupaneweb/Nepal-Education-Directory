@extends('layouts.admin')

@section('title', 'Institutions')
@section('heading', 'Institutions')

@section('content')

@php
    $statusClass = [
        'draft'          => 'badge badge-slate',
        'pending'        => 'badge badge-amber',
        'published'      => 'badge badge-green',
        'rejected'       => 'badge badge-red',
        'archived'       => 'badge badge-slate',
    ];
    $statusLabel = [
        'draft'          => 'Draft',
        'pending'        => 'Pending',
        'published'      => 'Published',
        'rejected'       => 'Rejected',
        'archived'       => 'Archived',
    ];
    $verifyClass = [
        'unverified' => 'badge badge-slate',
        'source_matched' => 'badge badge-blue',
        'admin_reviewed' => 'badge badge-amber',
        'institution_verified' => 'badge badge-green',
    ];
@endphp

{{-- Page header --}}
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-extrabold text-neutral-900">{{ $type?->name ?? 'Institutions' }}</h1>
        <p class="mt-1 text-sm text-neutral-500">
            Manage and moderate all institution records.
        </p>
    </div>
    <a href="{{ route('admin.institutions.create') }}"
       class="btn btn-primary shrink-0">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        New Institution
    </a>
</div>

    <form method="GET" class="mt-5 flex flex-col gap-3 sm:flex-row">
        @if ($type)
            <input type="hidden" name="type" value="{{ $type->slug }}">
        @endif
        <input name="q" value="{{ $term }}" placeholder="Search by name" class="form-input sm:max-w-sm">
        <button class="btn-primary" type="submit">Search</button>
    </form>

{{-- Stats strip --}}
<div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
    @foreach ([
        ['all', 'All', 'text-slate-800', 'bg-slate-50', ''],
        ['published', 'Published', 'text-emerald-700', 'bg-emerald-50', 'published'],
        ['pending', 'Pending', 'text-amber-700', 'bg-amber-50', 'pending'],
        ['draft', 'Draft', 'text-slate-500', 'bg-slate-50', 'draft'],
    ] as [$key, $label, $color, $bg, $statusValue])
        <a href="{{ route('admin.institutions.index', array_filter(['type' => $type?->slug, 'status' => $statusValue, 'q' => $term])) }}"
           class="rounded-xl border px-4 py-3 {{ $bg }} {{ $status === $statusValue ? 'border-blue-600' : 'border-neutral-200' }}">
            <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">{{ $label }}</p>
            <p class="mt-1 text-xl font-extrabold {{ $color }}">{{ number_format($counts[$key]) }}</p>
        </a>
    @endforeach
</div>

{{-- Table --}}
<div class="mt-6 overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-[720px] text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-widest text-slate-500">
                        Institution
                    </th>
                    <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-widest text-slate-500">
                        Type
                    </th>
                    <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-widest text-slate-500">
                        Status
                    </th>
                    <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-widest text-slate-500">
                        Verification
                    </th>
                    <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-widest text-slate-500">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($institutions as $institution)
                    @php
                        $statusVal = $institution->status->value ?? 'draft';
                        $verifyVal = $institution->verification_status->value ?? 'unverified';
                    @endphp
                    <tr class="group transition hover:bg-slate-50">
                        <td class="px-5 py-4">
                            @php
                                $logo = $institution->logo_path
                                    ? (str_starts_with($institution->logo_path, 'http') ? $institution->logo_path : asset($institution->logo_path))
                                    : null;
                            @endphp
                            <div class="flex items-center gap-3">
                                @if ($logo)
                                    <img src="{{ $logo }}" alt="" class="h-10 w-10 shrink-0 rounded-lg border border-slate-200 bg-white object-contain p-0.5">
                                @else
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-blue-900">{{ strtoupper(substr($institution->name, 0, 1)) }}</span>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('admin.institutions.show', $institution) }}"
                                       class="font-semibold text-blue-700 hover:text-blue-900">
                                        {{ $institution->name }}
                                    </a>
                                    @if ($institution->slug)
                                        <p class="mt-0.5 truncate text-xs text-slate-400">/{{ $institution->slug }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-semibold text-slate-600">
                                {{ $institution->type?->name ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass[$statusVal] ?? 'badge-draft' }}">
                                {{ $statusLabel[$statusVal] ?? ucfirst($statusVal) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $verifyClass[$verifyVal] ?? 'badge-draft' }}">
                                {{ ucfirst($verifyVal) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="relative inline-block text-left">
                                <button type="button" class="row-menu-open inline-flex h-8 w-8 items-center justify-center rounded text-slate-500 hover:bg-slate-100" aria-label="Actions for {{ $institution->name }}">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
                                </button>
                                <div class="row-menu absolute right-0 z-20 mt-1 hidden w-40 rounded-xl border border-slate-200 bg-white p-1 text-left shadow-lg">
                                    @if ($institution->slug)
                                        <a href="{{ route('institutions.show', $institution) }}" class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">View</a>
                                    @endif
                                    <a href="{{ route('admin.institutions.edit', $institution) }}" class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.institutions.destroy', $institution) }}" onsubmit="return confirm('Delete this institution?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-700 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <p class="text-4xl" aria-hidden="true">🏫</p>
                            <p class="mt-3 font-semibold text-slate-700">No institutions yet</p>
                            <p class="mt-1 text-sm text-neutral-400">Create the first institution to get started.</p>
                            <a href="{{ route('admin.institutions.create') }}"
                               class="btn btn-primary mt-5 inline-flex">
                                + New Institution
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-5">
    {{ $institutions->links() }}
</div>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.row-menu-open').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const menu = button.nextElementSibling;
            document.querySelectorAll('.row-menu').forEach((other) => {
                if (other !== menu) {
                    other.classList.add('hidden');
                }
            });
            menu.classList.toggle('hidden');
        });
    });
    document.addEventListener('click', () => {
        document.querySelectorAll('.row-menu').forEach((menu) => menu.classList.add('hidden'));
    });
</script>
@endpush
