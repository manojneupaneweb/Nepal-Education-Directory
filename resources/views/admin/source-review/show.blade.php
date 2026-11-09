@extends('layouts.admin')

@section('title', $institution->name)
@section('heading', 'Source review')

@section('content')
    <p class="text-sm"><a class="font-semibold text-brand-800" href="{{ route('admin.source-review.index') }}">Back to the queue</a></p>
    <h1 class="mt-2 text-2xl font-bold">{{ $institution->name }}</h1>
    @if (session('status'))
        <p class="mt-4 rounded-md bg-blue-50 px-3 py-2 text-sm text-blue-900">{{ session('status') }}</p>
    @endif
    @if ($errors->any())
        <ul class="mt-4 list-disc pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <dl class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-white p-5 sm:grid-cols-2">
        <div><dt class="text-sm text-slate-500">Type</dt><dd>{{ $institution->type?->name }}</dd></div>
        <div><dt class="text-sm text-slate-500">Verification</dt><dd>{{ $institution->verification_status->value }}</dd></div>
        <div><dt class="text-sm text-slate-500">Place</dt><dd>{{ $institution->municipality?->name }}, {{ $institution->district?->name }}</dd></div>
        <div><dt class="text-sm text-slate-500">Address</dt><dd>{{ $institution->address ?: '—' }}</dd></div>
        <div class="sm:col-span-2"><dt class="text-sm text-slate-500">Description</dt><dd>{{ $institution->description ?: '—' }}</dd></div>
        <div class="sm:col-span-2">
            <dt class="text-sm text-slate-500">Official website</dt>
            <dd>
                @if ($institution->official_website)
                    <a href="{{ $institution->official_website }}" target="_blank" rel="noopener noreferrer">{{ $institution->official_website }}</a>
                @else
                    —
                @endif
            </dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-sm text-slate-500">Public contacts</dt>
            <dd>
                @forelse ($institution->contacts as $contact)
                    <p>{{ $contact->label }}: {{ $contact->contact_value }}</p>
                @empty
                    —
                @endforelse
            </dd>
        </div>
    </dl>

    <form method="post" action="{{ route('admin.source-review.store', $institution) }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-5">
        @csrf
        <h2 class="font-semibold">Record a proposal</h2>
        <p class="text-sm text-slate-600">Paste the exact page URL that shows this value. Saving a proposal does not update the institution.</p>
        <label class="text-sm font-semibold">Field
            <select name="field" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 font-normal">
                @foreach ($fields as $field)
                    <option value="{{ $field }}" @selected(old('field') === $field)>{{ str_replace('_', ' ', $field) }}</option>
                @endforeach
            </select>
        </label>
        <label class="text-sm font-semibold">Value
            <textarea name="value" rows="4" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 font-normal">{{ old('value') }}</textarea>
        </label>
        <label class="text-sm font-semibold">Source page URL
            <input name="source_url" value="{{ old('source_url') }}" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 font-normal">
        </label>
        <button class="w-fit rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white">Save proposal</button>
    </form>

    <h2 class="mt-8 font-semibold">Proposals</h2>
    <ul class="mt-3 space-y-3">
        @forelse ($institution->fieldEvidence as $proposal)
            <li class="rounded-lg border border-slate-200 bg-white p-4 text-sm">
                <p class="font-semibold">{{ str_replace('_', ' ', $proposal->field_name) }} · {{ $proposal->review_status->value }}</p>
                <p class="mt-1 whitespace-pre-wrap">{{ $proposal->normalized_value }}</p>
                @if ($proposal->institutionSource)
                    <p class="mt-1"><a href="{{ $proposal->institutionSource->source_url }}" target="_blank" rel="noopener noreferrer">{{ $proposal->institutionSource->source_url }}</a></p>
                    <p class="text-slate-500">Checked {{ $proposal->institutionSource->last_checked_at?->toDateTimeString() }}</p>
                @endif
                @if ($proposal->review_status->value === 'pending')
                    <div class="mt-3 flex gap-2">
                        <form method="post" action="{{ route('admin.source-review.accept', $proposal) }}">@csrf<button class="rounded-md bg-brand-700 px-3 py-1.5 text-sm font-semibold text-white">Accept</button></form>
                        <form method="post" action="{{ route('admin.source-review.reject', $proposal) }}">@csrf<button class="rounded-md border border-slate-300 px-3 py-1.5 text-sm font-semibold">Reject</button></form>
                    </div>
                @endif
            </li>
        @empty
            <li class="text-sm text-slate-600">No proposals yet.</li>
        @endforelse
    </ul>
@endsection
