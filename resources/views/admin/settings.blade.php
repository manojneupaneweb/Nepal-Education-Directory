@extends('layouts.admin')

@section('title', 'Settings')
@section('heading', 'Settings')

@section('content')
    <h1 class="text-2xl font-bold text-slate-950">Settings</h1>

    @if (session('status'))
        <p class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</p>
    @endif

    <div class="mt-6 max-w-xl rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-semibold text-slate-900">Password</h2>
        <p class="mt-2 text-sm text-slate-600">
            Last password change:
            <span class="font-semibold text-slate-900">
                {{ $user->password_changed_at?->format('d M Y, H:i') ?? 'Not changed yet' }}
            </span>
        </p>

        <form action="{{ route('admin.settings.password') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="current_password" class="block text-sm font-semibold text-slate-700">Current password</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="form-input mt-2">
                @error('current_password')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700">New password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="form-input mt-2">
                @error('password')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="form-input mt-2">
            </div>
            <button type="submit" class="btn-primary">Change password</button>
        </form>
    </div>
@endsection
