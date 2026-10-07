@extends('Customer.layouts.master-one-col')

@section('news-seo-title', e(($news->meta_title ?: $news->title) . ' | ' . config('app.name')))

@section('head-tag')
<meta name="description" content="{{ $news->meta_description ?: $news->lead }}">
@include('Customer.news.seo', ['news' => $news])
<link rel="stylesheet" href="{{ asset('assets/css/style_single.css') }}">
<style>
    .video-news { min-width: 0; overflow-wrap: anywhere; }
    .video-news-player { aspect-ratio: 16 / 9; width: 100%; overflow: hidden; border-radius: 16px; background: #0f2233; margin-block: 24px; }
    .video-news-player iframe { display: block; width: 100%; height: 100%; border: 0; }
    .video-news .article-body img { max-width: 100%; height: auto; }
    .video-news .article-body figure { max-width: 100%; margin-inline: 0; }
    .inline-aparat-player { display: block; width: 100%; aspect-ratio: 16 / 9; overflow: hidden; border-radius: 12px; margin-block: 16px; background: #0f2233; }
    .inline-aparat-player iframe { display: block; width: 100%; height: 100%; border: 0; }
    .video-news-gallery { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 16px; }
    .video-news-gallery img { width: 100%; aspect-ratio: 16 / 9; object-fit: contain; border-radius: 12px; }
    .video-news-links { display: flex; flex-wrap: wrap; gap: 12px; }
    .video-news-links a { display: inline-flex; align-items: center; min-height: 44px; }
</style>
@endsection

@section('content')
<article class="col-lg-8 video-news">
    <header class="article-header">
        <span class="article-category-badge">{{ $news->post_type === 'video' ? 'خبر ویدیویی' : ($news->categories->first()?->name ?? 'خبر') }}</span>
        @if($news->uptitle)<p class="article-kicker">{{ $news->uptitle }}</p>@endif
        <h1 class="article-title">{{ $news->title }}</h1>
        @if($news->lead)<p class="article-lead">{{ $news->lead }}</p>@endif
        <div class="article-meta-row">
            @if($news->reporter)<span>{{ $news->reporter->name }}</span>@endif
            @if($news->published_at)
                <time datetime="{{ $news->published_at->toIso8601String() }}">{{ \Morilog\Jalali\Jalalian::fromDateTime($news->published_at->copy()->timezone('Asia/Tehran'))->format('Y/m/d H:i') }}</time>
            @endif
        </div>
    </header>

    @if($embedUrl)
        <div class="video-news-player">
            <iframe src="{{ $embedUrl }}" title="{{ 'ویدیوی ' . $news->title }}"
                loading="lazy" referrerpolicy="strict-origin-when-cross-origin"
                sandbox="allow-scripts allow-same-origin allow-presentation"
                allow="fullscreen; picture-in-picture" allowfullscreen></iframe>
        </div>
        <p class="video-news-links"><a href="https://www.aparat.com/v/{{ \App\Support\AparatVideo::id($news->aparat_video_id) }}" target="_blank" rel="noopener noreferrer">اگر ویدیو پخش نمی‌شود، در آپارات تماشا کنید</a></p>
    @elseif($news->post_type === 'video')
        <p role="status">ویدیو در حال حاضر در دسترس نیست.</p>
    @elseif($news->featuredMedia)
        <div class="article-hero-wrap"><img src="{{ $news->featuredMedia->variantUrl($news->featured_media_variant ?: 'original') }}" alt="{{ $news->featuredMedia->alt ?: $news->title }}"></div>
    @endif

    <div class="article-body">{!! \App\Support\NewsContent::render($news->content) !!}</div>

    @if($news->media->isNotEmpty())
        <section aria-label="تصاویر خبر" class="video-news-gallery">
            @foreach($news->media as $image)
                @php($imageUrl = $image->variantUrl('watermarked') ?: $image->variantUrl('cropped') ?: $image->variantUrl('original'))
                @if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $image->alt ?: $news->title }}" loading="lazy">@endif
            @endforeach
        </section>
    @endif
    <nav class="video-news-links" aria-label="برچسب‌های خبر">
        @foreach($news->tags as $tag)<a href="{{ route('tag.show', $tag->slug) }}">{{ $tag->name }}</a>@endforeach
    </nav>
    @if($news->relatedNews->isNotEmpty())
        <section class="related-section mt-4" aria-labelledby="related-news-heading">
            <h2 id="related-news-heading" class="section-title">خبرهای مرتبط</h2>
            <div class="row g-3 mt-1">
                @foreach($news->relatedNews as $related)
                    <div class="col-12 col-md-6">
                        <a class="related-card" href="{{ route('news.show', $related) }}">
                            @php($relatedImage = $related->featuredMedia?->variantUrl($related->featured_media_variant ?: 'original'))
                            @if($relatedImage)
                                <img class="related-card-img" src="{{ $relatedImage }}" alt="" loading="lazy" style="max-width:100%;aspect-ratio:16/9;object-fit:cover">
                            @endif
                            <div class="related-card-body"><h3 class="related-card-title">{{ $related->title }}</h3></div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</article>
@endsection
