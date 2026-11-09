@extends('layouts.admin')

@section('title', 'Messages')
@section('heading', 'Messages')

@section('content')
    <h1 class="text-2xl font-bold text-slate-950">Contact messages</h1>
    <p class="mt-2 text-sm text-slate-600">Messages sent from the public contact page.</p>

    <div class="mt-6 space-y-4">
        @forelse ($messages as $message)
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-slate-900">{{ $message->name }}</h2>
                        <p class="text-sm text-slate-500">{{ $message->email }} · {{ $message->subject }}</p>
                        @if ($message->institution_name)
                            <p class="mt-1 text-sm text-slate-600">Institution: {{ $message->institution_name }}</p>
                        @endif
                    </div>
                    <time class="text-xs text-slate-400">{{ $message->created_at->format('d M Y, H:i') }}</time>
                </div>
                <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $message->message }}</p>
            </article>
        @empty
            <p class="rounded-2xl border border-slate-200 bg-white px-5 py-10 text-center text-slate-500">No messages yet.</p>
        @endforelse
    </div>
    <div class="mt-5">{{ $messages->links() }}</div>
@endsection
