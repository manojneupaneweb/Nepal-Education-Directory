@extends('layouts.public')

@section('title', $type->name.' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12">
        <p class="text-sm font-semibold text-brand-700"><a href="{{ route('types.index') }}">Institution types</a></p>
        <h1 class="mt-2 text-3xl font-bold">{{ $type->name }}</h1>
        @if ($type->name_ne)<p class="mt-2 text-slate-600">{{ $type->name_ne }}</p>@endif
        @if ($institutions->isEmpty())
            <p class="mt-8 rounded-lg border border-dashed border-slate-300 bg-white px-4 py-8 text-slate-600">No published {{ strtolower($type->name) }} records yet.</p>
        @else
            <ul class="mt-8 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
                @foreach ($institutions as $institution)
                    <li class="px-4 py-4">
                        <a class="font-semibold text-brand-800" href="{{ route('institutions.show', $institution) }}">{{ $institution->name }}</a>
                        <p class="mt-1 text-sm text-slate-600">{{ $institution->municipality?->name }}, {{ $institution->district?->name }}</p>
                    </li>
                @endforeach
            </ul>
            <div class="mt-6">{{ $institutions->links() }}</div>
        @endif
    </section>
@endsection
