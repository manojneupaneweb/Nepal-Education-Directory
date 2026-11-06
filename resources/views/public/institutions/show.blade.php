@extends('layouts.public')

@section('title', $institution->name.' — '.config('app.name'))
@section('meta_description', $institution->description ?: $institution->name.' is listed in the '.config('app.name').' education directory.')

@section('content')
    <article class="mx-auto max-w-6xl px-4 py-12">
        <p class="text-sm font-semibold text-brand-700">{{ $institution->type?->name }}</p>
        <h1 class="mt-2 text-4xl font-bold">{{ $institution->name }}</h1>
        @if ($institution->name_ne)<p class="mt-2 text-lg text-slate-700">{{ $institution->name_ne }}</p>@endif
        <p class="mt-4 text-slate-600">
            {{ $institution->municipality?->name }}, {{ $institution->district?->name }}, {{ $institution->province?->name }}
            @if ($institution->ward) · Ward {{ $institution->ward->ward_number }} @endif
        </p>
        @if ($institution->description)
            <div class="prose mt-8 max-w-none text-slate-800">
                <p>{{ $institution->description }}</p>
            </div>
        @endif
        <dl class="mt-8 grid gap-4 rounded-lg border border-slate-200 bg-white p-5 sm:grid-cols-2">
            <div><dt class="text-sm text-slate-500">Verification</dt><dd class="font-semibold">{{ str_replace('_', ' ', $institution->verification_status->value) }}</dd></div>
            <div><dt class="text-sm text-slate-500">Ownership</dt><dd>{{ $institution->ownership_type?->value ? str_replace('_', ' ', $institution->ownership_type->value) : 'Not recorded' }}</dd></div>
            <div><dt class="text-sm text-slate-500">Established</dt><dd>{{ $institution->established_year ?: 'Not recorded' }}</dd></div>
            <div><dt class="text-sm text-slate-500">Website</dt><dd>@if ($institution->official_website)<a class="font-semibold text-brand-700" href="{{ $institution->official_website }}">Official website</a>@else Not recorded @endif</dd></div>
        </dl>
        @if ($institution->categories->isNotEmpty())
            <h2 class="mt-8 text-lg font-bold">Categories</h2>
            <ul class="mt-2 flex flex-wrap gap-2">
                @foreach ($institution->categories as $category)
                    <li class="rounded-full bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-800">{{ $category->name }}</li>
                @endforeach
            </ul>
        @endif
    </article>
@endsection
