<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name').' — Nepal Education Directory')</title>
    <meta name="description" content="@yield('meta_description', 'Search schools, colleges, universities, and training centers across Nepal.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta name="theme-color" content="#123B70">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

<a href="#page-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-blue-900 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white focus:shadow-lg">Skip to content</a>

{{-- ── TOP UTILITY BAR ────────────────────────────────────────────────────── --}}
<div class="border-b border-white/10 bg-navy text-blue-100">
    <div class="section-container flex h-9 items-center justify-between gap-4 text-xs">
        <a href="mailto:info@shikshyasanjal.com" class="transition hover:text-white hover:underline">info@shikshyasanjal.com</a>
        <a href="{{ route('suggestions.create') }}" class="text-slate-300 transition hover:text-white hover:underline">Suggest an institution →</a>
    </div>
</div>

{{-- ── NAVBAR ─────────────────────────────────────────────────────────────── --}}
<header class="sticky top-0 z-40 border-b border-slate-200 bg-white">
    <div class="section-container flex h-20 items-center justify-between gap-4">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex shrink-0 items-center" aria-label="{{ config('app.name') }} home">
            <img src="{{ asset('images/logo.jpg') }}" alt="{{ config('app.name') }}" class="h-12 w-auto sm:h-14 xl:h-16">
        </a>

        {{-- Desktop nav --}}
        @php
            $navItems = [
                ['route' => 'home',               'pattern' => 'home',          'label' => 'Home'],
                ['route' => 'universities.index',  'pattern' => 'universities.*','label' => 'Universities'],
                ['route' => 'colleges.index',      'pattern' => 'colleges.*',    'label' => 'Colleges'],
                ['route' => 'schools.index',       'pattern' => 'schools.*',     'label' => 'Schools'],
                ['route' => 'blog',                'pattern' => 'blog*',         'label' => 'Blog'],
                ['route' => 'about',               'pattern' => 'about',         'label' => 'About'],
                ['route' => 'contact',             'pattern' => 'contact',       'label' => 'Contact us'],
            ];
        @endphp
        <nav aria-label="Primary" class="hidden min-w-0 items-center xl:flex">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="nav-link {{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Right: search + hamburger --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('search') }}"
               class="flex h-9 w-9 items-center justify-center rounded border border-slate-200 text-slate-500 transition hover:border-blue-900 hover:text-blue-900"
               aria-label="Search institutions">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m20 20-4.2-4.2M16 10.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>
            </a>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn-primary hidden text-sm xl:inline-flex">Admin</a>
            @else
                <a href="{{ route('login') }}" class="btn-outline hidden text-sm xl:inline-flex">Sign in</a>
            @endauth
            <button id="nav-menu-open" type="button"
                class="flex h-9 w-9 items-center justify-center rounded border border-slate-200 text-slate-600 transition hover:border-blue-900 hover:text-blue-900 xl:hidden"
                aria-label="Open menu" aria-expanded="false" aria-controls="mobile-nav">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
        </div>
    </div>
</header>

{{-- ── MOBILE DRAWER ───────────────────────────────────────────────────────── --}}
<div id="mobile-nav-overlay"
     class="pointer-events-none fixed inset-0 z-40 bg-black/40 opacity-0 transition-opacity duration-200"
     aria-hidden="true"></div>
<div id="mobile-nav" role="dialog" aria-modal="true" aria-label="Navigation" aria-hidden="true"
     class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full overflow-y-auto bg-white shadow-lg transition-transform duration-200">
    <div class="flex h-16 items-center justify-between border-b border-slate-200 px-4">
        <a href="{{ route('home') }}" class="flex items-center" aria-label="{{ config('app.name') }} home">
            <img src="{{ asset('images/logo.jpg') }}" alt="" class="h-12 w-auto">
        </a>
        <button id="nav-menu-close" type="button"
            class="flex h-9 w-9 items-center justify-center rounded text-slate-400 hover:text-slate-900"
            aria-label="Close menu">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <nav class="p-4" aria-label="Mobile navigation">
        <ul class="space-y-1">
            @foreach ($navItems ?? [] as $item)
                @php $isActive = request()->routeIs($item['pattern']); @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center rounded px-3 py-2.5 text-sm font-bold transition {{ $isActive ? 'bg-blue-50 text-blue-900' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mt-4 border-t border-slate-200 pt-4">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn-primary w-full justify-center text-sm">Admin</a>
            @else
                <a href="{{ route('login') }}" class="btn-outline w-full justify-center text-sm">Sign in</a>
            @endauth
        </div>
    </nav>
</div>

{{-- ── MAIN CONTENT ──────────────────────────────────────────────────────── --}}
<main id="page-content">
    @yield('content')
</main>

{{-- ── FOOTER ───────────────────────────────────────────────────────────────── --}}
<footer class="mt-auto bg-navy text-blue-100">
    <div class="h-1 bg-accent"></div>
    <div class="section-container py-12">
        <div class="grid gap-10 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
            <div>
                <a href="{{ route('home') }}" class="inline-flex rounded-xl bg-white px-3 py-2">
                    <img src="{{ asset('images/logo.jpg') }}" alt="ShikshyaSanjal" class="h-12 w-auto">
                </a>
                <p class="mt-4 text-sm font-semibold tracking-wide text-white">Discover • Compare • Choose</p>
                <p class="mt-2 max-w-sm text-sm leading-relaxed text-blue-100/80">
                    A free education directory for students, parents, and teachers across Nepal.
                </p>
                <a href="mailto:info@shikshyasanjal.com" class="mt-4 inline-flex text-sm font-medium text-white underline-offset-2 hover:underline">info@shikshyasanjal.com</a>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-white">Browse</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('schools.index') }}" class="transition hover:text-white">Schools</a></li>
                    <li><a href="{{ route('colleges.index') }}" class="transition hover:text-white">Colleges</a></li>
                    <li><a href="{{ route('universities.index') }}" class="transition hover:text-white">Universities</a></li>
                    <li><a href="{{ route('provinces.index') }}" class="transition hover:text-white">Provinces</a></li>
                    <li><a href="{{ route('types.index') }}" class="transition hover:text-white">Institution types</a></li>
                </ul>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-white">About</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('about') }}" class="transition hover:text-white">About the directory</a></li>
                    <li><a href="{{ route('how-it-works') }}" class="transition hover:text-white">How it works</a></li>
                    <li><a href="{{ route('blog') }}" class="transition hover:text-white">Blog</a></li>
                    <li><a href="{{ route('search') }}" class="transition hover:text-white">Search</a></li>
                </ul>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-white">Contact</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('contact') }}" class="transition hover:text-white">Contact us</a></li>
                    <li><a href="{{ route('suggestions.create') }}" class="transition hover:text-white">Suggest an institution</a></li>
                    <li><a href="{{ route('contact') }}" class="transition hover:text-white">Correct an error</a></li>
                </ul>
                <p class="mt-5 text-xs leading-relaxed text-blue-200/70">
                    Data from the National Statistics Office and the Ministry of Education, Science and Technology.
                </p>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="section-container flex flex-col gap-2 py-4 text-xs text-blue-200/70 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} ShikshyaSanjal. Not affiliated with any government body.</p>
            <p>Discover • Compare • Choose</p>
        </div>
    </div>
</footer>

{{-- Back to top --}}
<button id="back-to-top" type="button" aria-label="Back to top"
    style="opacity:0;pointer-events:none;transition:opacity 200ms ease-out"
    class="fixed bottom-5 right-5 z-30 flex h-10 w-10 items-center justify-center rounded border border-slate-300 bg-white text-slate-600 shadow-sm transition hover:border-blue-900 hover:text-blue-900">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18"/></svg>
</button>

@stack('scripts')
</body>
</html>
