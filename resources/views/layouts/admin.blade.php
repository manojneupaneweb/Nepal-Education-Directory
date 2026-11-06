<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col md:flex-row">
        <aside class="border-b border-slate-200 bg-brand-900 text-white md:w-64 md:border-b-0 md:border-r">
            <div class="px-4 py-5">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold">{{ config('app.name') }}</a>
                <p class="mt-1 text-sm text-brand-100">Administration</p>
            </div>
            <nav aria-label="Admin" class="flex gap-2 px-3 pb-4 md:block md:space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 text-sm font-semibold hover:bg-brand-800">Dashboard</a>
                <a href="{{ route('admin.institutions.index') }}" class="block rounded-md px-3 py-2 text-sm font-semibold hover:bg-brand-800">Institutions</a>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3">
                <p class="text-sm font-semibold text-slate-700">@yield('heading', 'Dashboard')</p>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-slate-600">{{ auth()->user()->name }}</span>
                    <a href="{{ route('home') }}" class="font-semibold text-brand-700 hover:text-brand-800">View site</a>
                </div>
            </header>
            <main class="flex-1 px-4 py-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
