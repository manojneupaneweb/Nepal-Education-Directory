@extends('layouts.public')

@section('title', $district->name.' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12">
        <p class="text-sm font-semibold text-brand-700">
            <a href="{{ route('provinces.show', $district->province) }}">{{ $district->province->name }}</a>
        </p>
        <h1 class="mt-2 text-3xl font-bold">{{ $district->name }}</h1>
        <p class="mt-2 text-sm text-slate-600">Code {{ $district->code }}</p>
        <h2 class="mt-8 text-xl font-bold">Local governments</h2>
        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
            @foreach ($municipalities as $municipality)
                <li>
                    <a href="{{ route('municipalities.show', [$district, $municipality]) }}" class="block rounded-lg border border-slate-200 bg-white px-4 py-3 hover:border-brand-600">
                        <span class="font-semibold">{{ $municipality->name }}</span>
                        @if ($municipality->name_ne)<span class="mt-1 block text-sm text-slate-600">{{ $municipality->name_ne }}</span>@endif
                        <span class="mt-1 block text-sm text-slate-500">{{ str_replace('_', ' ', $municipality->municipality_type->value) }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
@endsection
