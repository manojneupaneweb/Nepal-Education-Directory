@extends('layouts.public')

@section('title', 'Provinces — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12">
        <h1 class="text-3xl font-bold">Provinces</h1>
        <p class="mt-3 max-w-2xl text-slate-600">These are the province records imported from the National Statistics Office code file. Open a province to see its districts and local governments.</p>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($provinces as $province)
                <a href="{{ route('provinces.show', $province) }}" class="rounded-lg border border-slate-200 bg-white p-5 hover:border-brand-600">
                    <p class="text-sm font-semibold text-brand-700">Code {{ $province->code }}</p>
                    <h2 class="mt-1 text-xl font-bold">{{ $province->name }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ number_format($province->districts_count) }} districts</p>
                    <p class="mt-2 text-sm text-slate-700">{{ number_format($province->universities_count) }} universities</p>
                    <p class="text-sm text-slate-700">{{ number_format($province->schools_count) }} schools</p>
                    <p class="text-sm text-slate-700">{{ number_format($province->colleges_count) }} colleges</p>
                </a>
            @empty
                <p class="text-slate-600">No provinces are available. Import the geographic dataset first.</p>
            @endforelse
        </div>
    </section>
@endsection
