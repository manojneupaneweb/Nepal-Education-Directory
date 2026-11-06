@extends('layouts.admin')

@section('title', 'Institutions')
@section('heading', 'Institutions')

@section('content')
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-bold">Institutions</h1>
        <a href="{{ route('admin.institutions.create') }}" class="rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white">New institution</a>
    </div>
    @if (session('status'))
        <p class="mt-4 rounded-md bg-blue-50 px-3 py-2 text-sm text-blue-900">{{ session('status') }}</p>
    @endif
    <div class="mt-6 overflow-x-auto rounded-lg border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-slate-500">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Publication</th>
                    <th class="px-4 py-3">Verification</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($institutions as $institution)
                    <tr class="border-b border-slate-100">
                        <td class="px-4 py-3"><a class="font-semibold text-brand-800" href="{{ route('admin.institutions.show', $institution) }}">{{ $institution->name }}</a></td>
                        <td class="px-4 py-3">{{ $institution->type?->name }}</td>
                        <td class="px-4 py-3">{{ $institution->status->value }}</td>
                        <td class="px-4 py-3">{{ $institution->verification_status->value }}</td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-slate-600" colspan="4">No institutions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $institutions->links() }}</div>
@endsection
