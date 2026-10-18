@php
    $socials = collect(config('social.links'))->filter(fn ($s) => filled($s['url'] ?? null));
@endphp

@if($socials->isNotEmpty())

@once
<style>
.soc-intro{margin:0 0 14px;font-size:.85rem;opacity:.75;line-height:1.9}
.soc-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
.soc-item{--soc:#0d6efd;display:flex;align-items:center;gap:10px;padding:10px;
  border:1px solid rgba(128,128,128,.28);border-radius:12px;
  text-decoration:none;color:inherit;
  transition:transform .2s,box-shadow .2s,border-color .2s}
.soc-item:hover{transform:translateY(-2px);border-color:var(--soc);
  box-shadow:0 6px 16px rgba(0,0,0,.15);color:inherit}
.soc-item:focus-visible{outline:2px solid var(--soc);outline-offset:2px}
.soc-icon{flex:0 0 40px;width:40px;height:40px;border-radius:10px;display:grid;place-items:center;
  color:#fff;font-size:1.25rem;font-weight:800;background:var(--soc-bg,var(--soc))}
.soc-icon--img{background:#fff;padding:6px}
.soc-icon--img img{width:100%;height:100%;object-fit:contain}
.soc-text{display:flex;flex-direction:column;min-width:0;line-height:1.5}
.soc-text strong{font-size:.88rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.soc-text small{font-size:.72rem;opacity:.65}
@media (max-width:420px){.soc-grid{grid-template-columns:1fr}}
</style>
@endonce

<div class="sidebar-widget">
    <div class="widget-header"><i class="bi bi-people-fill"></i> {{ config('social.title') }}</div>
    <div class="widget-body">
        <p class="soc-intro">برای دریافت سریع‌ترین اخبار، در شبکه‌های اجتماعی ما عضو شوید.</p>

        <div class="soc-grid">
            @foreach($socials as $s)
                @php
                    $hasImage = ! empty($s['image']) && file_exists(public_path($s['image']));
                    $style = '--soc:' . ($s['color'] ?? '#0d6efd') . ';'
                           . (! empty($s['bg']) ? '--soc-bg:' . $s['bg'] . ';' : '');
                @endphp

                <a href="{{ $s['url'] }}" class="soc-item" style="{{ $style }}"
                   target="_blank" rel="noopener noreferrer"
                   aria-label="{{ $s['title'] }} — {{ $s['hint'] ?? 'عضویت' }}">

                    @if($hasImage)
                        <span class="soc-icon soc-icon--img">
                            <img src="{{ asset($s['image']) }}" alt="" loading="lazy">
                        </span>
                    @elseif(! empty($s['icon']))
                        <span class="soc-icon"><i class="bi {{ $s['icon'] }}" aria-hidden="true"></i></span>
                    @else
                        <span class="soc-icon" aria-hidden="true">{{ mb_substr($s['title'], 0, 1) }}</span>
                    @endif

                    <span class="soc-text">
                        <strong>{{ $s['title'] }}</strong>
                        <small>{{ $s['hint'] ?? 'عضویت' }}</small>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</div>

@endif