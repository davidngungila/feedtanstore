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
  $selectedCategory = request('category')
      ? $categories->firstWhere(function($cat) {
          return $cat->id == request('category') || $cat->slug == request('category');
        })
      : null;
  $searchTerm = trim((string) request('search', ''));
  $shopName = 'Feedtan Store';
  $seoTitle = $selectedCategory ? $selectedCategory->name . ' - ' . $shopName : ($searchTerm ? 'Search Results for "' . $searchTerm . '" - ' . $shopName : $shopName);
  $seoDescription = $selectedCategory ? 'Shop ' . $selectedCategory->name . ' at ' . $shopName : ($searchTerm ? 'Find products matching "' . $searchTerm . '" at ' . $shopName : 'Welcome to ' . $shopName . ' - Your trusted store in Moshi, Kilimanjaro');
  $seo = [
      'title' => $seoTitle,
      'description' => $seoDescription,
      'keywords' => 'feedtan store, shop, products, online store, moshi, kilimanjaro, tanzania',
      'image' => $logoUrl
  ];

  $canonicalUrl = request()->fullUrl();
  $pageType = $selectedCategory || $searchTerm !== '' ? 'website' : 'store';
  $structuredData = [
      '@context' => 'https://schema.org',
      '@graph' => [
          [
              '@type' => 'Organization',
              '@id' => url('/#organization'),
              'name' => 'Feedtan Store',
              'url' => url('/'),
              'logo' => ['@type' => 'ImageObject', 'url' => $logoUrl],
              'image' => [$logoUrl],
              'telephone' => '+255717358865',
              'email' => 'info@feedtanstore.com',
              'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Kiboriloni', 'addressLocality' => 'Moshi', 'addressRegion' => 'Kilimanjaro', 'addressCountry' => 'TZ'],
          ],
          [
              '@type' => 'WebSite',
              '@id' => url('/#website'),
              'url' => url('/'),
              'name' => 'Feedtan Store',
              'publisher' => ['@id' => url('/#organization')],
              'potentialAction' => ['@type' => 'SearchAction', 'target' => route('shop.index') . '?search={search_term_string}', 'query-input' => 'required name=search_term_string'],
          ],
          [
              '@type' => 'CollectionPage',
              '@id' => $canonicalUrl . '#webpage',
              'url' => $canonicalUrl,
              'name' => $seo['title'],
              'description' => $seo['description'],
              'isPartOf' => ['@id' => url('/#website')],
              'about' => ['@id' => url('/#organization')],
              'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => $logoUrl],
          ],
      ],
  ];

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
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
<meta name="keywords" content="{{ $seo['keywords'] }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta name="author" content="Feedtan Store">
<meta name="theme-color" content="#1B4332">
<link rel="canonical" href="{{ $canonicalUrl }}">
<link rel="icon" type="image/png" href="{{ $logoUrl }}">
<link rel="apple-touch-icon" href="{{ $logoUrl }}">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="Feedtan Store">
<meta property="og:type" content="{{ $pageType }}">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $seo['image'] }}">
<meta property="og:image:secure_url" content="{{ $seo['image'] }}">
<meta property="og:image:type" content="image/png">
<meta property="og:image:alt" content="Feedtan Store logo">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo['title'] }}">
<meta name="twitter:description" content="{{ $seo['description'] }}">
<meta name="twitter:image" content="{{ $seo['image'] }}">
<meta name="twitter:image:alt" content="Feedtan Store logo">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;0,9..144,700;0,9..144,900;1,9..144,500&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@include('shop.partials.styles')
<style>
/* ---------- Hero ---------- */
.hero-wrap{margin:0 auto;max-width:var(--maxw);padding:0 20px;}
.hero-shell{
  position:relative;overflow:hidden;border-radius:var(--radius-xl);
  background:linear-gradient(135deg,var(--green-700) 0%,var(--green-900) 100%);
  color:#fff;margin-top:22px;box-shadow:var(--shadow-lift);
}
.hero-shell::before{
  content:'';position:absolute;inset:0;
  background:
    radial-gradient(circle at 12% 20%, rgba(232,137,58,.4), transparent 40%),
    radial-gradient(circle at 90% 80%, rgba(255,255,255,.12), transparent 45%);
  pointer-events:none;
}
.hero-grain{position:absolute;inset:0;opacity:.06;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");pointer-events:none;}
.hero-inner{position:relative;z-index:2;display:grid;grid-template-columns:1.1fr .9fr;gap:36px;align-items:center;padding:56px 44px 50px;}
.hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.28);padding:6px 14px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#FFE3C2;margin-bottom:16px;}
.hero-inner h1{font-size:clamp(30px,4.6vw,54px);line-height:1.05;font-weight:700;}
.hero-inner h1 em{font-style:italic;color:#FFCB94;font-weight:500;}
.hero-inner p.lead{font-size:16.5px;color:#DCEAE1;max-width:470px;margin:16px 0 26px;line-height:1.65;}
.hero-cta{display:flex;gap:12px;flex-wrap:wrap;}
.hero-stats{display:flex;gap:26px;margin-top:32px;flex-wrap:wrap;}
.hero-stats div b{display:block;font-family:var(--font-display);font-size:25px;font-weight:700;}
.hero-stats div span{font-size:12px;color:#BFD6C8;}
.hero-visual{position:relative;display:flex;justify-content:center;align-items:center;}
.produce-card{
  position:relative;background:var(--parchment);border-radius:var(--radius-l);padding:20px;width:100%;max-width:360px;
  box-shadow:var(--shadow-pop);transform:rotate(-1.5deg);
}
.produce-card img{border-radius:var(--radius-m);aspect-ratio:4/3;object-fit:cover;width:100%;}
.produce-card .tag{
  position:absolute;top:-13px;right:16px;background:var(--orange);color:#fff;font-weight:800;font-size:12.5px;
  padding:7px 15px;border-radius:999px;box-shadow:0 8px 18px rgba(0,0,0,.22);transform:rotate(4deg);
}
.produce-card .info{display:flex;justify-content:space-between;align-items:center;margin-top:13px;}
.produce-card .info b{font-family:var(--font-display);font-size:17px;color:var(--ink);}
.produce-card .info span{font-size:12px;color:var(--ink-soft);}
.float-chip{position:absolute;background:#fff;border-radius:var(--radius-m);padding:11px 15px;box-shadow:var(--shadow-card);display:flex;align-items:center;gap:9px;font-size:12.5px;font-weight:700;color:var(--green-900);}
.chip-1{top:6%;left:-6%;animation:float1 6s ease-in-out infinite;}
.chip-2{bottom:4%;right:-8%;animation:float2 7s ease-in-out infinite;}
@keyframes float1{0%,100%{transform:translateY(0)}50%{transform:translateY(-9px)}}
@keyframes float2{0%,100%{transform:translateY(0)}50%{transform:translateY(9px)}}

/* Carousel */
.hero-carousel{position:relative;}
.hero-slide{display:none;animation:fadeSlide .6s var(--ease);}
.hero-slide.active{display:block;}
.hero-slide .hero-inner{grid-template-columns:1.05fr .95fr;}
@keyframes fadeSlide{from{opacity:0;transform:translateX(24px)}to{opacity:1;transform:none}}
.hero-dots{position:absolute;bottom:18px;left:50%;transform:translateX(-50%);display:flex;gap:8px;z-index:5;}
.hero-dots button{width:9px;height:9px;border-radius:999px;border:none;background:rgba(255,255,255,.4);transition:all .25s;padding:0;}
.hero-dots button.active{width:26px;background:var(--orange);}
.hero-arrow{position:absolute;top:50%;transform:translateY(-50%);z-index:5;width:42px;height:42px;border-radius:50%;border:none;background:rgba(255,255,255,.16);color:#fff;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);transition:background .15s;}
.hero-arrow:hover{background:rgba(255,255,255,.3);}
.hero-arrow.prev{left:14px;}
.hero-arrow.next{right:14px;}

@media(max-width:900px){
  .hero-inner{grid-template-columns:1fr;padding:38px 24px 44px;}
  .hero-visual{order:-1;margin-bottom:6px;}
  .produce-card{max-width:280px;}
  .hero-arrow{display:none;}
}

/* ---------- Category chips ---------- */
.cat-row{display:flex;gap:10px;overflow-x:auto;padding:4px 2px 10px;margin-bottom:30px;scrollbar-width:none;scroll-snap-type:x proximity;}
.cat-row::-webkit-scrollbar{display:none;}
.cat-chip{
  flex-shrink:0;display:flex;align-items:center;gap:9px;background:#fff;border:1.5px solid var(--line);
  border-radius:999px;padding:9px 16px 9px 9px;font-size:13.5px;font-weight:700;color:var(--ink);
  transition:all .18s ease;scroll-snap-align:start;
}
.cat-chip .ic{width:30px;height:30px;border-radius:50%;background:var(--green-100);display:flex;align-items:center;justify-content:center;font-size:15px;transition:background .18s;}
.cat-chip:hover,.cat-chip.active{background:var(--green-700);color:#fff;border-color:var(--green-700);box-shadow:0 8px 18px rgba(27,67,50,.25);transform:translateY(-1px);}
.cat-chip.active .ic,.cat-chip:hover .ic{background:rgba(255,255,255,.2);}
@media(min-width:901px){.cat-row{flex-wrap:wrap;justify-content:center;}}

/* ---------- Product grid ---------- */
.product-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:20px;}
  @media(max-width:1080px){.product-grid{grid-template-columns:repeat(3,minmax(0,1fr));}}
  @media(max-width:760px){.product-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;}}

.p-card{
  background:#fff;border-radius:var(--radius-m);overflow:hidden;box-shadow:var(--shadow-card);
  display:flex;flex-direction:column;transition:transform .2s ease, box-shadow .2s ease;border:1px solid rgba(219,212,194,.5);
}
.p-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-lift);}
.p-media{position:relative;aspect-ratio:1/0.9;overflow:hidden;background:var(--parchment-dim);cursor:pointer;}
.p-media img{width:100%;height:100%;object-fit:cover;transition:transform .45s ease;}
.p-card:hover .p-media img{transform:scale(1.07);}
.p-badge{position:absolute;top:10px;left:10px;}
.p-fav{
  position:absolute;top:8px;right:8px;width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.95);
  display:flex;align-items:center;justify-content:center;border:none;color:var(--ink-soft);
  transition:transform .18s, color .18s;box-shadow:0 2px 8px rgba(15,42,31,.12);
}
.p-fav:hover{color:var(--red);transform:scale(1.12);}
.p-fav.active{color:var(--red);}
.p-fav.active svg{animation:heartPop .35s var(--ease);}
@keyframes heartPop{0%{transform:scale(1)}40%{transform:scale(1.4)}100%{transform:scale(1)}}
.quick-add{
  position:absolute;right:10px;bottom:10px;width:38px;height:38px;border-radius:50%;border:none;
  background:var(--green-700);color:#fff;display:flex;align-items:center;justify-content:center;
  box-shadow:0 6px 16px rgba(15,42,31,.35);transition:transform .18s, background .18s;
}
.quick-add:hover{transform:scale(1.12);background:var(--green-900);}
.quick-add.added{background:var(--success);}
.quick-add.added svg{animation:heartPop .4s var(--ease);}
@media(min-width:901px){
  .quick-add{opacity:0;transform:translateY(6px);}
  .p-card:hover .quick-add{opacity:1;transform:translateY(0);}
  .p-card:hover .quick-add:hover{transform:translateY(0) scale(1.12);}
}
.p-body{padding:13px 13px 15px;display:flex;flex-direction:column;gap:5px;flex:1;}
.p-cat{font-size:10.5px;font-weight:700;color:var(--green-700);text-transform:uppercase;letter-spacing:.05em;}
.p-name{font-size:14px;font-weight:700;color:var(--ink);cursor:pointer;line-height:1.32;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.p-name:hover{color:var(--green-700);}
.p-rating{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--ink-soft);}
.p-rating svg{color:var(--orange);}
.p-price-row{display:flex;align-items:baseline;gap:7px;margin-top:2px;flex-wrap:wrap;}
.p-price{font-family:var(--font-display);font-weight:700;font-size:18px;color:var(--ink);}
.p-price-old{font-size:12.5px;color:#A39E8C;text-decoration:line-through;}
.p-unit{font-size:11px;color:var(--ink-soft);}
.p-actions{display:flex;gap:8px;margin-top:auto;padding-top:10px;}
.p-actions .btn{flex:1;padding:10px 12px;font-size:13px;min-height:40px;}
@media(max-width:600px){
  .p-body{padding:11px;}
  .p-name{font-size:13.5px;}
  .p-price{font-size:16.5px;}
  .p-actions{flex-direction:column;}
  .p-actions .btn{width:100%;}
}

/* ---------- Trust strip ---------- */
.trust-strip{background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line);margin-top:40px;}
.trust-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;padding:28px 20px;max-width:var(--maxw);margin:0 auto;}
.trust-item{display:flex;gap:12px;align-items:flex-start;}
.trust-item .ic{width:42px;height:42px;border-radius:12px;background:var(--green-100);color:var(--green-700);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.trust-item b{display:block;font-size:14px;}
.trust-item span{font-size:12.5px;color:var(--ink-soft);}
@media(max-width:760px){.trust-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:600px){.trust-grid{grid-template-columns:1fr 1fr;padding:22px 14px;} .trust-item{gap:10px;}}

/* ---------- Pagination ---------- */
.pagination{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:36px;flex-wrap:wrap;}
.pg-btn{
  min-width:42px;height:42px;padding:0 10px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;
  background:#fff;border:1.5px solid var(--line);font-weight:700;font-size:14px;color:var(--ink);transition:all .15s;
}
.pg-btn:hover{border-color:var(--green-700);color:var(--green-700);transform:translateY(-1px);}
.pg-current{background:var(--green-700);border-color:var(--green-700);color:#fff;}
.pg-disabled{opacity:.4;pointer-events:none;}
.pg-ellipsis{color:var(--ink-soft);padding:0 4px;}
</style>
</head>
<body>

@include('shop.partials.header', ['activeNav' => 'home'])

<main id="mainContent">

  @if($slides->count() > 0)
  <div class="hero-wrap">
    <section class="hero-shell hero-carousel" id="heroCarousel" aria-roledescription="carousel" aria-label="Promotions">
      <div class="hero-grain"></div>
      <button class="hero-arrow prev" onclick="heroNav(-1)" aria-label="Previous">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 18-6-6 6-6"/></svg>
      </button>
      @foreach($slides as $i => $slide)
        @php
          $slideImg = $resolveImageUrl($slide->image);
        @endphp
        <div class="hero-slide {{ $loop->first ? 'active' : '' }}" style="background:linear-gradient(135deg,{{ $slide->gradient_color ?? '#1B4332' }} 0%,{{ $slide->background_color ?? '#0F2A1F' }} 100%);">
          <div class="hero-inner">
            <div>
              <span class="hero-eyebrow">⭐ Feedtan Store</span>
              <h1>{!! $slide->title !!}</h1>
              @if($slide->subtitle)<p class="lead">{{ $slide->subtitle }}</p>@endif
              <div class="hero-cta">
                @if($slide->button_url)
                  <a href="{{ $slide->button_url }}" class="btn btn-primary btn-lg">{{ $slide->button_text ?? __('Start shopping') }}</a>
                @endif
                <a href="{{ route('shop.tracking') }}" class="btn btn-outline btn-lg" style="border-color:rgba(255,255,255,.5);color:#fff;">{{ __('Track my order') }}</a>
              </div>
            </div>
            @if($slideImg)
            <div class="hero-visual">
              <div class="produce-card" style="transform:rotate(1.5deg);">
                <img src="{{ $slideImg }}" alt="{{ strip_tags($slide->title) }}" loading="lazy">
                <div class="info"><b>Feedtan Store</b><span>{{ $settings->store_address ?? __('Location') }}</span></div>
              </div>
            </div>
            @endif
          </div>
        </div>
      @endforeach
      <button class="hero-arrow next" onclick="heroNav(1)" aria-label="Next">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 18 6-6-6-6"/></svg>
      </button>
      <div class="hero-dots" id="heroDots">
        @foreach($slides as $i => $slide)
          <button class="{{ $loop->first ? 'active' : '' }}" onclick="heroGo({{ $i }})" aria-label="Slide {{ $loop->iteration }}"></button>
        @endforeach
      </div>
    </section>
  </div>
  @else
  <div class="hero-wrap">
    <section class="hero-shell">
      <div class="hero-grain"></div>
      <div class="hero-inner">
        <div>
          <span class="hero-eyebrow">⭐ Feedtan Store · Moshi</span>
          <h1>{{ __('Everyday essentials,') }} <em>{{ __('delivered to your door.') }}</em></h1>
          <p class="lead">{{ __('Quality products, unbeatable prices — ready for pickup or delivered across Moshi, Kilimanjaro.') }}</p>
          <div class="hero-cta">
            <a href="#shop" class="btn btn-primary btn-lg">{{ __('Start shopping') }}</a>
            <a href="{{ route('shop.tracking') }}" class="btn btn-outline btn-lg" style="border-color:rgba(255,255,255,.5);color:#fff;">{{ __('Track my order') }}</a>
          </div>
          <div class="hero-stats">
            <div><b>1,000+</b><span>{{ __('Products in stock') }}</span></div>
            <div><b>TZS 50k</b><span>{{ __('Free delivery') }}</span></div>
            <div><b>7 Days</b><span>{{ __('Store open weekly') }}</span></div>
          </div>
        </div>
        <div class="hero-visual">
          <div class="produce-card">
            <span class="tag">NEW</span>
            <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&q=80" alt="{{ __('Fresh produce') }}" loading="lazy">
            <div class="info"><b>{{ __('Fresh, every day') }}</b><span>{{ __('Kiboriloni, Moshi') }}</span></div>
          </div>
          <div class="float-chip chip-1">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green-700)" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            {{ __('Quality checked') }}
          </div>
          <div class="float-chip chip-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.58 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            {{ __('Call to order') }}
          </div>
        </div>
      </div>
    </section>
  </div>
  @endif

  <section class="section" id="shop" style="padding-top:40px;">
    <div class="wrap">
      <div class="section-head section-head-centered reveal">
        <div>
          @if($selectedCategory)
            <span class="eyebrow">{{ $selectedCategory->name }}</span>
            <h2>{{ $selectedCategory->name }}</h2>
            <p>{{ $products->total() }} {{ __('products') }}</p>
          @elseif($searchTerm !== '')
            <span class="eyebrow">{{ __('Search') }}</span>
            <h2>{{ __('Search Results for') }} "{{ $searchTerm }}"</h2>
            <p>{{ $products->total() }} {{ __('products') }}</p>
          @else
            <span class="eyebrow">{{ __('Shop') }}</span>
            <h2>{{ __('Buy by Category') }}</h2>
          @endif
        </div>
      </div>

      <div class="cat-row reveal" id="catRow">
        <a href="{{ route('shop.index') }}" class="cat-chip {{ !request('category') ? 'active' : '' }}">
          <span class="ic">🛒</span> {{ __('All') }}
        </a>
        @foreach($categories->take(5) as $cat)
          <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="cat-chip {{ request('category') == $cat->id || request('category') == $cat->slug ? 'active' : '' }}">
            <span class="ic">📦</span> {{ $cat->name }}
          </a>
        @endforeach
      </div>

      @if($products->count() > 0)
      <div class="product-grid" id="productGrid">
        @foreach($products as $product)
          @php
            $primaryImage = $product->images->firstWhere('is_primary', true);
            $imageToShow = $resolveImageUrl($primaryImage?->image_path) ?? $resolveImageUrl($product->image) ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=500&q=80';
            $oldPrice = $product->old_price ?? null;
            $badge = $oldPrice ? '-'.round((($oldPrice - $product->selling_price)/$oldPrice)*100).'%' : null;
          @endphp
          <div class="p-card reveal" data-id="{{ $product->id }}">
            <div class="p-media" onclick="window.location.href='{{ route('shop.product', $product) }}'">
              <img src="{{ $imageToShow }}" alt="{{ $product->name }}" loading="lazy">
              @if($badge)
                <span class="p-badge pill pill-orange">{{ $badge }}</span>
              @endif
              <button class="p-fav" aria-label="Save to wishlist" onclick="event.stopPropagation(); toggleFav(this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
              </button>
              <button class="quick-add" data-add-btn aria-label="{{ __('Add to cart') }}" onclick="event.stopPropagation(); addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }})">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              </button>
            </div>
            <div class="p-body">
              <span class="p-cat">{{ $product->category->name ?? 'Uncategorized' }}</span>
              <span class="p-name" onclick="window.location.href='{{ route('shop.product', $product) }}'">{{ $product->name }}</span>
              <div class="p-rating">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8l-6.2 3.2 1.2-6.8-5-4.9 6.9-1z"/></svg>
                4.5 ({{ rand(10, 500) }})
              </div>
              <div class="p-price-row">
                <span class="p-price" data-price="{{ $product->selling_price }}">TZS {{ number_format($product->selling_price, 0) }}</span>
                @if($oldPrice)
                  <span class="p-price-old">TZS {{ number_format($oldPrice, 0) }}</span>
                @endif
              </div>
              <span class="p-unit">{{ __('per item') }}</span>
              <div class="p-actions" id="actions-{{ $product->id }}">
                <button class="btn btn-dark btn-sm" onclick="addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }})">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                  {{ __('Add to cart') }}
                </button>
                <a href="{{ route('shop.product', $product) }}" class="btn btn-outline btn-sm">{{ __('Details') }}</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div style="margin-top:8px;">
        {{ $products->links('shop.partials.pagination') }}
      </div>
      @else
      <div class="card" style="text-align:center;padding:60px 24px;">
        <div style="font-size:46px;margin-bottom:12px;">🔍</div>
        <h3 style="font-size:20px;margin-bottom:8px;">{{ __('No products found') }}</h3>
        <p style="color:var(--ink-soft);font-size:14px;margin-bottom:20px;">{{ __('Try a different search term or browse all products.') }}</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary">{{ __('Browse all products') }}</a>
      </div>
      @endif
    </div>
  </section>

  <section class="trust-strip reveal">
    <div class="trust-grid">
      <div class="trust-item">
        <div class="ic">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.58 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <div><b>{{ __('Call to order') }}</b><span>+255 717 358 865</span></div>
      </div>
      <div class="trust-item">
        <div class="ic">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/></svg>
        </div>
        <div><b>{{ __('Fast delivery') }}</b><span>TZS 3,000 or free over 50k</span></div>
      </div>
      <div class="trust-item">
        <div class="ic">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v9h14v-9"/><path d="M9 19v-6h6v6"/></svg>
        </div>
        <div><b>{{ __('Store pickup') }}</b><span>Kiboriloni, Moshi</span></div>
      </div>
      <div class="trust-item">
        <div class="ic">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8l-6.2 3.2 1.2-6.8-5-4.9 6.9-1z"/></svg>
        </div>
        <div><b>{{ __('Quality products') }}</b><span>Checked before delivery</span></div>
      </div>
    </div>
  </section>
</main>

@include('shop.partials.footer')
@include('shop.partials.cart-drawer', ['showBottomBar' => true])
@include('shop.partials.cart-js')

<script>
var heroIndex = 0;
var heroSlides = document.querySelectorAll('#heroCarousel .hero-slide');
var heroTimer = null;

function heroGo(i) {
  if (!heroSlides.length) return;
  heroIndex = (i + heroSlides.length) % heroSlides.length;
  heroSlides.forEach(function(s, idx) { s.classList.toggle('active', idx === heroIndex); });
  var dots = document.querySelectorAll('#heroDots button');
  dots.forEach(function(d, idx) { d.classList.toggle('active', idx === heroIndex); });
  restartHero();
}
function heroNav(dir) { heroGo(heroIndex + dir); }
function restartHero() {
  clearInterval(heroTimer);
  if (heroSlides.length > 1) {
    heroTimer = setInterval(function(){ heroGo(heroIndex + 1); }, 6000);
  }
}
if (heroSlides.length > 1) {
  var heroEl = document.getElementById('heroCarousel');
  var touchX = null;
  heroEl.addEventListener('touchstart', function(e){ touchX = e.changedTouches[0].clientX; }, { passive: true });
  heroEl.addEventListener('touchend', function(e){
    if (touchX === null) return;
    var dx = e.changedTouches[0].clientX - touchX;
    if (Math.abs(dx) > 45) heroNav(dx < 0 ? 1 : -1);
    touchX = null;
  }, { passive: true });
  restartHero();
}

document.addEventListener('DOMContentLoaded', function() {
  initCart();
  setTimeout(hidePageLoader, 300);
});
window.addEventListener('load', hidePageLoader);
</script>
</body>
</html>
