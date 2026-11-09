@extends('layouts.public')

@section('title', $district->name.' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12">
        <p class="text-sm font-semibold text-brand-700">
            <a href="{{ route('provinces.show', $district->province) }}">{{ $district->province->name }}</a>
        </p>
        <h1 class="mt-2 text-3xl font-bold">{{ $district->name }}</h1>
        <p class="mt-2 text-sm text-slate-600">Code {{ $district->code }}</p>
        @php $leader = $municipalities->sortByDesc('schools_count')->first(); @endphp
        <p class="mt-3 max-w-3xl text-sm leading-relaxed text-slate-700">{{ $district->name }} is in {{ $district->province->name }}. This directory lists {{ number_format($schoolCount) }} published schools and {{ number_format($collegeCount) }} published colleges here.@if ($leader && $leader->schools_count > 0) {{ $leader->name }} has the most listed schools in the district ({{ number_format($leader->schools_count) }}).@endif</p>
        <h2 class="mt-8 text-xl font-bold">Local governments</h2>
        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
            @foreach ($municipalities as $municipality)
                <li>
                    <a href="{{ route('municipalities.show', [$district, $municipality]) }}" class="block rounded-lg border border-slate-200 bg-white px-4 py-3 hover:border-brand-600">
                        <span class="font-semibold">{{ $municipality->name }}</span>
                        @if ($municipality->name_ne)<span class="mt-1 block text-sm text-slate-600">{{ $municipality->name_ne }}</span>@endif
                        <span class="mt-1 block text-sm text-slate-500">{{ str_replace('_', ' ', $municipality->municipality_type->value) }}</span>
                        <span class="mt-1 block text-sm text-slate-700">{{ number_format($municipality->schools_count) }} schools · {{ number_format($municipality->colleges_count) }} colleges</span>
                    </a>
                </li>
            @endforeach
        </ul>
        @include('public.places.partials.institutions', [
            'placeName' => $district->name,
            'schoolsUrl' => route('schools.index', ['province' => $district->province_id, 'district' => $district->id]),
        ])
    </section>
@endsection
