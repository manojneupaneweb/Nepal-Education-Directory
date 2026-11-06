<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name').' — Nepal Education Directory')</title>
    <meta name="description" content="@yield('meta_description', 'Search schools, colleges, universities, and training centers across Nepal.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    @stack('meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<a href="#page-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-white focus:px-3 focus:py-2 focus:shadow-lg">
    Skip to content
</a>

{{-- ─── NAVBAR ──────────────────────────────────────────────────── --}}
<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-extrabold tracking-tight text-brand-800">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-700 text-lg text-white shadow-sm">📚</span>
            <span>{{ config('app.name') }}</span>
        </a>

        {{-- Desktop nav --}}
        <nav aria-label="Primary" class="hidden items-center gap-1 text-sm font-semibold md:flex">
            <a href="{{ route('home') }}"
                class="rounded-md px-3 py-2 transition {{ request()->routeIs('home') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Home
            </a>
            <a href="{{ route('schools.index') }}"
                class="rounded-md px-3 py-2 transition {{ request()->routeIs('schools.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Schools
            </a>
            <a href="{{ route('universities.index') }}"
                class="rounded-md px-3 py-2 transition {{ request()->routeIs('universities.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Universities
            </a>
            <a href="{{ route('university-programs.index') }}"
                class="rounded-md px-3 py-2 transition {{ request()->routeIs('university-programs.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Programs
            </a>
            <a href="{{ route('blog') }}"
                class="rounded-md px-3 py-2 transition {{ request()->routeIs('blog') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Blog
            </a>
            <a href="{{ route('about') }}"
                class="rounded-md px-3 py-2 transition {{ request()->routeIs('about') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                About
            </a>
            <a href="{{ route('contact') }}"
                class="rounded-md px-3 py-2 transition {{ request()->routeIs('contact') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                Contact
            </a>
            @auth
                <a href="{{ route('admin.dashboard') }}"
                    class="ml-2 rounded-lg bg-brand-700 px-4 py-2 text-white shadow-sm transition hover:bg-brand-800">
                    Admin
                </a>
            @endauth
        </nav>

        {{-- Mobile hamburger --}}
        <details class="relative md:hidden" id="mobile-menu">
            <summary class="flex cursor-pointer list-none items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
                Menu
            </summary>
            <nav aria-label="Mobile"
                class="absolute right-0 mt-2 w-64 rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                <a class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('home') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}"
                    href="{{ route('home') }}">
                    🏠 Home
                </a>
                <a class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('schools.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}"
                    href="{{ route('schools.index') }}">
                    🏫 Schools
                </a>
                <a class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('universities.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}"
                    href="{{ route('universities.index') }}">
                    🎓 Universities
                </a>
                <a class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('university-programs.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}"
                    href="{{ route('university-programs.index') }}">
                    📘 Programs
                </a>
                <a class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('blog') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}"
                    href="{{ route('blog') }}">
                    ✍️ Blog
                </a>
                <a class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('about') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}"
                    href="{{ route('about') }}">
                    🏢 About
                </a>
                <a class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('contact') ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}"
                    href="{{ route('contact') }}">
                    ✉️ Contact
                </a>
                @auth
                    <div class="mt-2 border-t border-slate-100 pt-2">
                        <a class="flex items-center gap-2 rounded-lg bg-brand-700 px-3 py-2.5 text-sm font-semibold text-white"
                            href="{{ route('admin.dashboard') }}">
                            ⚙️ Admin Dashboard
                        </a>
                    </div>
                @endauth
            </nav>
        </details>

    </div>
</header>

{{-- ─── MAIN CONTENT ────────────────────────────────────────────── --}}
<main id="page-content">
    @yield('content')
</main>

{{-- ─── FOOTER ──────────────────────────────────────────────────── --}}
<footer class="mt-0 border-t border-slate-200 bg-brand-900 text-slate-300">
    <div class="mx-auto max-w-6xl px-4 py-14">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Col 1 — Brand --}}
            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-extrabold text-white">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-700 text-sm text-white">📚</span>
                    {{ config('app.name') }}
                </a>
                <p class="mt-3 text-sm leading-relaxed text-slate-400">
                    Nepal's free and independent education directory. Find schools, colleges, universities, and training centers across all 7 provinces.
                </p>
                <p class="mt-4 text-xs text-slate-500">
                    Not affiliated with any government body or educational institution.
                </p>
            </div>

            {{-- Col 2 — Directory --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Directory</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a class="text-slate-300 transition hover:text-white" href="{{ route('search') }}">🔍 Search Institutions</a></li>
                    <li><a class="text-slate-300 transition hover:text-white" href="{{ route('types.index') }}">🏫 Institution Types</a></li>
                    <li><a class="text-slate-300 transition hover:text-white" href="{{ route('provinces.index') }}">🗺️ Browse by Province</a></li>
                    <li><a class="text-slate-300 transition hover:text-white" href="{{ route('provinces.index') }}">📍 Browse by District</a></li>
                </ul>
            </div>

            {{-- Col 3 — Learn --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Learn More</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a class="text-slate-300 transition hover:text-white" href="{{ route('about') }}">ℹ️ About Shiksha Link</a></li>
                    <li><a class="text-slate-300 transition hover:text-white" href="{{ route('how-it-works') }}">📖 How it Works</a></li>
                    <li><a class="text-slate-300 transition hover:text-white" href="{{ route('contact') }}">✉️ Contact Us</a></li>
                </ul>
            </div>

            {{-- Col 4 — Data & Contact --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Data Sources</p>
                <ul class="mt-4 space-y-2.5 text-sm text-slate-400">
                    <li>📊 National Statistics Office (geography)</li>
                    <li>🎓 Ministry of Education classifications</li>
                    <li>🏛️ MOEST institutional data</li>
                </ul>
                <p class="mt-6 text-xs font-bold uppercase tracking-widest text-slate-400">Get in Touch</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a class="text-slate-300 transition hover:text-white" href="{{ route('contact') }}">Submit a correction</a></li>
                    <li><a class="text-slate-300 transition hover:text-white" href="{{ route('contact') }}">Report missing institution</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-brand-800 bg-brand-900">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-5 sm:flex-row">
            <p class="text-xs text-slate-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}. An independent, non-commercial Nepal education directory.
            </p>
            <p class="text-xs text-slate-500">
                Geographic data: National Statistics Office of Nepal.
            </p>
        </div>
    </div>
</footer>

</body>
</html>
