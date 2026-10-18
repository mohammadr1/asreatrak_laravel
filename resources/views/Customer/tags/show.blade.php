@extends('customer.layouts.master-one-col')

@section('news-seo-title', e($tag->name . ' | ' . config('app.name')))

@section('head-tag')
    <link rel="stylesheet" href="{{ asset('assets/css/style_category.css') }}">
@endsection

@section('content')
@php
    use Morilog\Jalali\CalendarUtils;

    $fa = fn ($value) => CalendarUtils::convertNumbers((string) $value);
@endphp

<div class="col-12">

    {{-- سربرگ برچسب --}}
    <div class="cat-head">
        <h1>{{ $tag->name }}</h1>

        <span class="cat-count">
            <i class="bi bi-tags"></i>
            {{ $fa(number_format($news->total())) }} خبر
        </span>
    </div>

    {{-- اگر این برچسب خبری ندارد --}}
    @if($news->isEmpty())

        <div class="cat-empty">
            <i class="bi bi-inbox"></i>
            هنوز خبری برای این برچسب ثبت نشده است.
        </div>

    @else

        {{-- فهرست خبرهای مرتبط با برچسب --}}
        <div class="row g-4">

            @foreach($news as $item)
                <div class="col-12 col-sm-6 col-lg-4">

                    <a href="{{ route('news.show', $item) }}" class="cat-card">

                        {{-- تصویر خبر --}}
                        <div class="cat-card__media">
                            <img
                                src="{{ $item->cover_url }}"
                                alt="{{ $item->featuredMedia?->alt ?: $item->title }}"
                                loading="{{ $loop->first && $news->onFirstPage() ? 'eager' : 'lazy' }}"
                            >
                        </div>

                        {{-- اطلاعات خبر --}}
                        <div class="cat-card__body">

                            <h2 class="cat-card__title">
                                {{ $item->title }}
                            </h2>

                            @if($item->lead)
                                <p class="cat-hero__lead">
                                    {{ $item->lead }}
                                </p>
                            @endif

                            <div class="cat-card__meta">

                                @if($item->jalali_published_at)
                                    <span>
                                        <i class="bi bi-clock"></i>
                                        {{ $fa($item->jalali_published_at) }}
                                    </span>
                                @endif

                                <span>
                                    <i class="bi bi-eye"></i>
                                    {{ $fa(number_format($item->views_count ?? 0)) }}
                                </span>

                            </div>
                        </div>

                    </a>

                </div>
            @endforeach

        </div>

        {{-- صفحه‌بندی --}}
        <div class="mt-5 d-flex justify-content-center">
            {{ $news->links() }}
        </div>

    @endif

</div>
@endsection