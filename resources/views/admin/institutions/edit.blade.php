@extends('layouts.admin')

@section('title', 'Edit '.$institution->name)
@section('heading', 'Edit institution')

@section('content')
    <h1 class="text-2xl font-bold">Edit {{ $institution->name }}</h1>
    <p class="mt-2 text-sm text-slate-600">Saving keeps the current publication and verification statuses. Leave the slug unchanged unless you intend to change the public URL.</p>
    <form method="post" action="{{ route('admin.institutions.update', $institution) }}" class="mt-6 max-w-4xl rounded-lg border border-slate-200 bg-white p-5">
        @include('admin.institutions._form')
    </form>
@endsection
