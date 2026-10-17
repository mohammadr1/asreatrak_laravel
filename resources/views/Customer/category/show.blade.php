@extends('customer.layouts.master-one-col')

@section('title', $category->name)

@section('content')
<div class="container-xl my-4">
  <h1 class="h3 mb-4">{{ $category->name }}</h1>

  @forelse($news as $item)
    <article class="mb-3 pb-3 border-bottom">
      <h2 class="h5">
        <a href="#">{{ $item->title }}</a>
      </h2>
      <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
    </article>
  @empty
    <p>هنوز خبری در این دسته‌بندی ثبت نشده است.</p>
  @endforelse

  <div class="mt-4">
    {{ $news->links() }}
  </div>
</div>
@endsection