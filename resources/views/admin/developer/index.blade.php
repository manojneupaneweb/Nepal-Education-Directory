@extends('layouts.admin')

@section('title', 'Developer')
@section('heading', 'Developer')

@section('content')
    <h1 class="text-2xl font-bold text-slate-950">Developer</h1>
    <p class="mt-2 text-sm text-slate-600">Server errors recorded while the project is running.</p>

    @if (session('status'))
        <p class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</p>
    @endif

    <div class="mt-6 space-y-4">
        @forelse ($errors as $error)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 {{ $error->resolved_at ? 'opacity-60' : '' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-red-700">{{ class_basename($error->exception) }}</p>
                        <h2 class="mt-1 font-semibold text-slate-900">{{ $error->message }}</h2>
                        <p class="mt-2 break-all text-xs text-slate-500">{{ $error->file }}:{{ $error->line }}</p>
                        @if ($error->url)
                            <p class="mt-1 break-all text-xs text-slate-500">{{ $error->url }}</p>
                        @endif
                        <p class="mt-2 text-xs text-slate-500">
                            Seen {{ $error->occurrences }} {{ $error->occurrences === 1 ? 'time' : 'times' }}
                            · {{ $error->created_at->format('d M Y, H:i') }}
                            @if ($error->resolved_at)
                                · Resolved {{ $error->resolved_at->format('d M Y, H:i') }}
                            @endif
                        </p>
                    </div>
                    @unless ($error->resolved_at)
                        <form method="POST" action="{{ route('admin.developer.resolve', $error) }}">
                            @csrf
                            <button type="submit" class="btn-primary">Resolved</button>
                        </form>
                    @endunless
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-slate-200 bg-white px-5 py-10 text-center text-slate-500">No project errors have been recorded.</p>
        @endforelse
    </div>
    <div class="mt-5">{{ $errors->links() }}</div>
@endsection
