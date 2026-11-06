@extends('layouts.admin')

@section('title', 'New institution')
@section('heading', 'New institution')

@section('content')
    <h1 class="text-2xl font-bold">New institution</h1>
    <p class="mt-2 text-sm text-slate-600">New records are saved as drafts. Use the province, district, municipality, and ward IDs from the geographic directory.</p>
    <form method="post" action="{{ route('admin.institutions.store') }}" class="mt-6 max-w-4xl rounded-lg border border-slate-200 bg-white p-5">
        @include('admin.institutions._form')
    </form>
@endsection
