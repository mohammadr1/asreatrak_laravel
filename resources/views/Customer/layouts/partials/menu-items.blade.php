@foreach ($items as $item)
@php
$menuUrl = $item->category
? route('category.show', $item->category)
: ($item->url ?: '#');
$isActive = $item->category
&& request()->routeIs('category.show')
&& request()->route('category')?->is($item->category);
@endphp
<li class="{{ $isActive ? 'active' : '' }} {{ $item->children->isNotEmpty() ? 'has-submenu' : '' }}">
  <a href="{{ $menuUrl }}" @if($item->children->isNotEmpty()) aria-haspopup="true" @endif>
    {{ $item->title }}
    @if($item->children->isNotEmpty()) <i class="bi bi-chevron-down" aria-hidden="true"></i> @endif
  </a>
  @if($item->children->isNotEmpty())
  <ul class="dropdown-menu">
    @include('Customer.layouts.partials.menu-items', ['items' => $item->children])
  </ul>
  @endif
</li>
@endforeach