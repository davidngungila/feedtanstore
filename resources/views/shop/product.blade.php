<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-L0V2LBGD64"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-L0V2LBGD64');
</script>
@php
  $logoUrl = asset('logo-image-feedtan-store.png');
  $productCanonicalUrl = route('shop.product', $product);
  $seo = seo_product($product);
  $primaryImage = $product->images->firstWhere('is_primary', true);
  $baseUrl = $settings->store_url ?? config('app.url');
  $resolveImageUrl = function ($path) use ($baseUrl) {
    if (!$path) return null;
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
      $parsed = parse_url($path);
      if (isset($parsed['path'])) {
        $path = ltrim($parsed['path'], '/');
        return str_starts_with($path, 'storage/')
          ? rtrim($baseUrl, '/') . '/' . $path
          : rtrim($baseUrl, '/') . '/storage/' . $path;
      }
    }
    $cleanPath = ltrim($path, '/');
    return str_starts_with($cleanPath, 'storage/')
      ? rtrim($baseUrl, '/') . '/' . $cleanPath
      : rtrim($baseUrl, '/') . '/storage/' . $cleanPath;
  };
  $imageToShow = $resolveImageUrl($primaryImage?->image_path) ?? $resolveImageUrl($product->image) ?? $logoUrl;
  $allImages = collect([$primaryImage])->filter()
      ->map(fn($img) => $resolveImageUrl($img->image_path))
      ->filter()
      ->values()
      ->push($imageToShow)
      ->unique()
      ->values()
      ->all();
  $oldPrice = $product->old_price ?? null;
  $inStock = $product->quantity > 0;
  $lowStock = $inStock && $product->quantity <= 5;

  $breadcrumbList = [
      '@context' => 'https://schema.org',
      '@type' => 'BreadcrumbList',
      'itemListElement' => [
          [
              '@type' => 'ListItem',
              'position' => 1,
              'name' => 'Home',
              'item' => route('shop.index'),
          ],
          [
              '@type' => 'ListItem',
              'position' => 2,
              'name' => $product->category->name ?? 'Products',
              'item' => $product->category ? route('shop.index', ['category' => $product->category->slug]) : route('shop.index'),
          ],
          [
              '@type' => 'ListItem',
              'position' => 3,
              'name' => $product->name,
          ],
      ],
  ];
  $productSchema = [
      '@context' => 'https://schema.org',
      '@graph' => [
          $breadcrumbList,
          [
              '@type' => 'Product',
              'name' => $product->name,
              'description' => $seo['description'],
              'image' => array_values(array_unique(array_merge($allImages, [$seo['image']]))),
              'sku' => $product->sku ?: (string) $product->id,
              'category' => $product->category->name ?? 'Uncategorized',
              'brand' => [
                  '@type' => 'Brand',
                  'name' => $product->brand->name ?? 'Feedtan Store',
              ],
              'offers' => [
                  '@type' => 'Offer',
                  'url' => $productCanonicalUrl,
                  'priceCurrency' => 'TZS',
                  'price' => number_format((float) $product->selling_price, 0, '.', ''),
                  'availability' => $inStock
                      ? 'https://schema.org/InStock'
                      : 'https://schema.org/OutOfStock',
                  'itemCondition' => 'https://schema.org/NewCondition',
              ],
          ],
      ],
  ];

  $relatedProducts = $product->category
      ? \App\Models\Product::where('category_id', $product->category_id)
          ->where('id', '!=', $product->id)
          ->where('is_active', true)
          ->where('is_available_online', true)
          ->where('quantity', '>', 0)
          ->with(['category', 'images'])
          ->orderBy('name')
          ->take(4)
          ->get()
      : collect();
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
<meta name="keywords" content="{{ $seo['keywords'] }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta name="author" content="Feedtan Store">
<meta name="theme-color" content="#1B4332">
<link rel="canonical" href="{{ $productCanonicalUrl }}">
<link rel="icon" type="image/png" href="{{ $logoUrl }}">
<link rel="apple-touch-icon" href="{{ $logoUrl }}">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="Feedtan Store">
<meta property="og:type" content="product">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:url" content="{{ $productCanonicalUrl }}">
<meta property="og:image" content="{{ $seo['image'] }}">
<meta property="og:image:secure_url" content="{{ $seo['image'] }}">
<meta property="og:image:alt" content="{{ $product->name }}">
<meta property="product:price:amount" content="{{ number_format((float) $product->selling_price, 0, '.', '') }}">
<meta property="product:price:currency" content="TZS">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo['title'] }}">
<meta name="twitter:description" content="{{ $seo['description'] }}">
<meta name="twitter:image" content="{{ $seo['image'] }}">
<meta name="twitter:image:alt" content="{{ $product->name }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;0,9..144,700;0,9..144,900;1,9..144,500&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@include('shop.partials.styles')
<style>
/* ---------- Breadcrumb ---------- */
.pd-crumbs{display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px;color:var(--ink-soft);margin:26px 0 20px;}
.pd-crumbs a{font-weight:600;color:var(--green-700);}
.pd-crumbs a:hover{text-decoration:underline;}
.pd-crumbs .sep{color:var(--line);}

/* ---------- Gallery ---------- */
.pd-grid{display:grid;grid-template-columns:1fr 1fr;gap:44px;align-items:start;}
@media(max-width:900px){.pd-grid{grid-template-columns:1fr;gap:28px;}}
.pd-gallery{position:sticky;top:96px;}
@media(max-width:900px){.pd-gallery{position:static;}}
.pd-media{
  position:relative;border-radius:var(--radius-l);overflow:hidden;background:var(--parchment-dim);
  border:1px solid rgba(219,212,194,.5);cursor:zoom-in;
}
.pd-media img{width:100%;aspect-ratio:1/1;object-fit:cover;}
.pd-media .p-badge{top:14px;left:14px;z-index:2;}
.pd-fav{
  position:absolute;top:14px;right:14px;z-index:2;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.95);
  display:flex;align-items:center;justify-content:center;border:none;color:var(--ink-soft);
  box-shadow:0 2px 8px rgba(15,42,31,.15);transition:transform .18s,color .18s;
}
.pd-fav:hover{color:var(--red);transform:scale(1.12);}
.pd-fav.active{color:var(--red);}
.pd-fav.active svg{animation:heartPop .35s var(--ease);}
.pd-thumbs{display:flex;gap:10px;margin-top:12px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none;}
.pd-thumbs::-webkit-scrollbar{display:none;}
.pd-thumb{
  width:74px;height:74px;flex-shrink:0;border-radius:var(--radius-m);border:2px solid var(--line);
  background:var(--parchment-dim);cursor:pointer;overflow:hidden;padding:0;transition:border-color .15s;
}
.pd-thumb img{width:100%;height:100%;object-fit:cover;}
.pd-thumb:hover,.pd-thumb.active{border-color:var(--green-700);}

/* ---------- Info panel ---------- */
.pd-info .pd-cat{font-size:12px;font-weight:700;color:var(--green-700);text-transform:uppercase;letter-spacing:.05em;display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.pd-info .pd-cat .dot{width:4px;height:4px;border-radius:50%;background:var(--line);}
.pd-title{font-size:clamp(26px,3.4vw,38px);line-height:1.1;margin:10px 0 12px;font-weight:700;}
.pd-rating{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--ink-soft);margin-bottom:16px;}
.pd-rating svg{color:var(--orange);}
.pd-price-row{display:flex;align-items:baseline;gap:12px;flex-wrap:wrap;margin-bottom:18px;}
.pd-price{font-family:var(--font-display);font-size:clamp(28px,3.2vw,36px);font-weight:700;}
.pd-price-old{font-size:18px;color:#A39E8C;text-decoration:line-through;font-weight:500;}
.pd-save{background:#FCEADB;color:var(--orange-dark);font-weight:800;font-size:12px;padding:4px 10px;border-radius:999px;}
.pd-desc{font-size:15.5px;color:var(--ink-soft);line-height:1.7;margin-bottom:22px;}
.pd-meta-list{list-style:none;padding:0;margin:0 0 22px;display:flex;flex-direction:column;}
.pd-meta-list li{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--parchment-dim);font-size:14px;}
.pd-meta-list li span:first-child{color:var(--ink-soft);}
.pd-meta-list li span:last-child{font-weight:700;}
.stock-pill{display:inline-flex;align-items:center;gap:6px;}
.stock-pill .led{width:8px;height:8px;border-radius:50%;}
.stock-pill.in .led{background:var(--success);box-shadow:0 0 0 4px rgba(46,139,87,.15);}
.stock-pill.low .led{background:var(--orange);box-shadow:0 0 0 4px rgba(232,137,58,.15);}
.stock-pill.out .led{background:var(--red);box-shadow:0 0 0 4px rgba(214,69,69,.15);}

/* Trust bullets */
.pd-trust{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:24px;}
.trust-bullet{
  display:flex;gap:10px;align-items:flex-start;background:var(--green-100);border-radius:var(--radius-m);padding:12px 14px;
}
.trust-bullet svg{flex-shrink:0;color:var(--green-700);margin-top:1px;}
.trust-bullet b{display:block;font-size:13px;}
.trust-bullet span{font-size:12px;color:var(--ink-soft);}
@media(max-width:480px){.pd-trust{grid-template-columns:1fr;}}

/* Buy box */
.pd-buy{
  background:#fff;border:1px solid var(--line);border-radius:var(--radius-l);padding:20px;
  box-shadow:var(--shadow-card);
}
.pd-buy-row{display:flex;gap:12px;align-items:stretch;}
.pd-buy .qty-stepper{flex:0 0 140px;}
.qty-stepper{display:flex;align-items:center;border:1.5px solid var(--line);border-radius:999px;overflow:hidden;flex:1;background:#fff;}
.qty-stepper button{width:44px;height:50px;background:transparent;border:none;font-size:18px;font-weight:700;color:var(--green-700);}
.qty-stepper button:hover{background:var(--green-100);}
.qty-stepper span{flex:1;text-align:center;font-weight:800;font-size:16px;}
.pd-buy .btn-main{flex:1;}
.pd-buy-actions{display:flex;gap:10px;margin-top:12px;}
.pd-buy-actions .btn{flex:1;}
@media(max-width:400px){
  .pd-buy-row{flex-direction:column;}
  .pd-buy .qty-stepper{flex:none;}
}

/* ---------- Lightbox ---------- */
.lightbox{
  position:fixed;inset:0;z-index:500;background:rgba(6,14,9,.94);display:flex;align-items:center;justify-content:center;
  opacity:0;visibility:hidden;transition:opacity .22s ease;
}
.lightbox.open{opacity:1;visibility:visible;}
.lightbox img{max-width:92vw;max-height:82vh;border-radius:var(--radius-m);box-shadow:var(--shadow-pop);}
.lb-close{position:absolute;top:18px;right:18px;width:42px;height:42px;border-radius:50%;border:none;background:rgba(255,255,255,.12);color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;}
.lb-arrow{position:absolute;top:50%;transform:translateY(-50%);width:46px;height:46px;border-radius:50%;border:none;background:rgba(255,255,255,.12);color:#fff;display:flex;align-items:center;justify-content:center;transition:background .15s;}
.lb-arrow:hover{background:rgba(255,255,255,.28);}
.lb-arrow.prev{left:16px;}
.lb-arrow.next{right:16px;}
.lb-counter{position:absolute;bottom:20px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.85);font-size:13px;font-weight:700;}

/* ---------- Sticky mobile buy bar ---------- */
.pd-sticky{
  position:fixed;left:0;right:0;bottom:0;z-index:120;background:rgba(255,255,255,.98);backdrop-filter:blur(12px);
  border-top:1px solid var(--line);padding:10px 14px;display:none;align-items:center;gap:10px;
  box-shadow:0 -8px 24px rgba(15,42,31,.12);padding-bottom:calc(10px + env(safe-area-inset-bottom));
}
.pd-sticky .psb-left{flex:1;min-width:0;}
.pd-sticky .psb-left b{font-family:var(--font-display);font-size:17px;display:block;line-height:1.15;}
.pd-sticky .psb-left span{font-size:11.5px;color:var(--ink-soft);}
.pd-sticky .qty-stepper{flex:none;width:118px;}
.pd-sticky .qty-stepper button{width:36px;height:44px;}
.pd-sticky .btn{padding:0 18px;height:46px;}
@media(max-width:900px){
  .pd-sticky{display:flex;}
  .with-pd-sticky{padding-bottom:80px;}
}

/* ---------- Related ---------- */
.rel-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:20px;}
  @media(max-width:1080px){.rel-grid{grid-template-columns:repeat(3,minmax(0,1fr));}}
  @media(max-width:760px){.rel-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;}}
</style>
</head>
<body class="with-pd-sticky">

@include('shop.partials.header', ['activeNav' => 'shop'])

<main id="mainContent">
  <div class="wrap">
    <nav class="pd-crumbs" aria-label="Breadcrumb">
      <a href="{{ route('shop.index') }}">{{ __('Home') }}</a>
      <span class="sep">/</span>
      @if($product->category)
        <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
        <span class="sep">/</span>
      @endif
      <span>{{ $product->name }}</span>
    </nav>

    <div class="pd-grid">
      <div class="pd-gallery">
        <div class="pd-media p-media" onclick="openLightbox(lbIndex)">
          <img id="mainImage" src="{{ $imageToShow }}" alt="{{ $product->name }}">
          @if($oldPrice)
            <span class="p-badge pill pill-orange">-{{ round((($oldPrice - $product->selling_price)/$oldPrice)*100) }}%</span>
          @endif
          <button class="pd-fav" aria-label="{{ __('Save to wishlist') }}" onclick="event.stopPropagation(); toggleFav(this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
          </button>
        </div>
        @if(count($allImages) > 1)
        <div class="pd-thumbs" id="pdThumbs">
          @foreach($allImages as $i => $imgSrc)
            <button class="pd-thumb {{ $i === 0 ? 'active' : '' }}" onclick="changeImage('{{ $imgSrc }}', this, {{ $i }})">
              <img src="{{ $imgSrc }}" alt="{{ $product->name }}">
            </button>
          @endforeach
        </div>
        @endif
      </div>

      <div class="pd-info pd-card" data-id="{{ $product->id }}">
        <span class="pd-cat">
          {{ $product->category->name ?? 'Uncategorized' }}
          @if($product->brand)<span class="dot"></span>{{ $product->brand->name }}@endif
        </span>
        <h1 class="pd-title p-name">{{ $product->name }}</h1>
        <div class="pd-rating">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8l-6.2 3.2 1.2-6.8-5-4.9 6.9-1z"/></svg>
          4.5 ({{ rand(10, 500) }})
        </div>
        <div class="pd-price-row">
          <span class="pd-price p-price" data-price="{{ $product->selling_price }}">TZS {{ number_format($product->selling_price, 0) }}</span>
          @if($oldPrice)
            <span class="pd-price-old">TZS {{ number_format($oldPrice, 0) }}</span>
            <span class="pd-save">{{ __('You save') }} TZS {{ number_format($oldPrice - $product->selling_price, 0) }}</span>
          @endif
        </div>
        @if($product->description)
          <p class="pd-desc">{{ $product->description }}</p>
        @endif

        <ul class="pd-meta-list">
          <li>
            <span>{{ __('Availability') }}</span>
            <span class="stock-pill {{ $inStock ? ($lowStock ? 'low' : 'in') : 'out' }}">
              <span class="led"></span>
              {{ $inStock ? ($lowStock ? __('Low stock') : __('In stock')) : __('Out of stock') }}
            </span>
          </li>
          <li><span>{{ __('Delivery fee') }}</span><span>TZS 3,000 {{ __('or free pickup') }}</span></li>
          @if($product->sku)<li><span>SKU</span><span class="mono">{{ $product->sku }}</span></li>@endif
        </ul>

        <div class="pd-trust">
          <div class="trust-bullet">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/></svg>
            <div><b>{{ __('Fast delivery') }}</b><span>{{ __('Across Moshi, Kilimanjaro') }}</span></div>
          </div>
          <div class="trust-bullet">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v9h14v-9"/><path d="M9 19v-6h6v6"/></svg>
            <div><b>{{ __('Store pickup') }}</b><span>{{ __('Kiboriloni, Moshi') }}</span></div>
          </div>
          <div class="trust-bullet">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
            <div><b>{{ __('Quality checked') }}</b><span>{{ __('Before dispatch') }}</span></div>
          </div>
          <div class="trust-bullet">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
            <div><b>{{ __('Call to order') }}</b><span>+255 717 358 865</span></div>
          </div>
        </div>

        <div class="pd-buy">
          <div class="pd-buy-row">
            <div class="qty-stepper" aria-label="{{ __('Quantity') }}">
              <button onclick="changeProductQty(-1)" aria-label="{{ __('Decrease quantity') }}">−</button>
              <span id="productQty">1</span>
              <button onclick="changeProductQty(1)" aria-label="{{ __('Increase quantity') }}">+</button>
            </div>
            <button class="btn btn-primary btn-main" data-add-btn onclick="addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }})" {{ $inStock ? '' : 'disabled' }}>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              {{ __('Add to cart') }}
            </button>
          </div>
          <div class="pd-buy-actions">
            <a href="{{ route('shop.checkout') }}" class="btn btn-dark" onclick="event.preventDefault(); openCart()">{{ __('View cart') }}</a>
            <a href="{{ route('shop.index') }}" class="btn btn-ghost">{{ __('Continue shopping') }}</a>
          </div>
        </div>
      </div>
    </div>

    @if($relatedProducts->count() > 0)
    <section class="section" style="padding:56px 0 0;">
      <div class="section-head reveal">
        <div>
          <span class="eyebrow">{{ __('More from') }} {{ $product->category->name ?? '' }}</span>
          <h2>{{ __('You may also like') }}</h2>
        </div>
      </div>
      <div class="rel-grid">
        @foreach($relatedProducts as $rp)
          @php
            $rpPrimary = $rp->images->firstWhere('is_primary', true);
            $rpImg = $resolveImageUrl($rpPrimary?->image_path) ?? $resolveImageUrl($rp->image) ?? $imageToShow;
          @endphp
          <div class="p-card reveal" data-id="{{ $rp->id }}">
            <div class="p-media" onclick="window.location.href='{{ route('shop.product', $rp) }}'">
              <img src="{{ $rpImg }}" alt="{{ $rp->name }}" loading="lazy">
              <button class="p-fav" aria-label="{{ __('Save to wishlist') }}" onclick="event.stopPropagation(); toggleFav(this)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
              </button>
              <button class="quick-add" data-add-btn aria-label="{{ __('Add to cart') }}" onclick="event.stopPropagation(); addToCart('{{ $rp->id }}', '{{ addslashes($rp->name) }}', {{ $rp->selling_price }})">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              </button>
            </div>
            <div class="p-body">
              <span class="p-cat">{{ $rp->category->name ?? 'Uncategorized' }}</span>
              <span class="p-name" onclick="window.location.href='{{ route('shop.product', $rp) }}'">{{ $rp->name }}</span>
              <div class="p-price-row">
                <span class="p-price" data-price="{{ $rp->selling_price }}">TZS {{ number_format($rp->selling_price, 0) }}</span>
              </div>
              <div class="p-actions">
                <button class="btn btn-dark btn-sm" onclick="addToCart('{{ $rp->id }}', '{{ addslashes($rp->name) }}', {{ $rp->selling_price }})">{{ __('Add to cart') }}</button>
                <a href="{{ route('shop.product', $rp) }}" class="btn btn-outline btn-sm">{{ __('Details') }}</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </section>
    @endif
  </div>
</main>

@include('shop.partials.footer')
@include('shop.partials.cart-drawer', ['showBottomBar' => true])
@include('shop.partials.cart-js')

<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="{{ $product->name }}" onclick="if(event.target===this)closeLightbox()">
  <button class="lb-close" onclick="closeLightbox()" aria-label="{{ __('Close') }}">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M18 6 6 18M6 6l12 12"/></svg>
  </button>
  <button class="lb-arrow prev" onclick="changeLightbox(-1)" aria-label="{{ __('Previous') }}">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 18-6-6 6-6"/></svg>
  </button>
  <img id="lightboxImg" src="{{ $imageToShow }}" alt="{{ $product->name }}">
  <button class="lb-arrow next" onclick="changeLightbox(1)" aria-label="{{ __('Next') }}">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 18 6-6-6-6"/></svg>
  </button>
  <span class="lb-counter" id="lbCounter"></span>
</div>

<!-- Sticky mobile buy bar -->
<div class="pd-sticky" id="productStickyBar">
  <div class="psb-left">
    <b id="psbPrice">TZS {{ number_format($product->selling_price, 0) }}</b>
    <span>{{ $inStock ? ($lowStock ? __('Low stock') : __('In stock')) : __('Out of stock') }}</span>
  </div>
  <div class="qty-stepper">
    <button onclick="changeProductQty(-1)" aria-label="{{ __('Decrease quantity') }}">−</button>
    <span id="productQtySticky">1</span>
    <button onclick="changeProductQty(1)" aria-label="{{ __('Increase quantity') }}">+</button>
  </div>
  <button class="btn btn-primary" data-add-btn onclick="addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }})" {{ $inStock ? '' : 'disabled' }}>
    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
    {{ __('Add') }}
  </button>
</div>

<script>
var productQty = 1;
var productMaxQty = {{ $product->quantity ?? 999 }};
var lbImages = @json($allImages);
var lbIndex = 0;

function changeProductQty(delta) {
  productQty += delta;
  productQty = Math.max(1, productQty);
  if (productMaxQty > 0) productQty = Math.min(productQty, productMaxQty);
  var a = document.getElementById('productQty');
  var b = document.getElementById('productQtySticky');
  if (a) a.textContent = productQty;
  if (b) b.textContent = productQty;
}

function addToCart(id, name, price) {
  const meta = productMetaFromDOM(id);
  if (name === 'Item' && meta) name = meta.name;
  if (!price && meta) price = meta.price;
  const qtyToAdd = productQty || 1;
  const existing = cart.find(i => String(i.id) === String(id));
  if (existing) {
    existing.quantity += qtyToAdd;
    existing.name = name;
    existing.price = Number(price) || existing.price || 0;
  } else {
    cart.push({ id: String(id), name, price: Number(price) || 0, quantity: qtyToAdd });
  }
  const entry = cart.find(i => String(i.id) === String(id));
  if (entry && productMaxQty > 0) entry.quantity = Math.min(entry.quantity, productMaxQty);
  saveCart();
  updateCartUI();
  animateCartButton(id);
  showToast(qtyToAdd + 'x ' + name + ' {{ __('added to cart') }}', 'cart');
}

function updateBottomBar() {
  var shared = document.getElementById('mobileCartBar');
  if (shared) shared.classList.remove('visible');
}

function changeImage(src, btn, idx) {
  document.getElementById('mainImage').src = src;
  document.querySelectorAll('.pd-thumb').forEach(function(b){ b.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  if (typeof idx === 'number') lbIndex = idx;
}

function openLightbox(idx) {
  if (!lbImages.length) return;
  lbIndex = typeof idx === 'number' ? idx : lbIndex;
  lbIndex = (lbIndex + lbImages.length) % lbImages.length;
  updateLightbox();
  document.getElementById('lightbox').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function changeLightbox(dir) { openLightbox(lbIndex + dir); }
function closeLightbox() {
  document.getElementById('lightbox').classList.remove('open');
  document.body.style.overflow = '';
}
function updateLightbox() {
  document.getElementById('lightboxImg').src = lbImages[lbIndex];
  document.getElementById('lbCounter').textContent = (lbIndex + 1) + ' / ' + lbImages.length;
}
document.addEventListener('keydown', function(e) {
  if (!document.getElementById('lightbox').classList.contains('open')) return;
  if (e.key === 'Escape') closeLightbox();
  if (e.key === 'ArrowLeft') changeLightbox(-1);
  if (e.key === 'ArrowRight') changeLightbox(1);
});

document.addEventListener('DOMContentLoaded', function() {
  initCart();
  setTimeout(hidePageLoader, 300);
});
window.addEventListener('load', hidePageLoader);
</script>
</body>
</html>
