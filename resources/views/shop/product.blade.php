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
  $productCanonicalUrl = route('shop.product', $product->slug ?: $product->encrypted_key);
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
  $savePercent = $oldPrice ? round((($oldPrice - $product->selling_price) / $oldPrice) * 100) : 0;

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
              'sku' => $product->sku ?: $product->slug,
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
      ? \App\Models\Product::visibleInPublicShop()
          ->where('category_id', $product->category_id)
          ->where('id', '!=', $product->id)
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
<meta name="theme-color" content="#123328">
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
<script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@include('shop.partials.styles')
<style>
/* ---------- Breadcrumb ---------- */
.crumbs{display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:12.5px;color:var(--ink-faint);margin-bottom:22px;font-family:var(--font-mono);}
.crumbs a{color:var(--ink-soft);}
.crumbs a:hover{color:var(--orange-600);}
.crumbs .sep{opacity:.6;}

/* ---------- Sticky buy bar ---------- */
.pd-sticky .psb-left{min-width:0;}
.pd-sticky .psb-left b{display:block;font-family:var(--font-mono);font-weight:700;font-size:15px;color:var(--green-800);}
.pd-sticky .psb-left span{display:block;font-size:11.5px;color:var(--ink-faint);white-space:nowrap;}

/* ---------- Responsive ---------- */
@media (max-width:1024px){
  .pd-grid{gap:24px;}
  .pd-main{height:340px;}
}
@media (max-width:768px){
  .crumbs{margin-bottom:16px;font-size:11.5px;}
  .trust-bullets{grid-template-columns:1fr 1fr;}
}
@media (max-width:560px){
  .pd-card{padding:18px;}
  .pd-main{height:250px;}
  .pd-sticky{gap:8px;padding:8px 12px calc(8px + env(safe-area-inset-bottom));}
  .pd-sticky .psb-left span{display:none;}
  .pd-sticky .btn{flex:1;}
}
@media (max-width:400px){
  .pd-sticky .qty-stepper{display:none;}
}
</style>
</head>
<body class="with-pd-sticky">

@include('shop.partials.header', ['activeNav' => 'shop'])

<main id="mainContent" class="section" style="padding-top:28px;">
  <div class="wrap">

    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="{{ route('shop.index') }}">{{ __('Home') }}</a>
      <span class="sep">/</span>
      @if($product->category)
        <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
        <span class="sep">/</span>
      @endif
      <span>{{ $product->name }}</span>
    </nav>

    <div class="pd-grid">
      <!-- ===== GALLERY ===== -->
      <div class="pd-gallery">
        <div class="pd-main" onclick="openLightbox(lbIndex)" role="button" tabindex="0" aria-label="{{ __('View larger image') }}">
          <img id="mainImage" src="{{ $imageToShow }}" alt="{{ $product->name }}">
          @if($savePercent > 0)
            <span class="p-badge pill pill-orange" style="top:14px;left:14px;">-{{ $savePercent }}%</span>
          @endif
          <button class="p-fav" style="top:14px;right:14px;width:38px;height:38px;" aria-label="{{ __('Save to wishlist') }}" onclick="event.stopPropagation(); toggleFav(this)">
            <i class="fa-regular fa-heart"></i>
          </button>
        </div>

        @if(count($allImages) > 1)
          <div class="pd-thumbs" id="pdThumbs">
            @foreach($allImages as $i => $imgSrc)
              <button class="pd-thumb {{ $loop->first ? 'active' : '' }}" onclick="changeImage('{{ $imgSrc }}', this, {{ $i }})" aria-label="{{ __('Image') }} {{ $loop->iteration }}">
                <img src="{{ $imgSrc }}" alt="{{ $product->name }} {{ $loop->iteration }}" loading="lazy">
              </button>
            @endforeach
          </div>
        @endif
      </div>

      <!-- ===== BUY BOX ===== -->
      <div class="pd-card">
        <div class="pd-info">
          <span class="pd-cat">
            {{ $product->category->name ?? __('Uncategorized') }}
            @if($product->brand)<span class="dot"></span>{{ $product->brand->name }}@endif
          </span>

          <h1 class="pd-title p-name">{{ $product->name }}</h1>

          <div class="pd-price-row">
            <span class="p-price" data-price="{{ $product->selling_price }}">TZS {{ number_format($product->selling_price, 0) }}</span>
            @if($oldPrice)
              <span class="p-price-old">TZS {{ number_format($oldPrice, 0) }}</span>
              <span class="pd-save">{{ __('Save') }} {{ $savePercent }}%</span>
            @endif
          </div>

          @if($inStock)
            <span class="stock-pill {{ $lowStock ? 'low' : 'in' }}" style="align-self:flex-start;">
              <span class="led"></span>
              {{ $lowStock ? __('Low stock — only a few left') : __('In stock — ready to send') }}
            </span>
          @else
            <span class="stock-pill out" style="align-self:flex-start;">
              <span class="led"></span> {{ __('Currently out of stock') }}
            </span>
          @endif

          @if($product->description)
            <p class="pd-desc">{{ $product->description }}</p>
          @endif

          <ul class="pd-meta-list">
            <li>
              <i class="fa-solid fa-store" style="color:var(--orange-600);"></i>
              <span>{{ __('Store pickup available') }} — {{ $settings->store_address ?? __('Kiboriloni, Moshi') }}</span>
            </li>
            <li>
              <i class="fa-solid fa-barcode" style="color:var(--orange-600);"></i>
              <span>{{ __('SKU') }}: <b class="mono">{{ $product->sku ?: $product->slug }}</b></span>
            </li>
            <li>
              <i class="fa-solid fa-shield-halved" style="color:var(--orange-600);"></i>
              <span>{{ __('Quality checked before it leaves the store') }}</span>
            </li>
          </ul>

          <div class="pd-buy">
            <div style="display:flex;gap:12px;align-items:stretch;flex-wrap:wrap;">
              <div class="qty-stepper">
                <button onclick="changeProductQty(-1)" aria-label="{{ __('Decrease quantity') }}">&minus;</button>
                <span id="productQty">1</span>
                <button onclick="changeProductQty(1)" aria-label="{{ __('Increase quantity') }}">+</button>
              </div>
              <button class="btn btn-primary btn-lg" style="flex:1;min-width:180px;" data-add-btn
                      onclick="addToCart('{{ $product->encrypted_key }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }})"
                      {{ $inStock ? '' : 'disabled' }}>
                <i class="fa-solid fa-cart-plus"></i> {{ __('Add to cart') }}
              </button>
            </div>
            <a href="{{ route('shop.checkout') }}" class="btn btn-ghost btn-block" onclick="openCart();return false;">
              <i class="fa-solid fa-cart-shopping"></i> {{ __('View cart') }}
            </a>
            <a href="{{ route('shop.index') }}#shop" class="see-all" style="align-self:center;">← {{ __('Continue shopping') }}</a>
          </div>

          <div class="trust-bullets">
            <div class="trust-bullet"><i class="fa-solid fa-circle-check"></i> {{ __('Secure payment') }}</div>
            <div class="trust-bullet"><i class="fa-solid fa-circle-check"></i> {{ __('Fast handling') }}</div>
            <div class="trust-bullet"><i class="fa-solid fa-circle-check"></i> {{ __('Real-time tracking') }}</div>
            <div class="trust-bullet"><i class="fa-solid fa-circle-check"></i> {{ __('Support 24/7') }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== RELATED ===== -->
    @if($relatedProducts->count() > 0)
    <section style="padding:56px 0 0;">
      <div class="sec-head">
        <span class="eyebrow">{{ __('More to explore') }}</span>
        <h2>{{ __('You may also like') }}</h2>
      </div>
      <div class="rel-grid">
        @foreach($relatedProducts as $rp)
          @php
            $rpPrimary = $rp->images->firstWhere('is_primary', true);
            $rpImage = $resolveImageUrl($rpPrimary?->image_path) ?? $resolveImageUrl($rp->image) ?? $imageToShow;
            $rpOld = $rp->old_price ?? null;
            $rpBadge = $rpOld ? '-'.round((($rpOld - $rp->selling_price)/$rpOld)*100).'%' : null;
          @endphp
          <div class="p-card reveal" data-key="{{ $rp->encrypted_key }}">
            <div class="p-media" onclick="window.location.href='{{ route('shop.product', $rp->encrypted_key) }}'" role="button" tabindex="0" aria-label="{{ $rp->name }}">
              <img src="{{ $rpImage }}" alt="{{ $rp->name }}" loading="lazy">
              @if($rpBadge)<span class="p-badge pill pill-orange">{{ $rpBadge }}</span>@endif
              <button class="p-fav" aria-label="{{ __('Save to wishlist') }}" onclick="event.stopPropagation(); toggleFav(this)">
                <i class="fa-regular fa-heart"></i>
              </button>
              <button class="quick-add" data-add-btn aria-label="{{ __('Add to cart') }}" onclick="event.stopPropagation(); addToCart('{{ $rp->encrypted_key }}', '{{ addslashes($rp->name) }}', {{ $rp->selling_price }})">
                <i class="fa-solid fa-cart-plus"></i>
              </button>
            </div>
            <div class="p-body">
              <span class="p-cat">{{ $rp->category->name ?? __('Uncategorized') }}</span>
              <a class="p-name" href="{{ route('shop.product', $rp->encrypted_key) }}">{{ $rp->name }}</a>
              <div class="p-price-row">
                <span class="p-price" data-price="{{ $rp->selling_price }}">TZS {{ number_format($rp->selling_price, 0) }}</span>
                @if($rpOld)<span class="p-price-old">TZS {{ number_format($rpOld, 0) }}</span>@endif
              </div>
              <div class="p-actions">
                <button class="btn btn-primary btn-sm" onclick="addToCart('{{ $rp->encrypted_key }}', '{{ addslashes($rp->name) }}', {{ $rp->selling_price }})">{{ __('Add') }}</button>
                <a href="{{ route('shop.product', $rp->encrypted_key) }}" class="btn btn-outline btn-sm">{{ __('Details') }}</a>
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
    <button onclick="changeProductQty(-1)" aria-label="{{ __('Decrease quantity') }}">&minus;</button>
    <span id="productQtySticky">1</span>
    <button onclick="changeProductQty(1)" aria-label="{{ __('Increase quantity') }}">+</button>
  </div>
  <button class="btn btn-primary" style="flex:1;max-width:220px;" data-add-btn onclick="addToCart('{{ $product->encrypted_key }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }})" {{ $inStock ? '' : 'disabled' }}>
    <i class="fa-solid fa-cart-plus"></i> {{ __('Add') }}
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

function addToCart(key, name, price) {
  const meta = productMetaFromDOM(key);
  if (name === 'Item' && meta) name = meta.name;
  if (!price && meta) price = meta.price;
  const qtyToAdd = productQty || 1;
  const existing = cart.find(i => String(i.key) === String(key));
  if (existing) {
    existing.quantity += qtyToAdd;
    existing.name = name;
    existing.price = Number(price) || existing.price || 0;
  } else {
    cart.push({ key: String(key), name, price: Number(price) || 0, quantity: qtyToAdd });
  }
  const entry = cart.find(i => String(i.key) === String(key));
  if (entry && productMaxQty > 0) entry.quantity = Math.min(entry.quantity, productMaxQty);
  saveCart();
  updateCartUI();
  animateCartButton(key);
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
