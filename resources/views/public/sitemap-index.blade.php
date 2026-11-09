{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($maps as $map)
    <sitemap><loc>{{ route('sitemap.section', $map) }}</loc></sitemap>
@endforeach
</sitemapindex>
