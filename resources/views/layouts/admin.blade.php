<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased md:h-screen md:overflow-hidden">

<div id="admin-nav-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 md:hidden"></div>

<div id="admin-shell" class="admin-shell min-h-screen md:flex md:h-screen md:overflow-hidden">

    {{-- ─── SIDEBAR ──────────────────────────────────────────────────────── --}}
    <aside id="admin-nav" class="admin-nav fixed inset-y-0 left-0 z-50 flex w-64 flex-col overflow-y-auto border-r border-slate-200 bg-white md:static md:h-screen md:w-60 md:shrink-0">

        {{-- Brand --}}
        <div class="border-b border-slate-100 px-5 py-4">
            <a href="{{ route('admin.dashboard') }}" class="block" aria-label="{{ config('app.name') }} admin">
                <img src="{{ asset('images/logo.jpg') }}" alt="{{ config('app.name') }}" class="h-14 w-auto">
            </a>
            <p class="mt-1 text-xs text-slate-400">Administration Panel</p>
        </div>

        {{-- Nav --}}
        <nav aria-label="Admin navigation" class="p-3">
            @php
                $adminNav = [
                    ['route' => 'admin.dashboard',          'label' => 'Dashboard',    'pattern' => 'admin.dashboard',        'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z'],
                    ['route' => 'admin.institutions.index', 'label' => 'Institutions', 'pattern' => 'admin.institutions.*',   'icon' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z'],
                    ['route' => 'admin.source-review.index', 'label' => 'Source review', 'pattern' => 'admin.source-review.*', 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ['route' => 'admin.keywords.index', 'label' => 'Keywords', 'pattern' => 'admin.keywords.*', 'icon' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z'],
                    ['route' => 'admin.messages.index', 'label' => 'Messages', 'pattern' => 'admin.messages.*', 'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75'],
                    ['route' => 'admin.cloudflare', 'label' => 'Cloudflare', 'pattern' => 'admin.cloudflare', 'icon' => 'M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z'],
                    ['route' => 'admin.settings.edit', 'label' => 'Settings', 'pattern' => 'admin.settings.*', 'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z'],
                    ['route' => 'admin.developer.index', 'label' => 'Developer', 'pattern' => 'admin.developer.*', 'icon' => 'M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5'],
                ];
            @endphp
            <ul class="space-y-0.5">
                @foreach ($adminNav as $item)
                    @php
                        $active = request()->routeIs($item['pattern'])
                            || ($item['route'] === 'admin.institutions.index' && request()->routeIs('admin.universities.*'));
                    @endphp
                    <li>
                        @if ($item['route'] === 'admin.institutions.index')
                            <details class="nav-dropdown" {{ $active ? 'open' : '' }}>
                                <summary class="nav-item {{ $active ? 'is-active' : '' }}">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                                    </svg>
                                    {{ $item['label'] }}
                            @if (($adminCounts[$item['route']] ?? null) !== null)
                                <span class="ml-auto rounded-full px-2 py-0.5 text-xs {{ $active ? 'bg-white/20' : 'bg-slate-100 text-slate-600' }}">{{ number_format($adminCounts[$item['route']]) }}</span>
                            @endif
                                    <svg class="nav-chevron ml-auto h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                </summary>
                                <div class="nav-dropdown-panel">
                                    <ul class="space-y-0.5 pl-7 pt-1">
                                        @foreach (['school' => 'Schools', 'college' => 'Colleges', 'university' => 'Universities'] as $slug => $label)
                                            <li>
                                                <a href="{{ $slug === 'university' ? route('admin.universities.index') : route('admin.institutions.index', ['type' => $slug]) }}"
                                                   class="nav-subitem {{ ($slug === 'university' ? request()->routeIs('admin.universities.*') : request('type') === $slug) ? 'is-active' : '' }}">
                                                    {{ $label }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </details>
                        @else
                        <a href="{{ route($item['route']) }}"
                           class="nav-item {{ $active ? 'is-active' : '' }}">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                            </svg>
                            {{ $item['label'] }}
                            @if (($adminCounts[$item['route']] ?? null) !== null)
                                <span class="ml-auto rounded-full px-2 py-0.5 text-xs {{ $active ? 'bg-white/20' : 'bg-slate-100 text-slate-600' }}">{{ number_format($adminCounts[$item['route']]) }}</span>
                            @endif
                            @if ($active)
                                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white"></span>
                            @endif
                        </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Sidebar footer --}}
        <div class="mt-auto border-t border-slate-100 p-4">
            <a href="{{ route('home') }}"
               class="flex items-center gap-2 text-xs font-medium text-slate-400 transition hover:text-blue-900">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
                View public site →
            </a>
        </div>
    </aside>

    {{-- ─── MAIN ─────────────────────────────────────────────────────────── --}}
    <div class="flex min-w-0 flex-1 flex-col md:h-screen md:overflow-hidden">

        {{-- Top header --}}
        <header class="z-30 grid shrink-0 grid-cols-[1fr_auto_1fr] items-center gap-3 border-b border-slate-200 bg-white px-4 py-3 sm:px-5">
            <div class="flex min-w-0 items-center gap-2 text-sm text-slate-400">
                <button id="admin-nav-open" type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded border border-slate-200 text-slate-700" aria-label="Toggle menu" aria-controls="admin-nav" aria-expanded="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
                </button>
                <a href="{{ route('admin.dashboard') }}" class="shrink-0 font-semibold text-blue-900 hover:underline">Admin</a>
                <span class="shrink-0">/</span>
                <span class="truncate font-semibold text-slate-800">@yield('heading', 'Dashboard')</span>
            </div>

            <time id="admin-clock" class="text-center text-sm font-semibold tabular-nums text-navy" datetime="">--:--:--</time>

            <div class="relative shrink-0 justify-self-end">
                <button id="account-menu-open" type="button" class="flex items-center gap-2 rounded border border-slate-200 bg-slate-50 px-2 py-1.5 sm:px-3" aria-expanded="false" aria-controls="account-menu">
                    <img src="{{ asset('favicon.png') }}" alt="" class="h-7 w-7 rounded-full object-cover">
                    <span class="hidden text-left text-xs leading-tight sm:block">
                        <span class="block font-semibold text-slate-800">{{ auth()->user()->name ?: 'Manoj Neupane' }}</span>
                        <span class="block text-[11px] font-medium text-slate-500">Admin</span>
                    </span>
                </button>
                <div id="account-menu" class="absolute right-0 z-40 mt-2 hidden w-44 rounded-xl border border-slate-200 bg-white p-1 shadow-lg">
                    <a href="{{ route('admin.settings.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Change password</a>
                    <button type="button" id="logout-open" class="block w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-700 hover:bg-red-50">Sign out</button>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                <dialog id="logout-dialog" class="w-full max-w-sm rounded-2xl border border-slate-200 p-0 shadow-xl backdrop:bg-slate-900/40">
                    <form method="dialog" class="p-6">
                        <h2 class="text-lg font-semibold text-slate-900">Sign out?</h2>
                        <p class="mt-2 text-sm text-slate-600">You will leave the admin panel and need to sign in again.</p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="submit" value="cancel" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Cancel</button>
                            <button type="submit" value="confirm" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white">Sign out</button>
                        </div>
                    </form>
                </dialog>
            </div>
        </header>

        {{-- Flash messages --}}
        @if (session('status') || session('success') || session('error'))
            @php $isError = session('error'); @endphp
            <div class="border-b px-5 py-3 {{ $isError ? 'border-red-200 bg-red-50' : 'border-emerald-200 bg-emerald-50' }}">
                <p class="text-sm font-semibold {{ $isError ? 'text-red-800' : 'text-emerald-800' }}">
                    {{ session('status') ?? session('success') ?? session('error') }}
                </p>
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 px-4 py-6 sm:px-5 sm:py-8 md:overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>

<script>
    const adminNav = document.getElementById('admin-nav');
    const adminOverlay = document.getElementById('admin-nav-overlay');
    const adminOpen = document.getElementById('admin-nav-open');
    const adminShell = document.getElementById('admin-shell');
    const desktop = () => window.matchMedia('(min-width: 768px)').matches;

    if (!desktop()) {
        adminOpen?.setAttribute('aria-expanded', 'false');
    }

    function setAdminNav(open) {
        if (desktop()) {
            adminShell.classList.toggle('is-collapsed', !open);
        } else {
            adminNav.classList.toggle('is-open', open);
            adminOverlay.classList.toggle('hidden', !open);
        }
        adminOpen.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    adminOpen?.addEventListener('click', () => {
        const open = adminOpen.getAttribute('aria-expanded') === 'true';
        setAdminNav(!open);
    });
    adminOverlay?.addEventListener('click', () => setAdminNav(false));
    const adminClock = document.getElementById('admin-clock');
    const tickClock = () => {
        if (!adminClock) {
            return;
        }
        const now = new Date();
        adminClock.dateTime = now.toISOString();
        adminClock.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    };
    tickClock();
    setInterval(tickClock, 1000);

    const accountMenu = document.getElementById('account-menu');
    const accountOpen = document.getElementById('account-menu-open');
    accountOpen?.addEventListener('click', () => {
        const open = accountMenu.classList.toggle('hidden') === false;
        accountOpen.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('click', (event) => {
        if (!accountOpen?.contains(event.target) && !accountMenu?.contains(event.target)) {
            accountMenu?.classList.add('hidden');
            accountOpen?.setAttribute('aria-expanded', 'false');
        }
    });

    const logoutDialog = document.getElementById('logout-dialog');
    document.getElementById('logout-open')?.addEventListener('click', () => logoutDialog?.showModal());
    logoutDialog?.addEventListener('close', () => {
        if (logoutDialog.returnValue === 'confirm') {
            document.getElementById('logout-form')?.requestSubmit();
        }
    });

    adminNav?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (!desktop()) {
                setAdminNav(false);
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>
