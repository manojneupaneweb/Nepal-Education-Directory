@extends('layouts.public')

@section('title', 'Sign In — '.config('app.name'))
@section('meta_description', 'Sign in to access the Shiksha Link administration dashboard.')

@section('content')

<div class="flex min-h-[calc(100vh-10rem)] items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
    <div class="w-full max-w-sm">

        {{-- Brand mark --}}
        <div class="mb-8 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-blue-900 text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 20V9l8-4 8 4v11M8 20v-6h8v6"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-slate-900">{{ config('app.name') }}</span>
            </a>
            <p class="mt-2 text-sm text-slate-500">Administration Access</p>
        </div>

        {{-- Card --}}
        <div class="card p-8">
            <h1 class="text-xl font-bold text-slate-900">Sign in to your account</h1>
            <p class="mt-1 text-sm text-slate-500">Enter your credentials to access the admin panel.</p>

            {{-- Error alert --}}
            @if ($errors->any())
                <div class="mt-5 flex items-start gap-3 rounded border border-red-200 bg-red-50 p-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('login.store') }}" method="POST" class="mt-6 space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                    <input id="email"
                           name="email"
                           type="email"
                           autocomplete="email"
                           required
                           value="{{ old('email') }}"
                           placeholder="admin@example.com"
                           class="form-input mt-1.5 @error('email') border-red-400 @enderror">
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input id="password"
                           name="password"
                           type="password"
                           autocomplete="current-password"
                           required
                           placeholder="••••••••"
                           class="form-input mt-1.5 @error('password') border-red-400 @enderror">
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                           class="h-4 w-4 rounded border-slate-300 text-blue-900 focus:ring-blue-900">
                    <label for="remember" class="text-sm text-slate-600">Remember me</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary w-full justify-center">
                    Sign In
                </button>
            </form>

            {{-- Footer --}}
            <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                <p class="text-xs text-slate-400">Protected administrative area for verified staff and reviewers only.</p>
                <a href="{{ route('home') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-blue-900 hover:underline">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Back to Homepage
                </a>
            </div>
        </div>

        <p class="mt-5 text-center text-xs text-slate-400">
            {{ config('app.name') }} is a free, non-commercial platform. No subscription required.
        </p>
    </div>
</div>

@endsection
