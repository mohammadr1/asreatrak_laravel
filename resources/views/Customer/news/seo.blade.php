@php
    $canonical = route('news.show', $news);
    $seoImage = $news->featuredMedia?->variantUrl($news->featured_media_variant ?: 'original');
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $news->title,
        'description' => $news->meta_description ?: $news->lead,
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
        'inLanguage' => 'fa',
    ];
    if ($news->published_at) $schema['datePublished'] = $news->published_at->toIso8601String();
    if ($seoImage) $schema['image'] = [$seoImage];
    if ($news->reporter) $schema['author'] = ['@type' => 'Person', 'name' => $news->reporter->name];
@endphp
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $news->meta_title ?: $news->title }}">
<meta property="og:description" content="{{ $news->meta_description ?: $news->lead }}">
<meta property="og:url" content="{{ $canonical }}">
@if($seoImage)<meta property="og:image" content="{{ $seoImage }}">@endif
<script type="application/ld+json">{!! json_encode($schema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) !!}</script>
