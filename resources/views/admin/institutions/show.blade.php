@extends('layouts.admin')

@section('title', $institution->name)
@section('heading', 'Institution')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">{{ $institution->name }}</h1>
            @if ($institution->name_ne)
                <p class="mt-1 text-slate-700">{{ $institution->name_ne }}</p>
            @endif
        </div>
        <a href="{{ route('admin.institutions.edit', $institution) }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold">Edit</a>
    </div>
    @if (session('status'))
        <p class="mt-4 rounded-md bg-blue-50 px-3 py-2 text-sm text-blue-900">{{ session('status') }}</p>
    @endif
    @if ($errors->any())
        <ul class="mt-4 list-disc pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <dl class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-white p-5 sm:grid-cols-2">
        <div><dt class="text-sm text-slate-500">Type</dt><dd>{{ $institution->type?->name }}</dd></div>
        <div><dt class="text-sm text-slate-500">Slug</dt><dd>{{ $institution->slug }}</dd></div>
        <div><dt class="text-sm text-slate-500">Publication</dt><dd>{{ $institution->status->value }}</dd></div>
        <div><dt class="text-sm text-slate-500">Verification</dt><dd>{{ $institution->verification_status->value }}</dd></div>
        <div><dt class="text-sm text-slate-500">Ownership</dt><dd>{{ $institution->ownership_type?->value ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500">Management</dt><dd>{{ $institution->management_type?->value ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500">Location</dt><dd>{{ $institution->municipality?->name }}, {{ $institution->district?->name }}, {{ $institution->province?->name }}</dd></div>
        <div><dt class="text-sm text-slate-500">Categories</dt><dd>{{ $institution->categories->pluck('name')->join(', ') ?: '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500">Parent</dt><dd>{{ $institution->parentInstitution?->name ?? '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500">Branches</dt><dd>{{ $institution->branches->pluck('name')->join(', ') ?: '—' }}</dd></div>
        <div><dt class="text-sm text-slate-500">Contacts</dt><dd>{{ $institution->contacts->count() }}</dd></div>
        <div><dt class="text-sm text-slate-500">Programs</dt><dd>{{ $institution->programs->count() }}</dd></div>
        <div><dt class="text-sm text-slate-500">Images</dt><dd>{{ $institution->images->count() }}</dd></div>
        <div><dt class="text-sm text-slate-500">Sources</dt><dd>{{ $institution->institutionSources->count() }}</dd></div>
        <div><dt class="text-sm text-slate-500">Verification records</dt><dd>{{ $institution->verifications->count() }}</dd></div>
    </dl>

    <div class="mt-6 flex flex-wrap gap-3">
        @if (in_array($institution->status->value, ['draft', 'rejected'], true))
            <form method="post" action="{{ route('admin.institutions.submit', $institution) }}">@csrf<button class="rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white">Submit for review</button></form>
        @endif
        @if ($institution->status->value === 'pending')
            <form method="post" action="{{ route('admin.institutions.approve', $institution) }}">@csrf<button class="rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white">Approve</button></form>
            <form method="post" action="{{ route('admin.institutions.reject', $institution) }}" class="flex gap-2">
                @csrf
                <input name="reason" placeholder="Optional reason" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
                <button class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold">Reject</button>
            </form>
        @endif
        @if ($institution->status->value !== 'archived')
            <form method="post" action="{{ route('admin.institutions.archive', $institution) }}">@csrf<button class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold">Archive</button></form>
        @endif
        <form method="post" action="{{ route('admin.institutions.destroy', $institution) }}">@csrf @method('DELETE')<button class="rounded-md border border-red-200 px-4 py-2 text-sm font-semibold text-red-700">Delete</button></form>
    </div>
@endsection
