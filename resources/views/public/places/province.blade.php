@extends('layouts.public')

@section('title', $province->name.' — '.config('app.name'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12">
        <p class="text-sm font-semibold text-brand-700"><a href="{{ route('provinces.index') }}">Provinces</a></p>
        <h1 class="mt-2 text-3xl font-bold">{{ $province->name }}</h1>
        <p class="mt-2 text-sm text-slate-600">Code {{ $province->code }}</p>
        <p class="mt-3 text-sm font-semibold text-slate-700">{{ number_format($province->universities_count) }} universities · {{ number_format($schoolCount) }} schools · {{ number_format($collegeCount) }} colleges</p>
        <h2 class="mt-8 text-xl font-bold">Districts</h2>
        <ul class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($districts as $district)
                <li>
                    <a href="{{ route('districts.show', $district) }}" class="block rounded-lg border border-slate-200 bg-white px-4 py-3 hover:border-brand-600">
                        <span class="font-semibold">{{ $district->name }}</span>
                        <span class="mt-1 block text-sm text-slate-600">{{ number_format($district->municipalities_count) }} local governments</span>
                        <span class="mt-1 block text-sm text-slate-700">{{ number_format($district->universities_count) }} universities · {{ number_format($district->schools_count) }} schools · {{ number_format($district->colleges_count) }} colleges</span>
                    </a>
                </li>
            @endforeach
        </ul>
        @include('public.places.partials.institutions', [
            'placeName' => $province->name,
            'schoolsUrl' => route('schools.index', ['province' => $province->id]),
        ])
    </section>
@endsection
