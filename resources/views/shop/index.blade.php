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
  $seo = seo_shop_index($selectedCategory, $searchTerm);

  // Encrypted page tokens change on every render, so keep them out of the
  // canonical URL to avoid duplicate-content signals.
  $canonicalQuery = request()->except('page');
  $canonicalUrl = request()->url() . ($canonicalQuery ? '?' . http_build_query($canonicalQuery) : '');
  $pageType = 'website';
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

  $shopListItems = [];
  foreach ($products as $position => $listedProduct) {
    $listedPrimary = $listedProduct->images->firstWhere('is_primary', true);
    $listedImage = $resolveImageUrl($listedPrimary?->image_path) ?? $resolveImageUrl($listedProduct->image) ?? $logoUrl;
    $shopListItems[] = [
        '@type' => 'ListItem',
        'position' => $position + 1,
        'item' => [
            '@type' => 'Product',
            'name' => $listedProduct->name,
            'url' => route('shop.product', $listedProduct->slug ?: $listedProduct->encrypted_key),
            'image' => [$listedImage],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'TZS',
                'price' => number_format((float) $listedProduct->selling_price, 0, '.', ''),
                'availability' => 'https://schema.org/InStock',
            ],
        ],
    ];
  }
  $structuredData['@graph'][] = [
      '@type' => 'ItemList',
      'name' => $seo['title'],
      'url' => $canonicalUrl,
      'numberOfItems' => count($shopListItems),
      'itemListElement' => $shopListItems,
  ];
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
<meta name="keywords" content="{{ $seo['keywords'] }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta name="author" content="Feedtan Store">
<meta name="theme-color" content="#123328">
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
<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@include('shop.partials.styles')
<style>
/* ---------- DEALS MARQUEE ---------- */
.deals-band{background:var(--orange-600);color:#fff;overflow:hidden;}
.deals-track{
  display:flex;gap:44px;padding:9px 0;white-space:nowrap;
  width:max-content;animation:ftDeals 26s linear infinite;
}
.deals-track span{
  display:inline-flex;align-items:center;gap:10px;
  font-family:var(--font-mono);font-size:12.5px;font-weight:600;letter-spacing:.03em;
}
.deals-track span::before{content:"\25C6";font-size:9px;color:#ffd9ae;}
@keyframes ftDeals{from{transform:translateX(0);}to{transform:translateX(-50%);}}
@media (prefers-reduced-motion:reduce){.deals-track{animation:none;}}

/* ---------- HERO ---------- */
.hero{position:relative;overflow:hidden;padding:56px 0 0;}
.hero-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:40px;align-items:center;}
.hero h1{
  font-size:clamp(34px,5vw,58px);line-height:1.04;margin:14px 0 16px;letter-spacing:-.01em;
}
.hero h1 em{color:var(--orange-600);font-style:italic;}
.hero-cta{display:flex;gap:12px;flex-wrap:wrap;margin:26px 0 22px;}
.trust-row{display:flex;gap:22px;flex-wrap:wrap;}
.trust-row .ti{display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:var(--green-800);}
.hero-art{position:relative;height:400px;}
.blob{
  position:absolute;inset:0;margin:auto;width:360px;height:360px;
  border-radius:44% 56% 62% 38% / 45% 40% 60% 55%;
  background:linear-gradient(155deg,var(--green-100),var(--green-050));
  animation:ftBlob 9s ease-in-out infinite;
}
@keyframes ftBlob{
  0%,100%{border-radius:44% 56% 62% 38% / 45% 40% 60% 55%;}
  50%{border-radius:58% 42% 40% 60% / 55% 60% 40% 45%;}
}
.float-icon{
  position:absolute;filter:drop-shadow(0 10px 16px rgba(18,51,40,.18));
  animation:ftFloat 5s ease-in-out infinite;
}
@keyframes ftFloat{0%,100%{transform:translateY(0);}50%{transform:translateY(-13px);}}
.hero-chip{
  position:absolute;background:var(--paper);border:1px solid var(--line);
  border-radius:14px;padding:12px 15px;box-shadow:var(--shadow-lift);
  font-size:12.5px;font-weight:700;color:var(--green-900);
  display:flex;align-items:center;gap:10px;
}
.hero-chip span{display:block;font-weight:500;font-size:11px;color:var(--ink-faint);}

/* hero carousel (admin slides) */
.hero-carousel{
  position:relative;overflow:hidden;border-radius:var(--radius-xl);
  background:var(--green-900);box-shadow:var(--shadow-lift);
}
.hero-slide{
  position:absolute;inset:0;opacity:0;visibility:hidden;
  transition:opacity .6s var(--ease),visibility .6s var(--ease);
  display:flex;align-items:center;
}
.hero-slide.active{position:relative;opacity:1;visibility:visible;}
.hero-slide-inner{
  display:grid;grid-template-columns:1.1fr .9fr;gap:30px;align-items:center;
  padding:44px 40px;color:#fff;width:100%;
}
.hero-slide h2{
  color:#fff;font-size:clamp(26px,3.6vw,44px);line-height:1.08;margin:12px 0 12px;
}
.hero-slide h2 :is(p,span,div){margin:0;}
.hero-slide .hero-eyebrow{color:var(--orange-400);}
.hero-slide .lead{color:#cfe0d7;font-size:16px;margin-bottom:22px;}
.hero-slide .hero-art{height:280px;}
.hero-slide-img{
  width:100%;height:260px;object-fit:cover;border-radius:20px;
  border:1px solid rgba(255,255,255,.18);
}
.hero-arrow{
  position:absolute;top:50%;transform:translateY(-50%);z-index:3;
  width:42px;height:42px;border-radius:50%;border:1px solid rgba(255,255,255,.28);
  background:rgba(255,255,255,.12);color:#fff;
  display:flex;align-items:center;justify-content:center;
  transition:background .18s var(--ease);
}
.hero-arrow:hover{background:rgba(255,255,255,.25);}
.hero-arrow.prev{left:14px;} .hero-arrow.next{right:14px;}
.hero-dots{position:absolute;left:0;right:0;bottom:16px;display:flex;justify-content:center;gap:8px;z-index:3;}
.hero-dots button{
  width:9px;height:9px;padding:0;border-radius:999px;border:none;
  background:rgba(255,255,255,.4);transition:.2s var(--ease);
}
.hero-dots button.active{width:26px;background:var(--orange-500);}

/* ---------- USP BAND ---------- */
.usp-band{background:var(--green-900);color:#fff;padding:24px 0;margin-top:52px;}
.usp-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;}
.usp-item{display:flex;align-items:center;gap:12px;}
.usp-item .ic{
  width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,.1);
  display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:17px;
}
.usp-item b{display:block;font-size:14.5px;color:#fff;}
.usp-item span{display:block;font-size:12px;color:#c7d6cd;}

/* ---------- CATEGORY CARDS ---------- */
.cat-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:16px;}
.cat-card{
  background:var(--green-050);border:1px solid transparent;border-radius:16px;
  padding:22px 14px;text-align:center;transition:.2s var(--ease);
  display:flex;flex-direction:column;align-items:center;gap:12px;
}
.cat-card:nth-child(even){background:var(--orange-100);}
.cat-card:hover{transform:translateY(-4px);border-color:var(--green-500);box-shadow:var(--shadow-lift);}
.cat-card .ic{margin:0 auto;line-height:0;}
.cat-card b{font-size:13px;color:var(--green-900);display:block;line-height:1.35;}
.cat-card .cnt{font-family:var(--font-mono);font-size:11px;color:var(--ink-faint);}

/* ---------- HOW IT WORKS ---------- */
.how-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0;position:relative;}
.how-grid::before{
  content:"";position:absolute;top:38px;left:12%;right:12%;height:2px;
  background-image:linear-gradient(to right,var(--green-500) 60%,transparent 0);
  background-size:14px 2px;background-repeat:repeat-x;
}
.how-step{position:relative;text-align:center;padding:0 20px;}
.how-ic{
  width:76px;height:76px;border-radius:50%;background:#fff;border:2px solid var(--green-500);
  display:flex;align-items:center;justify-content:center;margin:0 auto 18px;
  position:relative;z-index:2;font-size:26px;color:var(--green-700);
}
.how-step h3{font-size:19px;margin-bottom:8px;}
.how-step p{font-size:14px;color:var(--ink-soft);max-width:240px;margin:0 auto;}

/* ---------- PAYMENTS ---------- */
.pay-strip{
  background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);
  padding:26px;display:flex;flex-wrap:wrap;gap:12px;justify-content:center;
}
.pay-chip{
  display:inline-flex;align-items:center;gap:9px;padding:10px 16px;border-radius:12px;
  background:var(--green-050);border:1px solid var(--line);
  font-weight:700;font-size:13.5px;color:var(--green-900);
}
.pay-chip .dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}

/* ---------- TRACKING SECTION ---------- */
.track-sec{background:var(--green-900);color:#fff;position:relative;overflow:hidden;}
.track-grid{display:grid;grid-template-columns:1fr 1fr;gap:50px;align-items:center;}
.track-sec .eyebrow{color:var(--orange-400);}
.track-sec h2{color:#fff;}
.track-sec p{color:#c7d6cd;font-size:15.5px;margin:12px 0 22px;max-width:430px;}
.track-input-row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;}
.track-input-row input{
  flex:1;min-width:180px;padding:13px 16px;border-radius:12px;
  border:1.5px solid rgba(255,255,255,.25);background:rgba(255,255,255,.08);
  color:#fff;font-family:var(--font-mono);font-size:14px;
}
.track-input-row input::placeholder{color:#8fa89b;}
.track-input-row input:focus{outline:none;border-color:var(--orange-500);}
.rider-card{
  background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.18);
  border-radius:16px;padding:14px 16px;display:flex;align-items:center;gap:14px;max-width:420px;
}
.rider-avatar{
  width:44px;height:44px;border-radius:50%;background:var(--orange-500);flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-family:var(--font-display);font-weight:700;color:#fff;
}
.rider-info b{display:block;font-size:13.5px;color:#fff;}
.rider-info span{font-size:11.5px;color:#a9c2b6;}
.rider-call{
  margin-left:auto;width:36px;height:36px;border-radius:50%;background:var(--orange-600);
  display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;
}

/* receipt card */
.receipt-wrap{display:flex;justify-content:center;}
.receipt{
  width:318px;max-width:100%;background:var(--paper);color:var(--ink);
  font-family:var(--font-mono);font-size:12.5px;
  box-shadow:0 30px 60px -20px rgba(0,0,0,.55);
}
.receipt-zig{
  height:13px;width:100%;
  background:linear-gradient(-45deg,var(--paper) 8px,transparent 0),linear-gradient(45deg,var(--paper) 8px,var(--green-900) 0);
  background-position:left bottom;background-repeat:repeat-x;background-size:16px 16px;
}
.receipt-zig.bot{transform:rotate(180deg);}
.receipt-inner{padding:20px;}
.rc-head{text-align:center;margin-bottom:12px;}
.rc-head b{font-size:15px;letter-spacing:.05em;}
.rc-head span{display:block;font-size:10.5px;color:var(--ink-faint);margin-top:3px;}
.rc-divider{border:none;border-top:1px dashed var(--line);margin:12px 0;}
.rc-row{display:flex;justify-content:space-between;gap:10px;margin:5px 0;font-size:12px;}
.rc-row.total{font-weight:700;font-size:13px;}
.rc-status-list{margin-top:14px;display:flex;flex-direction:column;gap:12px;}
.rc-status{display:flex;align-items:flex-start;gap:10px;opacity:.45;}
.rc-status.done{opacity:1;}
.rc-status .chk{
  width:18px;height:18px;border-radius:50%;border:2px solid var(--green-600);flex-shrink:0;
  display:flex;align-items:center;justify-content:center;margin-top:1px;color:#fff;font-size:9px;
}
.rc-status.done .chk{background:var(--green-600);}
.rc-status b{font-size:12px;display:block;}
.rc-status span{font-size:10.5px;color:var(--ink-faint);}

/* ---------- ZONES ---------- */
.zone-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;}
.zone-card{
  background:var(--green-050);border-radius:14px;padding:18px;
  display:flex;flex-direction:column;gap:6px;
}
.zone-card b{font-size:14px;color:var(--green-900);}
.zone-card .zt{font-family:var(--font-mono);font-size:11.5px;font-weight:700;color:var(--green-600);}
.zone-note{
  display:flex;align-items:center;gap:10px;margin-top:20px;padding:14px 18px;
  background:var(--orange-100);border-radius:12px;font-size:13.5px;color:var(--ink-soft);
}

/* ---------- FAQ ---------- */
.faq-wrap{max-width:760px;margin:0 auto;}
.faq-item{border-bottom:1px solid var(--line);}
.faq-q{
  width:100%;background:none;border:none;text-align:left;padding:20px 4px;
  display:flex;justify-content:space-between;align-items:center;gap:16px;
  font-family:var(--font-display);font-size:17px;font-weight:600;color:var(--green-900);
}
.faq-q .plus{
  width:26px;height:26px;border-radius:50%;border:1.5px solid var(--green-600);flex-shrink:0;
  display:flex;align-items:center;justify-content:center;transition:transform .25s var(--ease),background .25s var(--ease),color .25s var(--ease);
}
.faq-item.open .faq-q .plus{transform:rotate(45deg);background:var(--green-600);color:#fff;}
.faq-a{max-height:0;overflow:hidden;transition:max-height .3s var(--ease);}
.faq-a p{padding:0 4px 20px;color:var(--ink-soft);font-size:14.5px;max-width:620px;}

/* ---------- NEWSLETTER ---------- */
.news-band{
  background:var(--green-100);border-radius:var(--radius-xl);padding:38px;
  display:flex;align-items:center;justify-content:space-between;gap:28px;flex-wrap:wrap;
}
.news-band h3{font-size:24px;margin-bottom:8px;}
.news-band p{color:var(--ink-soft);font-size:14.5px;max-width:400px;}
.news-form{display:flex;gap:10px;flex-wrap:wrap;}
.news-form input{
  padding:13px 18px;border-radius:999px;border:1.5px solid var(--line);
  min-width:220px;font-family:var(--font-body);font-size:14px;background:var(--white);
}
.news-form input:focus{outline:none;border-color:var(--green-500);}

/* ---------- TRUST BADGES ---------- */
.trust-band{
  display:flex;justify-content:space-around;flex-wrap:wrap;gap:22px;
  background:var(--paper);border:1px solid var(--line);
  border-radius:var(--radius);padding:24px;
}
.trust-badge{display:flex;align-items:center;gap:10px;font-size:13px;font-weight:700;color:var(--green-900);}
.trust-badge .ic{
  width:38px;height:38px;border-radius:10px;background:var(--green-050);
  display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:15px;color:var(--green-700);
}

/* ---------- RESPONSIVE ---------- */
@media (max-width:1024px){
  .cat-grid{grid-template-columns:repeat(4,1fr);}
  .usp-grid{grid-template-columns:repeat(2,1fr);}
  .hero-slide-inner{grid-template-columns:1fr;padding:34px 30px;}
  .hero-slide .hero-art{display:none;}
  .hero-arrow{display:none;}
}
@media (max-width:900px){
  .hero{padding:34px 0 0;}
  .hero-grid{grid-template-columns:1fr;}
  .hero-art{height:270px;margin-top:10px;}
  .blob{width:250px;height:250px;}
  .how-grid{grid-template-columns:1fr;gap:34px;}
  .how-grid::before{display:none;}
  .track-grid{grid-template-columns:1fr;gap:34px;}
  .zone-grid{grid-template-columns:repeat(2,1fr);}
  .news-band{flex-direction:column;text-align:center;justify-content:center;}
  .news-form{justify-content:center;width:100%;}
}
@media (max-width:768px){
  .usp-grid{grid-template-columns:1fr 1fr;}
  .cat-grid{grid-template-columns:repeat(3,1fr);}
  .news-band{padding:26px 20px;}
  .news-band h3{font-size:21px;}
  .news-form input{min-width:0;flex:1;}
}
@media (max-width:560px){
  .cat-grid{grid-template-columns:repeat(2,1fr);gap:12px;}
  .zone-grid{grid-template-columns:1fr;}
  .usp-grid{grid-template-columns:1fr;}
  .hero h1{font-size:clamp(28px,8vw,36px);}
  .hero-cta .btn{flex:1 1 100%;}
  .trust-row{gap:12px;}
  .receipt{width:100%;}
  .deals-track{gap:28px;}
  .deals-track span{font-size:11.5px;}
  .pay-strip{padding:18px;gap:8px;}
  .pay-chip{font-size:12.5px;padding:9px 13px;}
  .faq-q{font-size:15.5px;padding:16px 2px;}
  .hero-chip{font-size:11.5px;padding:10px 12px;}
}
@media (max-width:420px){
  .cat-grid{grid-template-columns:repeat(2,1fr);}
  .trust-row{flex-direction:column;align-items:flex-start;gap:10px;}
  .hero-art{height:220px;}
}
</style>
</head>
<body>

@include('shop.partials.header', ['activeNav' => 'home'])

@php
  $marqueeItems = [
    __('New Arrivals') . ' ' . __('Every Day'),
    __('24 Hours Delivery') . ' ' . __('Within Moshi'),
    __('Bundle Offers') . ' ' . __('Save More'),
    __('Fresh Products') . ' ' . __('Quality Guaranteed'),
    __('Fast Checkout') . ' ' . __('Pay on Delivery'),
    __('Free Delivery') . ' ' . __('On Orders Over TZS 50,000'),
  ];
@endphp
<div class="deals-band" aria-hidden="true">
  <div class="deals-track">
    @for($r = 0; $r < 2; $r++)
      @foreach($marqueeItems as $item)
        <span>{{ $item }}</span>
      @endforeach
    @endfor
  </div>
</div>

<main id="mainContent">

  <!-- ================= HERO ================= -->
  @if($slides->count() > 0)
  <section class="hero">
    <div class="wrap">
      <div class="hero-carousel" id="heroCarousel" aria-roledescription="carousel" aria-label="{{ __('Promotions') }}">
        <button class="hero-arrow prev" onclick="heroNav(-1)" aria-label="{{ __('Previous') }}">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        @foreach($slides as $slide)
          @php $slideImg = $resolveImageUrl($slide->image); @endphp
          <div class="hero-slide {{ $loop->first ? 'active' : '' }}" style="background:linear-gradient(135deg,{{ $slide->gradient_color ?? '#1f5c43' }} 0%,{{ $slide->background_color ?? '#123328' }} 100%);">
            <div class="hero-slide-inner">
              <div>
                <span class="hero-eyebrow">Feedtan Store</span>
                <h2>{!! $slide->title !!}</h2>
                @if($slide->subtitle)<p class="lead">{{ $slide->subtitle }}</p>@endif
                <div class="hero-cta">
                  @if($slide->button_url)
                    <a href="{{ $slide->button_url }}" class="btn btn-primary btn-lg">{{ $slide->button_text ?? __('Start shopping') }}</a>
                  @else
                    <a href="{{ route('shop.index') }}#shop" class="btn btn-primary btn-lg">{{ __('Start shopping') }}</a>
                  @endif
                  <a href="{{ route('shop.tracking') }}" class="btn btn-ghost-white btn-lg">{{ __('Track my order') }}</a>
                </div>
              </div>
              @if($slideImg)
                <div class="hero-art">
                  <img class="hero-slide-img" src="{{ $slideImg }}" alt="{{ strip_tags($slide->title) }}" loading="lazy">
                </div>
              @endif
            </div>
          </div>
        @endforeach
        <button class="hero-arrow next" onclick="heroNav(1)" aria-label="{{ __('Next') }}">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 18 6-6-6-6"/></svg>
        </button>
        <div class="hero-dots" id="heroDots">
          @foreach($slides as $i => $slide)
            <button class="{{ $loop->first ? 'active' : '' }}" onclick="heroGo({{ $i }})" aria-label="{{ __('Slide') }} {{ $loop->iteration }}"></button>
          @endforeach
        </div>
      </div>
    </div>
  </section>
  @else
  <section class="hero">
    <div class="wrap hero-grid">
      <div>
        <span class="eyebrow">Feedtan Store · {{ __('Online shopping') }}</span>
        <h1>{{ __('Everyday essentials,') }} <em>{{ __('delivered to your door.') }}</em></h1>
        <p class="lead">{{ __('Shop everyday essentials at honest prices — order online and collect in store or get it delivered across Moshi, Kilimanjaro.') }}</p>
        <div class="hero-cta">
          <a href="#shop" class="btn btn-primary btn-lg">{{ __('Start shopping') }}</a>
          <a href="{{ route('shop.tracking') }}" class="btn btn-outline btn-lg">{{ __('Track my order') }}</a>
        </div>
        <div class="trust-row">
          <span class="ti"><i class="fa-solid fa-circle-check" style="color:var(--green-600);"></i> {{ __('Quality checked') }}</span>
          <span class="ti"><i class="fa-solid fa-circle-check" style="color:var(--green-600);"></i> {{ __('Honest prices') }}</span>
          <span class="ti"><i class="fa-solid fa-circle-check" style="color:var(--green-600);"></i> {{ __('Fast delivery') }}</span>
        </div>
      </div>
      <div class="hero-art" aria-hidden="true">
        <div class="blob"></div>
        <svg class="float-icon" style="top:36px;left:52px;animation-delay:.2s;" width="58" height="58" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="13" r="7" fill="#e8720c"/><path d="M12 6c1.6 0 2-2 4-2" stroke="#1f5c43" stroke-width="2" stroke-linecap="round"/></svg>
        <svg class="float-icon" style="top:190px;left:6px;animation-delay:1.4s;" width="52" height="52" viewBox="0 0 24 24" fill="none"><rect x="7" y="4" width="10" height="16" rx="2" fill="#fff" stroke="#1f5c43" stroke-width="1.6"/><rect x="7" y="4" width="10" height="5" rx="2" fill="#26714f"/></svg>
        <svg class="float-icon" style="bottom:30px;right:52px;animation-delay:.8s;" width="66" height="66" viewBox="0 0 24 24" fill="none"><ellipse cx="12" cy="14" rx="6" ry="4" fill="#f2954a"/><path d="M7 12c1-4 4-8 7-9" stroke="#e8720c" stroke-width="2" stroke-linecap="round"/></svg>
        <svg class="float-icon" style="top:110px;right:0;animation-delay:2s;" width="54" height="54" viewBox="0 0 24 24" fill="none"><rect x="5" y="8" width="14" height="10" rx="2" fill="#fff" stroke="#e8720c" stroke-width="1.6"/><rect x="5" y="6" width="14" height="4" rx="1.5" fill="#e8720c"/></svg>
        <div class="hero-chip" style="top:0;right:20px;">
          <i class="fa-solid fa-basket-shopping" style="color:var(--orange-600);"></i>
          <div>{{ __('Fresh every day') }}<span>{{ $settings->store_address ?? 'Kiboriloni, Moshi' }}</span></div>
        </div>
        <div class="hero-chip" style="bottom:0;left:0;">
          <i class="fa-solid fa-truck-fast" style="color:var(--green-600);"></i>
          <div>{{ __('Home Delivery') }}<span>{{ __('Calculate at checkout') }}</span></div>
        </div>
      </div>
    </div>
  </section>
  @endif

  <!-- ================= USP ================= -->
  <div class="usp-band">
    <div class="wrap usp-grid">
      <div class="usp-item">
        <div class="ic"><i class="fa-solid fa-cart-shopping"></i></div>
        <div><b>{{ __('ORDER') }}</b><span>{{ __('Easy online shopping') }}</span></div>
      </div>
      <div class="usp-item">
        <div class="ic"><i class="fa-solid fa-truck-fast"></i></div>
        <div><b>{{ __('WE DELIVER') }}</b><span>{{ __('Fast and safe') }}</span></div>
      </div>
      <div class="usp-item">
        <div class="ic"><i class="fa-solid fa-location-dot"></i></div>
        <div><b>{{ __('YOU PAY') }}</b><span>{{ __('Where we deliver') }}</span></div>
      </div>
      <div class="usp-item">
        <div class="ic"><i class="fa-solid fa-credit-card"></i></div>
        <div><b>{{ __('PAY ONLINE') }}</b><span>{{ __('Mobile money or card') }}</span></div>
      </div>
    </div>
  </div>

  <!-- ================= CATEGORIES ================= -->
  @if($categories->count() > 0)
  <section id="categories">
    <div class="wrap">
      <div class="sec-head reveal" style="max-width:620px;margin-bottom:32px;">
        <span class="eyebrow">{{ __('Shop by') }}</span>
        <h2>{{ __('Product categories') }}</h2>
        <p>{{ __('Everything for your home and kitchen, organised so shopping is quick.') }}</p>
      </div>
      <div class="cat-grid">
        @foreach($categories->take(12) as $cat)
          @php
            $catName = mb_strtolower($cat->name);
            $catIcon = 'box';
            if (\Illuminate\Support\Str::contains($catName, ['mboga','matunda','vegetable','fruit','nyanya','machungwa','ndizi'])) $catIcon = 'produce';
            elseif (\Illuminate\Support\Str::contains($catName, ['vinywaji','drink','soda','juice','maji','water'])) $catIcon = 'bottle';
            elseif (\Illuminate\Support\Str::contains($catName, ['maziwa','mayai','milk','egg','mtindi'])) $catIcon = 'milk';
            elseif (\Illuminate\Support\Str::contains($catName, ['nafaka','unga','mchele','rice','sugar','sukari'])) $catIcon = 'grain';
            elseif (\Illuminate\Support\Str::contains($catName, ['nyumbani','home','mafuta','oil','mkate','bread'])) $catIcon = 'pantry';
          @endphp
          <a class="cat-card reveal" href="{{ route('shop.index', ['category' => $cat->slug]) }}">
            <span class="ic">
              @if($catIcon === 'produce')
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><ellipse cx="12" cy="14" rx="7" ry="5" fill="#e8720c"/><path d="M7 11c1-4 4-8 7-9" stroke="#1f5c43" stroke-width="2" stroke-linecap="round"/></svg>
              @elseif($catIcon === 'bottle')
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><rect x="7" y="4" width="10" height="16" rx="2" fill="#fff" stroke="#1f5c43" stroke-width="1.6"/><rect x="7" y="4" width="10" height="5" rx="2" fill="#26714f"/></svg>
              @elseif($catIcon === 'milk')
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><rect x="6" y="9" width="12" height="10" rx="2" fill="#fff" stroke="#e8720c" stroke-width="1.6"/><rect x="6" y="7" width="12" height="4" rx="1.5" fill="#e8720c"/></svg>
              @elseif($catIcon === 'grain')
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="M5 18c0-6 3-11 7-11s7 5 7 11z" fill="#f2954a"/><path d="M5 18h14" stroke="#1f5c43" stroke-width="1.6" stroke-linecap="round"/></svg>
              @elseif($catIcon === 'pantry')
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><rect x="5" y="5" width="14" height="14" rx="3" stroke="#1f5c43" stroke-width="1.8"/><path d="M9 9h6M9 13h6" stroke="#26714f" stroke-width="1.6" stroke-linecap="round"/></svg>
              @else
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="7" fill="#e8720c"/><path d="M9 12h6M12 9v6" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
              @endif
            </span>
            <b>{{ $cat->name }}</b>
          </a>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <!-- ================= PRODUCTS ================= -->
  <section id="shop">
    <div class="wrap">
      <div class="prod-toolbar" style="display:flex;justify-content:space-between;align-items:flex-end;gap:14px;flex-wrap:wrap;margin-bottom:24px;">
        <div>
          <span class="eyebrow">{{ __('Our store') }}</span>
          <h2 style="font-size:clamp(24px,3.2vw,34px);margin-top:8px;">
            @if($selectedCategory)
              {{ $selectedCategory->name }}
            @elseif($searchTerm !== '')
              {{ __('Search results for') }} "{{ $searchTerm }}"
            @else
              {{ __('Popular products') }}
            @endif
          </h2>
          <p class="muted" style="margin-top:8px;font-size:14.5px;">{{ $products->total() }} {{ __('products available') }}</p>
        </div>
        <a href="{{ route('shop.index') }}" class="see-all">{{ __('Clear filters') }} →</a>
      </div>

      <div class="cat-row">
        <a href="{{ route('shop.index') }}" class="cat-chip {{ !request('category') ? 'active' : '' }}">
          <span class="ic"><i class="fa-solid fa-grip"></i></span> {{ __('All') }}
        </a>
        @foreach($categories as $cat)
          <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="cat-chip {{ (request('category') == $cat->id || request('category') == $cat->slug) ? 'active' : '' }}">
            {{ $cat->name }}
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
              $stockLevel = $product->quantity <= 5 ? 'low' : 'in';
            @endphp
            <div class="p-card reveal" data-key="{{ $product->encrypted_key }}">
              <div class="p-media" onclick="window.location.href='{{ route('shop.product', $product->encrypted_key) }}'" role="button" tabindex="0" aria-label="{{ $product->name }}">
                <img src="{{ $imageToShow }}" alt="{{ $product->name }}" loading="lazy">
                @if($badge)
                  <span class="p-badge pill pill-orange">{{ $badge }}</span>
                @endif
                <button class="p-fav" aria-label="{{ __('Save to wishlist') }}" onclick="event.stopPropagation(); toggleFav(this)">
                  <i class="fa-regular fa-heart"></i>
                </button>
                <button class="quick-add" data-add-btn aria-label="{{ __('Add to cart') }}" onclick="event.stopPropagation(); addToCart('{{ $product->encrypted_key }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }})">
                  <i class="fa-solid fa-cart-plus"></i>
                </button>
              </div>
              <div class="p-body">
                <span class="p-cat">{{ $product->category->name ?? __('Uncategorized') }}</span>
                <a class="p-name" href="{{ route('shop.product', $product->encrypted_key) }}">{{ $product->name }}</a>
                <div class="p-price-row">
                  <span class="p-price" data-price="{{ $product->selling_price }}">TZS {{ number_format($product->selling_price, 0) }}</span>
                  @if($oldPrice)
                    <span class="p-price-old">TZS {{ number_format($oldPrice, 0) }}</span>
                  @endif
                </div>
                <span class="p-stock {{ $stockLevel }}">
                  <span class="led"></span>
                  {{ $stockLevel === 'low' ? __('Only a few left') : __('In stock') }}
                </span>
                <div class="p-actions">
                  <button class="btn btn-primary btn-sm" onclick="addToCart('{{ $product->encrypted_key }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }})">
                    <i class="fa-solid fa-cart-plus"></i> {{ __('Add') }}
                  </button>
                  <a href="{{ route('shop.product', $product->encrypted_key) }}" class="btn btn-outline btn-sm">{{ __('Details') }}</a>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div style="margin-top:8px;">
          {{ $products->links('shop.partials.pagination') }}
        </div>
      @else
        <div class="card empty-state">
          <div class="es-ic"><i class="fa-solid fa-magnifying-glass"></i></div>
          <h3>{{ __('No products found') }}</h3>
          <p>{{ __('Try a different search term or browse all products.') }}</p>
          <a href="{{ route('shop.index') }}" class="btn btn-primary">{{ __('Browse all products') }}</a>
        </div>
      @endif
    </div>
  </section>

  <!-- ================= TRUST ================= -->
  <section style="padding-top:0;">
    <div class="wrap">
      <div class="trust-band reveal">
        <div class="trust-badge">
          <div class="ic"><i class="fa-solid fa-shield-halved"></i></div>
          {{ __('Secure payments') }}
        </div>
        <div class="trust-badge">
          <div class="ic"><i class="fa-solid fa-circle-check"></i></div>
          {{ __('Quality checked goods') }}
        </div>
        <div class="trust-badge">
          <div class="ic"><i class="fa-solid fa-headset"></i></div>
          {{ __('Support on WhatsApp') }}
        </div>
        <div class="trust-badge">
          <div class="ic"><i class="fa-solid fa-store"></i></div>
          {{ __('Store pickup available') }}
        </div>
      </div>
    </div>
  </section>

  <!-- ================= HOW IT WORKS ================= -->
  <section id="how">
    <div class="wrap">
      <div class="sec-head reveal" style="margin:0 auto 48px;text-align:center;">
        <span class="eyebrow">{{ __('Simple process') }}</span>
        <h2>{{ __('How it works') }}</h2>
      </div>
      <div class="how-grid">
        <div class="how-step reveal">
          <div class="how-ic"><i class="fa-solid fa-cart-shopping"></i></div>
          <h3>{{ __('Order') }}</h3>
          <p>{{ __('Add the things you need to your cart — it only takes a moment.') }}</p>
        </div>
        <div class="how-step reveal">
          <div class="how-ic"><i class="fa-solid fa-truck-fast"></i></div>
          <h3>{{ __('We deliver') }}</h3>
          <p>{{ __('Our team prepares and delivers your order quickly and safely.') }}</p>
        </div>
        <div class="how-step reveal">
          <div class="how-ic"><i class="fa-solid fa-location-dot"></i></div>
          <h3>{{ __('Pay on arrival') }}</h3>
          <p>{{ __('Pay online or with cash when your order reaches you.') }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= PAYMENTS ================= -->
  <section style="padding-top:0;">
    <div class="wrap">
      <div class="sec-head reveal" style="text-align:center;margin:0 auto 26px;">
        <span class="eyebrow">{{ __('Payment options') }}</span>
        <h2>{{ __('Pay the way that suits you') }}</h2>
      </div>
      <div class="pay-strip">
        <div class="pay-chip"><span class="dot" style="background:#1f9c3d;"></span>M-Pesa</div>
        <div class="pay-chip"><span class="dot" style="background:#e8720c;"></span>Airtel Money</div>
        <div class="pay-chip"><span class="dot" style="background:#1c3fa0;"></span>Mixx by Yas</div>
        <div class="pay-chip"><span class="dot" style="background:#2e7d5b;"></span>HaloPesa</div>
        <div class="pay-chip"><span class="dot" style="background:#1a1f71;"></span>Visa</div>
        <div class="pay-chip"><span class="dot" style="background:#eb001b;"></span>Mastercard</div>
        <div class="pay-chip"><span class="dot" style="background:var(--ink-faint);"></span>{{ __('Cash on delivery') }}</div>
      </div>
    </div>
  </section>

  <!-- ================= DELIVERY ZONES ================= -->
  <section style="padding-top:0;">
    <div class="wrap">
      <div class="sec-head reveal">
        <span class="eyebrow">{{ __('Delivery areas') }}</span>
        <h2>{{ __('Where we deliver') }}</h2>
        <p>{{ __('Delivery is available every day. Fees are confirmed at checkout based on your location.') }}</p>
      </div>
      <div class="zone-grid">
        <div class="zone-card"><b>{{ __('Moshi Town') }}</b><span class="zt">{{ __('Fastest delivery') }}</span></div>
        <div class="zone-card"><b>{{ __('Moshi Outskirts') }}</b><span class="zt">{{ __('Same day') }}</span></div>
        <div class="zone-card"><b>{{ __('Kilimanjaro') }}</b><span class="zt">{{ __('Next day') }}</span></div>
        <div class="zone-card"><b>{{ __('Store pickup') }}</b><span class="zt">{{ __('No delivery fee') }}</span></div>
      </div>
      <div class="zone-note">
        <i class="fa-solid fa-location-dot" style="color:var(--orange-600);flex-shrink:0;"></i>
        {{ __('Outside these areas? Message us on WhatsApp') }} +255 717 358 865 — {{ __('we will check if we can deliver.') }}
      </div>
    </div>
  </section>

  <!-- ================= TRACKING ================= -->
  <section id="tracking" class="track-sec" style="padding:64px 0;">
    <div class="wrap track-grid">
      <div>
        <span class="eyebrow">{{ __('Track order') }}</span>
        <h2 style="margin-top:8px;">{{ __('Follow your order step by step') }}</h2>
        <p>{{ __('Enter your order number to see exactly where your order is — from our shelf to your door.') }}</p>
        <form class="track-input-row" action="{{ route('shop.tracking') }}" method="GET">
          <input type="text" name="order" placeholder="{{ __('Order number') }}" aria-label="{{ __('Order number') }}" required>
          <button type="submit" class="btn btn-primary">{{ __('Check status') }}</button>
        </form>
        <div class="rider-card">
          <div class="rider-avatar">FT</div>
          <div class="rider-info">
            <b>{{ __('Need help with an order?') }}</b>
            <span>{{ __('Our team replies on WhatsApp and phone') }}</span>
          </div>
          <a href="https://wa.me/255717358865" class="rider-call" aria-label="WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
          </a>
        </div>
      </div>
      <div class="receipt-wrap">
        <div class="receipt">
          <div class="receipt-zig"></div>
          <div class="receipt-inner">
            <div class="rc-head">
              <b>FEEDTAN STORE</b>
              <span>{{ __('What you will see') }}</span>
              <span>{{ __('Order status timeline') }}</span>
            </div>
            <hr class="rc-divider">
            <div class="rc-row"><span>{{ __('Status updates') }}</span><span>{{ __('Real time') }}</span></div>
            <div class="rc-row"><span>{{ __('Payment state') }}</span><span>{{ __('Confirmed') }}</span></div>
            <div class="rc-row"><span>{{ __('Delivery proof') }}</span><span>{{ __('On arrival') }}</span></div>
            <hr class="rc-divider">
            <div class="rc-status-list">
              <div class="rc-status done">
                <div class="chk"><i class="fa-solid fa-check"></i></div>
                <div><b>{{ __('Order received') }}</b><span>{{ __('Payment confirmed') }}</span></div>
              </div>
              <div class="rc-status done">
                <div class="chk"><i class="fa-solid fa-check"></i></div>
                <div><b>{{ __('Preparing in store') }}</b><span>{{ __('We pick the freshest items') }}</span></div>
              </div>
              <div class="rc-status">
                <div class="chk"></div>
                <div><b>{{ __('On the way') }}</b><span>{{ __('Rider heading to you') }}</span></div>
              </div>
              <div class="rc-status">
                <div class="chk"></div>
                <div><b>{{ __('Delivered') }}</b><span>{{ __('Enjoy your shopping') }}</span></div>
              </div>
            </div>
          </div>
          <div class="receipt-zig bot"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= FAQ ================= -->
  <section id="faq">
    <div class="wrap">
      <div class="sec-head reveal" style="text-align:center;margin:0 auto 20px;">
        <span class="eyebrow">{{ __('Common questions') }}</span>
        <h2>{{ __('Questions, answered') }}</h2>
      </div>
      <div class="faq-wrap" id="faqWrap">
        <div class="faq-item">
          <button class="faq-q">{{ __('How do I pay for my order?') }} <span class="plus">+</span></button>
          <div class="faq-a"><p>{{ __('You can pay online with mobile money or card at checkout, or choose cash and pay when your order is delivered.') }}</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q">{{ __('How long does delivery take?') }} <span class="plus">+</span></button>
          <div class="faq-a"><p>{{ __('Most orders inside Moshi arrive the same day. The exact fee and time are confirmed at checkout using your location.') }}</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q">{{ __('How do I track my order?') }} <span class="plus">+</span></button>
          <div class="faq-a"><p>{{ __('Use the tracking section above with your order number to follow each step of your delivery in real time.') }}</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q">{{ __('Can I return something I am not happy with?') }} <span class="plus">+</span></button>
          <div class="faq-a"><p>{{ __('Yes. Contact us within 24 hours of receiving your order and we will help you with an exchange or return.') }}</p></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= WHATSAPP ================= -->
  <section style="padding-top:0;">
    <div class="wrap">
      <div class="news-band reveal">
        <div>
          <h3>{{ __('Get offers before everyone else') }}</h3>
          <p>{{ __('Message us on WhatsApp for new arrivals, price updates and offers.') }}</p>
        </div>
        <div class="news-form">
          <input type="text" id="waNumber" inputmode="tel" placeholder="{{ __('Your WhatsApp number') }}" aria-label="{{ __('Your WhatsApp number') }}">
          <button class="btn btn-primary" onclick="joinWhatsApp()">{{ __('Join now') }}</button>
        </div>
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

function joinWhatsApp() {
  var raw = (document.getElementById('waNumber').value || '').replace(/[^0-9]/g, '');
  if (!raw) { showToast('{{ __('Please enter your WhatsApp number') }}', 'info'); return; }
  if (raw.length < 9) { showToast('{{ __('That number looks too short') }}', 'warning'); return; }
  var digits = raw.charAt(0) === '0' ? '255' + raw.slice(1) : raw;
  window.open('https://wa.me/' + digits + '?text=' + encodeURIComponent('{{ __('Hello Feedtan Store, please add me to your offers list.') }}'), '_blank');
}

(function initFaq() {
  document.querySelectorAll('.faq-item').forEach(function(item) {
    var q = item.querySelector('.faq-q');
    var a = item.querySelector('.faq-a');
    q.addEventListener('click', function() {
      var isOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item').forEach(function(i) {
        i.classList.remove('open');
        i.querySelector('.faq-a').style.maxHeight = null;
      });
      if (!isOpen) {
        item.classList.add('open');
        a.style.maxHeight = a.scrollHeight + 'px';
      }
    });
  });
})();

document.addEventListener('DOMContentLoaded', function() {
  initCart();
  setTimeout(hidePageLoader, 300);
  scrollToProductsIfFiltered();
});

function scrollToProductsIfFiltered() {
  var hasFilter = {{ request('category') || request('search') ? 'true' : 'false' }};
  if (!hasFilter || window.location.hash) return;
  var target = document.getElementById('shop');
  if (!target) return;
  var header = document.getElementById('siteHeader');
  var offset = header ? header.offsetHeight + 16 : 16;
  var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
  window.scrollTo({ top: top, behavior: 'smooth' });
}

window.addEventListener('load', hidePageLoader);
</script>
</body>
</html>
