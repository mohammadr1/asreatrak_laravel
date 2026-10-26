
@if ($news->isNotEmpty())
    <div class="breaking-bar">
        <div class="container-xl p-0 d-flex align-items-stretch w-100">
            <div class="breaking-label">
                <span class="dot" aria-hidden="true"></span>
                آخرین اخبار
            </div>

            <div class="ticker-wrap">
                <div class="ticker-inner" id="ticker"
                     aria-live="polite" aria-atomic="true">

                    @foreach ($news as $item)
                        <a class="ticker-item"
                           href="{{ route('news.show', $item) }}">
                            {{ $item->title }}
                        </a>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endif
