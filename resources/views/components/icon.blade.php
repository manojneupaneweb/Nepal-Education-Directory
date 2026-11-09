@props(['name' => 'info'])
@php
    $paths = [
        'home' => 'M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1z',
        'school' => 'M3 10 12 4l9 6M5 10v8h14v-8M12 13v5',
        'college' => 'M3 9 12 4l9 5-9 5-9-5zm0 4 9 5 9-5M12 14v6',
        'university' => 'M4 20V9l8-4 8 4v11M8 20v-6h8v6',
        'book' => 'M5 5h9a3 3 0 0 1 3 3v11H8a3 3 0 0 0-3 3zM5 5v14',
        'search' => 'm20 20-4.2-4.2M16 10.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0',
        'pin' => 'M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11zm0-8.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5',
        'mail' => 'M4 6h16v12H4zM4 7l8 6 8-6',
        'info' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18zM12 11v5M12 8h.01',
        'menu' => 'M4 7h16M4 12h16M4 17h16',
    ];
@endphp
<svg {{ $attributes->merge(['class' => 'h-4 w-4 shrink-0']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="{{ $paths[$name] ?? $paths['info'] }}"/>
</svg>
