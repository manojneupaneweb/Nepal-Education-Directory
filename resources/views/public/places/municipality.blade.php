@extends('layouts.public')

@section('title', $municipality->name.' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12">
        <p class="text-sm font-semibold text-brand-700">
            <a href="{{ route('districts.show', $district) }}">{{ $district->name }}</a>
            · {{ $district->province->name }}
        </p>
        <h1 class="mt-2 text-3xl font-bold">{{ $municipality->name }}</h1>
        @if ($municipality->name_ne)<p class="mt-2 text-slate-600">{{ $municipality->name_ne }}</p>@endif
        <p class="mt-2 text-sm text-slate-500">{{ str_replace('_', ' ', $municipality->municipality_type->value) }} · code {{ $municipality->code }}</p>
        <h2 class="mt-8 text-xl font-bold">Published institutions</h2>
        @if ($institutions->isEmpty())
            <p class="mt-4 rounded-lg border border-dashed border-slate-300 bg-white px-4 py-8 text-slate-600">No published institutions are linked to this local government yet.</p>
        @else
            <ul class="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
                @foreach ($institutions as $institution)
                    <li class="px-4 py-4">
                        <a class="font-semibold text-brand-800" href="{{ route('institutions.show', $institution) }}">{{ $institution->name }}</a>
                        <p class="mt-1 text-sm text-slate-600">{{ $institution->type?->name }}</p>
                    </li>
                @endforeach
            </ul>
            <div class="mt-6">{{ $institutions->links() }}</div>
        @endif
    </section>
@endsection
