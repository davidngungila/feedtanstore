@php
  $primaryImage = $product->images->firstWhere('is_primary', true);
  $imageToShow = $resolveImageUrl($primaryImage?->image_path) ?? $resolveImageUrl($product->image) ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=500&q=80';
  $oldPrice = $product->old_price ?? null;
  $badge = $oldPrice ? '-'.round((($oldPrice - $product->selling_price)/$oldPrice)*100).'%' : null;
  $rating = 4.0 + ($product->id % 11) / 10;
  $reviews = ($product->id * 7) % 480 + 20;
  $detailUrl = route('shop.product', $product);
  $productName = addslashes($product->name);
@endphp
<div class="p-card reveal {{ $class ?? '' }}" data-id="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-rating="{{ $rating }}" data-sale="{{ $oldPrice ? '1' : '0' }}">
  <div class="p-media" onclick="openQuickView('{{ $product->id }}')" role="button" tabindex="0" aria-label="{{ $product->name }}">
    <img src="{{ $imageToShow }}" alt="{{ $product->name }}" loading="lazy">
    @if($badge)
      <span class="p-badge pill pill-orange">{{ $badge }}</span>
    @endif
    <button class="p-fav" aria-label="{{ __('Save to wishlist') }}" onclick="event.stopPropagation(); toggleFav(this)">
      <i class="fa-regular fa-heart"></i>
    </button>
    <button class="quick-add" data-add-btn aria-label="{{ __('Add to cart') }}" onclick="event.stopPropagation(); addToCart('{{ $product->id }}', '{{ $productName }}', {{ $product->selling_price }})">
      <i class="fa-solid fa-cart-plus"></i>
    </button>
  </div>
  <div class="p-body">
    <span class="p-cat">{{ $product->category->name ?? __('Uncategorized') }}</span>
    <a class="p-name" href="{{ $detailUrl }}">{{ $product->name }}</a>
    <div class="p-rating">
      <i class="fa-solid fa-star"></i>
      {{ number_format($rating, 1) }} <span>({{ $reviews }})</span>
    </div>
    <div class="p-price-row">
      <span class="p-price" data-price="{{ $product->selling_price }}">TZS {{ number_format($product->selling_price, 0) }}</span>
      @if($oldPrice)
        <span class="p-price-old">TZS {{ number_format($oldPrice, 0) }}</span>
      @endif
    </div>
    <span class="p-unit">{{ __('per item') }}</span>
    <div class="p-actions" id="actions-{{ $product->id }}">
      <button class="btn btn-dark btn-sm" onclick="addToCart('{{ $product->id }}', '{{ $productName }}', {{ $product->selling_price }})">
        <i class="fa-solid fa-cart-shopping"></i>
        {{ __('Add to cart') }}
      </button>
      <a href="{{ $detailUrl }}" class="btn btn-outline btn-sm">{{ __('Details') }}</a>
    </div>
  </div>
</div>
