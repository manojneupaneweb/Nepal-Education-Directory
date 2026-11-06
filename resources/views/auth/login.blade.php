@extends('layouts.public')

@section('title', 'Sign In — '.config('app.name'))
@section('meta_description', 'Sign in to access the Shiksha Link administration dashboard.')

@section('content')
<div class="flex min-h-[calc(100vh-16rem)] items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">

        {{-- Card Container --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            
            {{-- Header / Logo --}}
            <div class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-2xl text-brand-700 shadow-sm">
                    📚
                </div>
                <h1 class="mt-4 text-2xl font-black tracking-tight text-slate-900">
                    Sign in to your account
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Enter your credentials to access the {{ config('app.name') }} admin panel
                </p>
            </div>

            {{-- Validation Errors Alert --}}
            @if ($errors->any())
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <div class="flex items-start gap-2.5">
                        <svg class="h-5 w-5 shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <p class="font-medium">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('login.store') }}" method="POST" class="mt-6 space-y-5">
                @csrf

                {{-- Email Address --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">
                        Email address
                    </label>
                    <div class="mt-1.5">
                        <input id="email"
                               name="email"
                               type="email"
                               autocomplete="email"
                               required
                               value="{{ old('email') }}"
                               placeholder="admin@shikshalink.com"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-brand-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100 @error('email') border-red-300 bg-red-50/50 @enderror">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-semibold text-slate-700">
                            Password
                        </label>
                    </div>
                    <div class="mt-1.5">
                        <input id="password"
                               name="password"
                               type="password"
                               autocomplete="current-password"
                               required
                               placeholder="••••••••"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-brand-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100 @error('password') border-red-300 bg-red-50/50 @enderror">
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox"
                               name="remember"
                               class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-600">
                        <span class="text-sm text-slate-600">Remember me</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <div>
                    <button type="submit"
                            class="w-full rounded-xl bg-brand-700 px-4 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-800 hover:shadow-md active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2">
                        Sign In
                    </button>
                </div>
            </form>

            {{-- Footer Note --}}
            <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                <p class="text-xs text-slate-500">
                    Protected administrative area for verified staff and reviewers.
                </p>
                <div class="mt-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-900 hover:underline">
                        ← Back to Homepage
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
