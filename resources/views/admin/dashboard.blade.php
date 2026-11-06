@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold text-slate-950">Administration</h1>
    <p class="mt-3 max-w-2xl text-slate-700">
        This dashboard is a protected placeholder. Institution editing, verification review, and configuration tools will be added in later milestones.
    </p>
    <dl class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-white p-5">
            <dt class="text-sm text-slate-500">Institutions</dt>
            <dd class="mt-1 text-2xl font-bold text-slate-950">—</dd>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5">
            <dt class="text-sm text-slate-500">Pending review</dt>
            <dd class="mt-1 text-2xl font-bold text-slate-950">—</dd>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5">
            <dt class="text-sm text-slate-500">Signed in as</dt>
            <dd class="mt-1 truncate text-base font-semibold text-slate-950">{{ auth()->user()->email }}</dd>
        </div>
    </dl>
@endsection
