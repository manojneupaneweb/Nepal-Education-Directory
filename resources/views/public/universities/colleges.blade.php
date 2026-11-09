@extends('layouts.public')

@section('title', $university->name.' campuses and colleges — '.config('app.name'))
@section('meta_description', 'Colleges and campuses listed with '.$university->name.' in the '.config('app.name').' directory.')
@section('canonical', route('universities.colleges', $university))

@section('content')
<section class="bg-gradient-to-br from-brand-900 via-brand-800 to-brand-700 py-10 text-white">
    <div class="mx-auto max-w-6xl px-4">
        <nav class="text-sm text-brand-100" aria-label="Breadcrumb">
            <a class="hover:text-white" href="{{ route('home') }}">Home</a>
            <span aria-hidden="true"> / </span>
            <a class="hover:text-white" href="{{ route('universities.index') }}">Universities</a>
            <span aria-hidden="true"> / </span>
            <a class="hover:text-white" href="{{ route('universities.show', $university) }}">{{ $university->name }}</a>
            <span aria-hidden="true"> / </span>
            <span class="text-white">Campuses and colleges</span>
        </nav>
        <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center">
            @if ($university->logo_path)
                <img src="{{ asset($university->logo_path) }}" alt="{{ $university->name }} logo" width="96" height="96" class="h-24 w-24 shrink-0 rounded-2xl border border-white bg-white object-contain p-2 shadow-md">
            @endif
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $university->name }} campuses and colleges</h1>
                <p class="mt-2 max-w-3xl text-brand-100">Colleges named with this university in a public directory. Affiliation is not a confirmation from the university itself.</p>
            </div>
        </div>
        <div class="mt-6">
            <span class="rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-brand-800 shadow-md">{{ number_format($colleges->total()) }} listed</span>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-10">
    @if ($colleges->isEmpty())
        <p class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-8 text-slate-600">No colleges are linked to {{ $university->name }} yet.</p>
    @else
        <ul class="grid gap-3 sm:grid-cols-2">
            @foreach ($colleges as $college)
                <li class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <a class="font-semibold text-brand-800" href="{{ route('institutions.show', $college) }}">{{ $college->name }}</a>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ collect([$college->municipality?->name, $college->district?->name, $college->province?->name])->filter()->implode(', ') }}
                    </p>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ str_replace('_', ' ', $college->pivot->affiliation_type) }} · {{ str_replace('_', ' ', $college->pivot->status) }}</p>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $colleges->links() }}</div>
    @endif
</section>
@endsection
