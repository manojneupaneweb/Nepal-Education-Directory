@extends('layouts.admin')

@section('title', 'Source review')
@section('heading', 'Source review')

@section('content')
    <h1 class="text-2xl font-bold">Source review</h1>
    <p class="mt-2 max-w-3xl text-sm text-slate-600">Published institutions that already have an official website and are missing the selected field. Open the website yourself, then record only a value that page shows.</p>

    <form method="get" class="mt-4 flex flex-wrap gap-2 text-sm">
        <select name="type" class="rounded-md border border-slate-300 px-3 py-2">
            <option value="school" @selected($typeSlug === 'school')>Schools</option>
            <option value="college" @selected($typeSlug === 'college')>Colleges</option>
        </select>
        <select name="gap" class="rounded-md border border-slate-300 px-3 py-2">
            <option value="phone" @selected($gap === 'phone')>Missing phone</option>
            <option value="email" @selected($gap === 'email')>Missing email</option>
            <option value="description" @selected($gap === 'description')>Short or missing description</option>
        </select>
        <select name="evidence" class="rounded-md border border-slate-300 px-3 py-2">
            <option value="">No evidence filter</option>
            <option value="pending" @selected($review === 'pending')>Pending evidence</option>
            <option value="accepted" @selected($review === 'accepted')>Accepted evidence</option>
            <option value="rejected" @selected($review === 'rejected')>Rejected evidence</option>
        </select>
        <label class="flex items-center gap-2"><input type="checkbox" name="checked" value="1" @checked(request()->boolean('checked'))> Checked in 30 days</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="include_complete" value="1" @checked(request()->boolean('include_complete'))> Include complete records</label>
        <button class="rounded-md bg-brand-700 px-4 py-2 font-semibold text-white">Filter</button>
    </form>

    <ul class="mt-6 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
        @forelse ($institutions as $institution)
            <li class="px-4 py-3">
                <a class="font-semibold text-brand-800" href="{{ route('admin.source-review.show', $institution) }}">{{ $institution->name }}</a>
                <p class="text-sm text-slate-600">{{ $institution->municipality?->name }}, {{ $institution->district?->name }}</p>
            </li>
        @empty
            <li class="px-4 py-8 text-sm text-slate-600">No institutions match this filter.</li>
        @endforelse
    </ul>
    <div class="mt-4">{{ $institutions->links() }}</div>
@endsection
