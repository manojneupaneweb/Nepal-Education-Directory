@extends('layouts.public')

@section('title', 'Institution types — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12">
        <h1 class="text-3xl font-bold">Institution types</h1>
        <p class="mt-3 max-w-2xl text-slate-600">A type describes the kind of institution. Categories such as government or boarding school are stored separately and are not guessed from the name.</p>
        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            @forelse ($types as $type)
                <a href="{{ route('types.show', $type) }}" class="rounded-lg border border-slate-200 bg-white p-5 hover:border-brand-600">
                    <h2 class="text-lg font-bold">{{ $type->name }}</h2>
                    @if ($type->name_ne)<p class="mt-1 text-sm text-slate-600">{{ $type->name_ne }}</p>@endif
                    <p class="mt-3 text-sm font-semibold text-brand-700">{{ number_format($type->institutions_count) }} published</p>
                </a>
            @empty
                <p class="text-slate-600">No institution types are active.</p>
            @endforelse
        </div>
    </section>
@endsection
