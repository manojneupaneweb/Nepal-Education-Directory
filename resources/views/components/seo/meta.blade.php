@php
    $context = '@'.'context';
    $type = '@'.'type';
    $breadcrumbJson = $seo->breadcrumbs === [] ? null : json_encode([
        $context => 'https://schema.org',
        $type => 'BreadcrumbList',
        'itemListElement' => collect($seo->breadcrumbs)->values()->map(fn ($crumb, $index) => [
            $type => 'ListItem',
            'position' => $index + 1,
            'name' => $crumb['name'],
            'item' => $crumb['url'],
        ])->all(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
<title>{{ $seo->title }} — {{ config('app.name') }}</title>
<meta name="description" content="{{ $seo->description }}">
<meta name="robots" content="{{ $seo->robots }}">
<link rel="canonical" href="{{ $seo->canonical }}">
<meta property="og:title" content="{{ $seo->title }}">
<meta property="og:description" content="{{ $seo->description }}">
<meta property="og:url" content="{{ $seo->canonical }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name') }}">
@if ($seo->image)
    <meta property="og:image" content="{{ $seo->image }}">
@endif
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $seo->title }}">
<meta name="twitter:description" content="{{ $seo->description }}">
@if (filled(config('seo.google_site_verification')))
    <meta name="google-site-verification" content="{{ config('seo.google_site_verification') }}">
@endif
@if ($breadcrumbJson)
    <script type="application/ld+json">{!! $breadcrumbJson !!}</script>
@endif
@foreach ($seo->jsonLd as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach
