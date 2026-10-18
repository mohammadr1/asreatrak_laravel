@extends('customer.layouts.master-one-col')

@section('news-seo-title', e($category->name . ' | ' . config('app.name')))

@section('head-tag')
<link rel="stylesheet" href="{{ asset('assets/css/style_category.css') }}">
@endsection

@section('content')
@php
    use Morilog\Jalali\CalendarUtils;
    $fa = fn ($v) => CalendarUtils::convertNumbers((string) $v);
    $showHero = $hero && $news->onFirstPage();
@endphp

<div class="col-12">

    <div class="cat-head">
        <h1>{{ $category->name }}</h1>
        <span class="cat-count">
            <i class="bi bi-newspaper"></i>
            {{ $fa(number_format($news->total() + ($hero ? 1 : 0))) }} خبر
        </span>
    </div>

    @if(! $hero)
        <div class="cat-empty">
            <i class="bi bi-inbox"></i>
            هنوز خبری در این دسته‌بندی ثبت نشده است.
        </div>
    @else

        {{-- خبر شاخص (فقط صفحه‌ی اول) --}}
        @if($showHero)
            <a href="{{ route('news.show', $hero) }}" class="cat-card cat-hero">
                <div class="cat-card__media">
                    <img src="{{ $hero->cover_url }}" alt="{{ $hero->featuredMedia?->alt ?: $hero->title }}"
                         fetchpriority="high">
                </div>
                <div class="cat-card__body">
                    <span class="cat-hero__badge">
                        {{ $hero->pivot->is_top ? 'خبر ویژه‌ی' : 'تازه‌ترین خبر' }} {{ $category->name }}
                    </span>
                    <h2 class="cat-card__title">{{ $hero->title }}</h2>
                    @if($hero->lead)
                        <p class="cat-hero__lead">{{ $hero->lead }}</p>
                    @endif
                    <div class="cat-card__meta">
                        @if($hero->jalali_published_at)
                            <span><i class="bi bi-clock"></i> {{ $fa($hero->jalali_published_at) }}</span>
                        @endif
                        <span><i class="bi bi-eye"></i> {{ $fa(number_format($hero->views_count ?? 0)) }}</span>
                    </div>
                </div>
            </a>
        @endif

        {{-- شبکه‌ی کارت‌ها --}}
        <div class="row g-4">
            @foreach($news as $item)
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route('news.show', $item) }}" class="cat-card">
                        <div class="cat-card__media">
                            <img src="{{ $item->cover_url }}"
                                 alt="{{ $item->featuredMedia?->alt ?: $item->title }}" loading="lazy">
                        </div>
                        <div class="cat-card__body">
                            <h3 class="cat-card__title">{{ $item->title }}</h3>
                            <div class="cat-card__meta">
                                @if($item->jalali_published_at)
                                    <span><i class="bi bi-clock"></i> {{ $fa($item->jalali_published_at) }}</span>
                                @endif
                                <span><i class="bi bi-eye"></i> {{ $fa(number_format($item->views_count ?? 0)) }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $news->links() }}
        </div>
    @endif

</div>
@endsection