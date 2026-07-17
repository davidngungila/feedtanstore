<!DOCTYPE html>
<html lang="en">
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
  $canonicalUrl = route('shop.checkout');
  $title = 'Checkout - Feedtan Store';
  $description = 'Complete your Feedtan Store order with secure checkout, delivery location, and easy payment options.';
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="noindex,nofollow,noarchive">
<meta name="author" content="Feedtan Store">
<meta name="theme-color" content="#1B4332">
<link rel="canonical" href="{{ $canonicalUrl }}">
<link rel="icon" type="image/png" href="{{ $logoUrl }}">
<link rel="apple-touch-icon" href="{{ $logoUrl }}">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="Feedtan Store">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $logoUrl }}">
<meta property="og:image:secure_url" content="{{ $logoUrl }}">
<meta property="og:image:type" content="image/png">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $logoUrl }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;0,9..144,700;0,9..144,900;1,9..144,500&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<style>
:root{
  --green-900:#0F2A1F;
  --green-700:#1B4332;
  --green-600:#235A41;
  --green-100:#E3EEE6;
  --parchment:#F7F4ED;
  --parchment-dim:#EFEADD;
  --ink:#0D1B12;
  --ink-soft:#4A5750;
  --orange:#E8893A;
  --orange-dark:#C96E22;
  --red:#D64545;
  --red-dim:#FBE7E7;
  --white:#FFFFFF;
  --line:#DBD4C2;

  --font-display:'Fraunces', serif;
  --font-body:'Inter', sans-serif;
  --font-mono:'JetBrains Mono', monospace;

  --radius-s:8px;
  --radius-m:14px;
  --radius-l:22px;
  --shadow-card:0 2px 10px rgba(15,42,31,0.07), 0 1px 2px rgba(15,42,31,0.06);
  --shadow-pop:0 18px 50px rgba(15,42,31,0.22);
  --maxw:1240px;
}

*{box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{
  margin:0;
  font-family:var(--font-body);
  background:var(--parchment);
  color:var(--ink);
  -webkit-font-smoothing:antialiased;
  line-height:1.5;
}
img{max-width:100%;display:block;}
a{color:inherit;text-decoration:none;}
button{font-family:inherit;cursor:pointer;}
input,select,textarea{font-family:inherit;}
.wrap{max-width:var(--maxw);margin:0 auto;padding:0 24px;}
h1,h2,h3,h4{font-family:var(--font-display);margin:0;letter-spacing:-0.01em;}
.monospace{font-family:var(--font-mono);}

:focus-visible{outline:2.5px solid var(--orange);outline-offset:2px;border-radius:4px;}

@media (prefers-reduced-motion: reduce){
  *{animation-duration:0.001ms !important;animation-iteration-count:1 !important;transition-duration:0.001ms !important;scroll-behavior:auto !important;}
}

.btn{
  display:inline-flex;align-items:center;justify-content:center;gap:8px;
  border:none;border-radius:999px;font-weight:700;font-size:15px;
  padding:13px 24px;transition:transform .15s ease, box-shadow .15s ease, background .15s ease;
  white-space:nowrap;
}
.btn:active{transform:scale(0.97);}
.btn-primary{background:var(--orange);color:var(--white);box-shadow:0 6px 16px rgba(232,137,58,0.35);}
.btn-primary:hover{background:var(--orange-dark);}
.btn-dark{background:var(--green-700);color:var(--white);}
.btn-dark:hover{background:var(--green-900);}
.btn-outline{background:transparent;color:var(--green-700);border:1.5px solid var(--green-700);}
.btn-outline:hover{background:var(--green-100);}
.btn-ghost{background:transparent;color:var(--ink);border:1.5px solid var(--line);}
.btn-ghost:hover{background:var(--white);}
.btn-ghost.active{background:var(--green-700);color:var(--white);border-color:var(--green-700);}
.btn-block{width:100%;}
.btn-sm{padding:9px 16px;font-size:13.5px;}
.btn:disabled{opacity:.45;cursor:not-allowed;transform:none;box-shadow:none;}

.visually-hidden{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);}

.topbar{background:var(--green-900);color:#CFE3D7;font-size:13px;}
.topbar .wrap{display:flex;align-items:center;justify-content:space-between;padding:7px 24px;gap:12px;flex-wrap:wrap;}
.topbar-msg{display:flex;align-items:center;gap:8px;}
.topbar-msg svg{flex-shrink:0;}

header.site-header{
  position:sticky;top:0;z-index:60;background:rgba(247,244,237,0.92);
  backdrop-filter:blur(10px);border-bottom:1px solid var(--line);
}
.header-inner{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:14px 24px;}
.logo{display:flex;align-items:center;gap:10px;font-family:var(--font-display);font-weight:800;font-size:23px;color:var(--green-900);flex-shrink:0;}
.logo-mark{
  width:38px;height:38px;border-radius:10px;background:var(--green-700);
  display:flex;align-items:center;justify-content:center;color:var(--orange);
  font-size:19px;font-weight:900;flex-shrink:0;
}
.logo-sub{display:block;font-family:var(--font-body);font-weight:600;font-size:10.5px;letter-spacing:.12em;color:var(--ink-soft);text-transform:uppercase;}

.search-bar{
  flex:1;display:flex;align-items:center;background:var(--white);border:1.5px solid var(--line);
  border-radius:999px;padding:0 6px 0 16px;max-width:560px;transition:border-color .15s;
}
.search-bar:focus-within{border-color:var(--green-700);}
.search-bar input{flex:1;border:none;background:transparent;padding:11px 8px;font-size:14.5px;outline:none;color:var(--ink);}
.search-bar button{background:var(--green-700);color:#fff;border-radius:999px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}

.header-actions{display:flex;align-items:center;gap:8px;flex-shrink:0;}
.icon-btn{
  position:relative;width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;
  background:transparent;border:none;color:var(--green-900);transition:background .15s;
}
.icon-btn:hover{background:var(--green-100);}
.icon-btn .badge{
  position:absolute;top:-2px;right:-2px;background:var(--orange);color:#fff;font-size:10.5px;font-weight:800;
  min-width:18px;height:18px;border-radius:999px;display:flex;align-items:center;justify-content:center;padding:0 4px;
  border:2px solid var(--parchment);
}
.nav-strip{border-top:1px solid var(--line);}
.nav-strip .wrap{display:flex;gap:26px;padding:11px 24px;overflow-x:auto;scrollbar-width:none;}
.nav-strip .wrap::-webkit-scrollbar{display:none;}
.nav-strip a{font-size:13.5px;font-weight:600;color:var(--ink-soft);white-space:nowrap;transition:color .15s;display:flex;align-items:center;gap:6px;}
.nav-strip a:hover, .nav-strip a.active{color:var(--green-700);}
.nav-strip a.active{color:var(--orange-dark);}

.mobile-search{display:none;padding:0 24px 14px;}

.section{padding:40px 0;}
.section-head{display:flex;align-items:baseline;justify-content:space-between;gap:16px;margin-bottom:28px;flex-wrap:wrap;}
.section-head h2{font-size:clamp(24px,3vw,32px);font-weight:700;}
.section-head .eyebrow{display:block;font-family:var(--font-body);font-size:12.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--orange-dark);margin-bottom:6px;}
.section-head p{color:var(--ink-soft);font-size:14.5px;margin:6px 0 0;}
.back-link{display:inline-flex;align-items:center;gap:6px;font-size:13.5px;font-weight:700;color:var(--green-700);}

.card{
  background:var(--white);
  border-radius:var(--radius-l);
  box-shadow:var(--shadow-card);
  padding:24px;
  margin-bottom:24px;
}

.option-card{
  display:flex;align-items:flex-start;gap:14px;border:2px solid var(--line);border-radius:var(--radius-m);
  padding:16px;cursor:pointer;margin-bottom:12px;transition:border-color .15s, background .15s;
}
.option-card:hover{border-color:var(--green-600);}
.option-card.selected{border-color:var(--green-700);background:var(--green-100);}
.option-card input{margin-top:3px;accent-color:var(--green-700);width:18px;height:18px;flex-shrink:0;}
.option-card .icon{width:40px;height:40px;border-radius:10px;background:var(--parchment-dim);display:flex;align-items:center;justify-content:center;color:var(--green-700);flex-shrink:0;}
.option-card b{display:block;font-size:14.5px;}
.option-card span{font-size:12.5px;color:var(--ink-soft);}
.option-grid-two{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}
.checkout-bottom-grid{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(320px,0.85fr);gap:24px;align-items:start;}

.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;}
@media(max-width:600px){.form-grid{grid-template-columns:1fr;}}
.field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px;}
.field label{font-size:12.5px;font-weight:700;color:var(--ink-soft);}
.field input, .field select, .field textarea{
  border:1.5px solid var(--line);border-radius:var(--radius-s);padding:11px 13px;font-size:14px;color:var(--ink);
  background:var(--white);outline:none;transition:border-color .15s;width:100%;
}
.field input:focus, .field select:focus, .field textarea:focus{border-color:var(--green-700);}
.field-error{font-size:12px;color:var(--red);min-height:14px;}
.field.has-error input, .field.has-error select, .field.has-error textarea{border-color:var(--red);}

.location-box{border:1.5px dashed var(--line);border-radius:var(--radius-m);padding:16px;margin:6px 0 18px;background:var(--parchment);}
.location-box-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;}
.location-status{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:var(--green-700);}
.location-status.pending{color:var(--ink-soft);}
.location-status.error{color:var(--red);}
.location-coords{font-family:var(--font-mono);font-size:12px;color:var(--ink-soft);margin-top:6px;}
.mini-map{width:100%;height:280px;border-radius:var(--radius-s);margin-top:10px;overflow:hidden;border:1px solid var(--line);position:relative;z-index:10;}
.mini-map .leaflet-control-container .leaflet-control{border-radius:10px;overflow:hidden;}
.mini-map .leaflet-control-container{z-index:20;}
.mini-map .leaflet-pane{z-index:15;}
.search-result-item{padding:10px 12px;cursor:pointer;border-bottom:1px solid var(--line);transition:background .15s;}
.search-result-item:hover{background:var(--green-100);}
.search-result-item:last-child{border-bottom:none;}
.search-result-name{font-size:13px;font-weight:600;color:var(--ink);}
.search-result-address{font-size:11px;color:var(--ink-soft);margin-top:2px;}

.summary-card{background:var(--parchment);border-radius:var(--radius-m);padding:18px;}
.summary-card h4{font-size:14px;margin-bottom:12px;}
.mini-item{display:flex;justify-content:space-between;font-size:13px;padding:7px 0;color:var(--ink-soft);}
.mini-item b{color:var(--ink);font-weight:600;}

.sum-row{display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:8px;color:var(--ink-soft);}
.sum-row.total{font-size:17px;font-weight:800;color:var(--ink);margin-top:10px;padding-top:10px;border-top:1px dashed var(--line);}
.sum-row.total span:last-child{font-family:var(--font-display);}

footer{background:var(--green-900);color:#BFD6C8;padding:40px 0 0;margin-top:40px;}
.footer-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1.2fr;gap:34px;padding-bottom:40px;}
.footer-grid h4{color:#fff;font-family:var(--font-body);font-size:13.5px;letter-spacing:.04em;text-transform:uppercase;margin-bottom:16px;}
.footer-grid ul{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;font-size:13.5px;}
.footer-grid ul a:hover{color:#fff;}
.footer-logo{display:flex;align-items:center;gap:10px;color:#fff;font-family:var(--font-display);font-weight:800;font-size:20px;margin-bottom:12px;}
.footer-bottom{border-top:1px solid rgba(255,255,255,0.1);padding:18px 24px;display:flex;justify-content:space-between;font-size:12.5px;flex-wrap:wrap;gap:8px;}
@media(max-width:760px){.footer-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:480px){.footer-grid{grid-template-columns:1fr;}}

@media(max-width:880px){
  .search-bar{display:none;}
  .mobile-search{display:block;}
  .logo-sub{display:none;}
}
@media(max-width:480px){
  .header-inner{padding:12px 16px;gap:10px;}
  .logo{font-size:19px;}
  .logo-mark{width:32px;height:32px;font-size:16px;}
  .wrap{padding:0 16px;}
  .section{padding:38px 0;}
}
@media(max-width:900px){
  .checkout-bottom-grid{grid-template-columns:1fr;}
}
.page-loader{
  position:fixed;inset:0;z-index:9999;background:rgba(247,244,237,0.94);backdrop-filter:blur(8px);
  display:flex;align-items:center;justify-content:center;transition:opacity .3s ease, visibility .3s ease;
}
.page-loader.hidden{opacity:0;visibility:hidden;pointer-events:none;}
.page-loader-card{text-align:center;padding:24px;}
.page-loader-ring{
  position:relative;width:110px;height:110px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;
}
.page-loader-ring::before{
  content:'';position:absolute;inset:0;border-radius:50%;border:4px solid rgba(35,90,65,0.12);border-top-color:var(--green-700);
  animation:spinLoader 1s linear infinite;
}
.page-loader-logo{
  width:74px;height:74px;border-radius:50%;object-fit:cover;background:#fff;box-shadow:var(--shadow-card);padding:4px;
}
@keyframes spinLoader{to{transform:rotate(360deg);}}
@media (prefers-reduced-motion: reduce){
  .page-loader-ring::before{animation:none;}
}

.toast{
  position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--green-900);color:#fff;padding:13px 22px;border-radius:999px;font-size:13.5px;font-weight:600;z-index:400;box-shadow:var(--shadow-pop);display:flex;align-items:center;gap:10px;opacity:0;visibility:hidden;transition:all .25s ease;
}
.toast.show{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0);}

.qty-stepper{display:flex;align-items:center;border:1.5px solid var(--line);border-radius:999px;overflow:hidden;flex:1;background:#fff;}
.qty-stepper button{width:40px;height:48px;background:transparent;border:none;font-size:18px;font-weight:700;color:var(--green-700);}
.qty-stepper button:hover{background:var(--green-100);}
.qty-stepper span{flex:1;text-align:center;font-weight:700;font-size:16px;}

.cart-drawer{
  position:fixed;top:50%;left:50%;transform:translate(-50%,-46%);width:92vw;max-width:760px;max-height:88vh;background:#fff;z-index:220;
  box-shadow:var(--shadow-pop);display:flex;flex-direction:column;opacity:0;visibility:hidden;transition:opacity .22s ease, transform .22s ease;
}
.cart-drawer.open{opacity:1;visibility:visible;transform:translate(-50%,-50%);}
.drawer-head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--line);}
.drawer-head h3{font-size:19px;}
.close-x{width:36px;height:36px;border-radius:50%;border:none;background:var(--parchment);display:flex;align-items:center;justify-content:center;color:var(--ink);flex-shrink:0;}
.close-x:hover{background:var(--line);}
.cart-list{flex:1;overflow-y:auto;padding:14px 20px;display:flex;flex-direction:column;gap:14px;}
.cart-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;height:100%;gap:14px;color:var(--ink-soft);padding:40px 20px;}
.cart-empty svg{color:var(--line);}
.cart-row{display:flex;gap:12px;padding-bottom:14px;border-bottom:1px solid var(--parchment-dim);}
.cart-row img{width:64px;height:64px;border-radius:10px;object-fit:cover;flex-shrink:0;background:var(--parchment-dim);}
.cart-row-info{flex:1;min-width:0;}
.cart-row-info b{font-size:13.5px;display:block;line-height:1.3;}
.cart-row-info .cr-meta{font-size:12px;color:var(--ink-soft);}
.cart-row-bottom{display:flex;align-items:center;justify-content:space-between;margin-top:8px;}
.cart-row .qty-stepper{flex:none;}
.cart-row .qty-stepper button{width:26px;height:30px;}
.cart-row .qty-stepper span{width:24px;font-size:13px;}
.cr-remove{background:none;border:none;color:var(--ink-soft);font-size:12px;text-decoration:underline;padding:0;}
.cr-remove:hover{color:var(--red);}
.cr-price{font-weight:700;font-size:13.5px;font-family:var(--font-display);}

.drawer-foot{padding:18px 20px;border-top:1px solid var(--line);background:var(--parchment);}
.sum-row{display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:8px;color:var(--ink-soft);}
.sum-row.total{font-size:17px;font-weight:800;color:var(--ink);margin-top:10px;padding-top:10px;border-top:1px dashed var(--line);}
.sum-row.total span:last-child{font-family:var(--font-display);}

.scrim{position:fixed;inset:0;background:rgba(13,27,18,0.55);z-index:200;opacity:0;visibility:hidden;transition:opacity .25s ease;}
.scrim.open{opacity:1;visibility:visible;}

@media(max-width:600px){
    .cart-drawer{width:100%;max-width:100%;height:100%;max-height:100%;border-radius:0;top:0;left:0;transform:translateY(100%);}
    .cart-drawer.open{transform:translateY(0);}
}
</style>
</head>
<body>

<div id="pageLoader" class="page-loader" aria-live="polite" aria-label="Page loading">
  <div class="page-loader-card">
      <div class="page-loader-ring">
        <img src="{{ asset('logo-image-feedtan-store.png') }}" alt="Feedtan Store" class="page-loader-logo">
      </div>
      <div style="font-weight:700;color:var(--green-700);font-size:18px;">{{ __('Loading...') }}</div>
    </div>
</div>

<div class="topbar">
  <div class="wrap">
    <div class="topbar-msg">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
      <span>{{ __('Free delivery for orders over TZS 50,000') }}</span>
    </div>
    <div class="topbar-msg" id="topbarPhone">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
      <span>+255 717 358 865</span>
    </div>
  </div>
</div>

<header class="site-header">
  <div class="header-inner wrap">
    <a href="{{ route('shop.index') }}" class="logo">
      <span class="logo-mark">F</span>
      <span>Feedtan<span class="logo-sub">Online Store</span></span>
    </a>
    <form class="search-bar" id="searchForm" role="search" action="{{ route('shop.index') }}">
      <label for="searchInput" class="visually-hidden">{{ __('Search products') }}</label>
      <input type="search" id="searchInput" name="search" placeholder="{{ __('Search products placeholder') }}" autocomplete="off" value="{{ request('search', '') }}">
      <button type="submit" aria-label="{{ __('Search') }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
      </button>
    </form>
    <div class="header-actions">
      <button class="icon-btn" id="mobileSearchToggle" aria-label="Toggle search" onclick="toggleMobileSearch()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
      </button>
      <button class="icon-btn" aria-label="Wishlist" onclick="showToast('{{ __('Saved items live in your wishlist') }}','heart')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
      </button>
      <a href="{{ route('shop.tracking') }}" class="icon-btn" aria-label="{{ __('Track my order') }}" title="{{ __('Track my order') }}">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/></svg>
      </a>
      <button class="icon-btn" aria-label="{{ __('Open cart') }}" onclick="openCart()">
        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="badge" id="cartBadge" style="display:none;">0</span>
      </button>
      <div class="language-switcher" style="margin-left: 10px;">
        <a href="{{ route('lang.switch', 'en') }}" class="btn btn-ghost btn-sm {{ App::getLocale() === 'en' ? 'active' : '' }}">EN</a>
        <a href="{{ route('lang.switch', 'sw') }}" class="btn btn-ghost btn-sm {{ App::getLocale() === 'sw' ? 'active' : '' }}">SW</a>
      </div>
    </div>
  </div>
  <div class="mobile-search" id="mobileSearchBox" style="display:none;">
    <form class="search-bar" action="{{ route('shop.index') }}">
      <input type="search" id="searchInputMobile" name="search" placeholder="{{ __('Search products placeholder') }}" autocomplete="off" value="{{ request('search', '') }}">
      <button type="submit" aria-label="{{ __('Search') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg></button>
    </form>
  </div>
  <nav class="nav-strip" aria-label="Primary">
    <div class="wrap">
      <a href="{{ route('shop.index') }}">{{ __('Home') }}</a>
      <a href="{{ route('shop.index') }}#shop">{{ __('Buy All') }}</a>
      @foreach($categories as $cat)
        <a href="{{ route('shop.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
      @endforeach
      <a href="{{ route('shop.tracking') }}">{{ __('Track my order') }}</a>
    </div>
  </nav>
</header>

<main id="mainContent">
  <section class="section">
    <div class="wrap">
      <a href="{{ route('shop.index') }}" class="back-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 18-6-6 6-6"/></svg> Back to store
      </a>
    </div>
    <div class="wrap" style="margin-top:24px;">
      <div class="section-head">
        <div>
          <span class="eyebrow">Checkout</span>
          <h1>Complete your order</h1>
        </div>
      </div>

      <div id="emptyCartState" class="card" style="display:none;">
        <h2 style="font-size:20px;margin-bottom:8px;">Your cart is empty</h2>
        <p style="margin:0 0 16px;color:var(--ink-soft);">Add at least one product to continue.</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <a href="{{ route('shop.index') }}" class="btn btn-primary">Go to store</a>
          <a href="{{ route('shop.tracking') }}" class="btn btn-ghost">Track order</a>
        </div>
      </div>

      <form id="checkoutForm" class="space-y-6">
        <!-- Delivery Options -->
        <div class="card" id="stepDelivery">
          <h2 style="font-size:20px;margin-bottom:16px;">Delivery Option</h2>
          <div class="option-grid-two">
            <label class="option-card selected" id="opt-delivery">
              <input type="radio" name="need_delivery" value="yes" checked onchange="toggleDeliveryOptions()">
              <div class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/></svg>
              </div>
              <div>
                <b>Home Delivery</b>
                <span>Your order will be delivered to your door</span>
              </div>
            </label>
            <label class="option-card" id="opt-pickup">
              <input type="radio" name="need_delivery" value="no" onchange="toggleDeliveryOptions()">
              <div class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v9h14v-9"/><path d="M9 19v-6h6v6"/></svg>
              </div>
              <div>
                <b>Pickup</b>
                <span>Pick up your order from our store</span>
              </div>
            </label>
          </div>
        </div>

        <!-- Customer Info -->
        <div class="card" id="stepCustomer">
          <h2 style="font-size:20px;margin-bottom:16px;">Customer Information</h2>
          <div class="form-grid">
            <div class="field">
              <label for="customerName">Full Name *</label>
              <input type="text" id="customerName" required>
              <div class="field-error" id="err-customerName"></div>
            </div>
            <div class="field">
              <label for="customerPhone">Phone Number *</label>
              <input type="tel" id="customerPhone" required>
              <div class="field-error" id="err-customerPhone"></div>
            </div>
            <div class="field" style="grid-column:1/-1;">
              <label for="customerEmail">Email (optional)</label>
              <input type="email" id="customerEmail">
              <div class="field-error" id="err-customerEmail"></div>
            </div>
          </div>
        </div>

        <!-- Delivery Address & Location -->
        <div class="card" id="deliveryAddressSection">
          <h2 style="font-size:20px;margin-bottom:16px;">Delivery Location</h2>
          
          <!-- Location Type Selection -->
          <div class="option-grid-two mb-4">
            <label class="option-card selected" id="opt-current-location">
              <input type="radio" name="location_type" value="current" checked onchange="toggleLocationType()">
              <div class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
              </div>
              <div>
                <b>Current Location</b>
                <span>Use your current GPS location</span>
              </div>
            </label>
            <label class="option-card" id="opt-other-location">
              <input type="radio" name="location_type" value="other" onchange="toggleLocationType()">
              <div class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </div>
              <div>
                <b>Other Location</b>
                <span>Choose a location on the map or enter an address</span>
              </div>
            </label>
          </div>

          <!-- Current Location Section -->
          <div class="location-box" id="currentLocationBox">
            <div class="field mb-4">
              <label for="deliveryAddress">Delivery Address *</label>
              <input type="text" id="deliveryAddress" placeholder="Searching your location automatically...">
              <div class="field-error" id="err-deliveryAddress" style="margin-top:8px;"></div>
            </div>
            <div class="location-box-head">
              <div class="location-status pending" id="locStatus">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <span>Detecting your location...</span>
              </div>
              <button type="button" class="btn btn-outline btn-sm" onclick="detectLocation()">Refresh</button>
            </div>
            <div class="location-coords" id="locCoords"></div>
            <div class="mini-map" id="mapPreview">
              <div class="pin">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
              </div>
            </div>
          </div>

          <!-- Other Location Section -->
          <div class="location-box" id="otherLocationBox" style="display:none;">
            <!-- Address Search (prominent) -->
            <div class="field mb-4">
              <label for="addressSearch">Search Location</label>
              <div style="display:flex;gap:8px;">
                <input type="text" id="addressSearch" placeholder="Search for a location (e.g., Kariakoo Market)" style="flex:1;" onkeypress="if(event.key === 'Enter') searchAddress()">
                <button type="button" class="btn btn-primary btn-sm" onclick="searchAddress()">Search</button>
              </div>
              <div class="field-error" id="err-addressSearch"></div>
            </div>
            
            <!-- Search Results -->
            <div id="searchResults" style="display:none;margin-bottom:12px;max-height:200px;overflow-y:auto;border:1px solid var(--line);border-radius:8px;"></div>
            
            <div class="field mb-4">
              <label for="manualAddress">Delivery Address *</label>
              <input type="text" id="manualAddress" placeholder="Enter delivery address (e.g., Kiboriloni, Moshi)">
              <div class="field-error" id="err-manualAddress"></div>
            </div>
            
            <div class="location-box-head">
              <div class="location-status pending" id="mapLocStatus">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><path d="M12 8h.01M11 12h2v4h-2z"/></svg>
                <span>Tap on the map to select a delivery location</span>
              </div>
            </div>
            <div class="location-coords" id="mapLocCoords"></div>
            <div class="mini-map" id="mapPreviewOther">
              <div class="pin">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
              </div>
            </div>
            <div class="field-error" id="err-mapLocation" style="margin-top:8px;"></div>
          </div>
        </div>

        <div class="checkout-bottom-grid">
          <!-- Order Summary -->
          <div class="card" id="stepSummary">
            <h2 style="font-size:20px;margin-bottom:16px;">Order Summary</h2>
            <div id="checkoutItems" class="space-y-3 mb-4"></div>
            <div class="border-t border-gray-100 pt-4 space-y-2" style="border-top:1px solid var(--line);padding-top:16px;">
              <div class="sum-row"><span>Subtotal</span><span id="subtotal">TZS 0</span></div>
              <div class="sum-row"><span>Delivery Distance</span><span id="deliveryDistanceDisplay">Choose location to calculate</span></div>
              <div class="sum-row"><span>Delivery Fee</span><span id="deliveryFeeDisplay">Choose location to calculate</span></div>
              <div class="sum-row total"><span>Total Now</span><span id="checkoutTotal">TZS 0</span></div>
            </div>
          </div>

          <!-- Payment Section -->
          <div>
            <div style="margin-bottom:16px;color:var(--ink-soft);font-size:14px;">
              Pay securely using mobile money. After placing the order, you will be asked to enter a valid phone number such as 2557XXXXXXXX, 07XXXXXXXX, or 7XXXXXXXX.
            </div>
            <input type="hidden" name="payment_method" value="online">
            <button type="submit" id="placeOrderBtn" class="btn btn-primary" style="display:flex;justify-content:center;width:100%;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
              Pay Now
            </button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>

<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <div class="footer-logo"><span class="logo-mark" style="background:var(--orange);color:var(--green-900);">F</span> Feedtan Store</div>
        <p style="font-size:13.5px;line-height:1.7;max-width:280px;">{{ __('Quality products, unbeatable prices, delivery to your door — or ready when you step in.') }}</p>
        <div style="display:flex;gap:10px;margin-top:16px;">
          <a href="#" class="icon-btn" style="background:rgba(255,255,255,0.08);color:#fff;" aria-label="Facebook"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1 0 2 .1 2.3.2v2.7h-1.6c-1.2 0-1.5.6-1.5 1.4V12h2.9l-.4 2.9h-2.5v7A10 10 0 0 0 22 12z"/></svg></a>
          <a href="#" class="icon-btn" style="background:rgba(255,255,255,0.08);color:#fff;" aria-label="Instagram"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg></a>
          <a href="#" class="icon-btn" style="background:rgba(255,255,255,0.08);color:#fff;" aria-label="WhatsApp"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.6-.8-1.9-.9-.2-.1-.4-.1-.6.1-.2.2-.6.9-.8 1-.1.2-.3.2-.5.1-1.4-.7-2.3-1.3-3.3-2.8-.1-.2-.1-.4.1-.5.2-.2.4-.5.6-.7.1-.2.1-.4 0-.5-.1-.2-.7-1.6-.9-2.2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9 1-.9 2.3 0 1.4 1 2.7 1.1 2.9.1.2 1.9 3 4.6 4.1 2.3.9 2.3.6 2.7.6.4 0 1.4-.6 1.6-1.1.2-.6.2-1.1.1-1.2 0-.1-.2-.2-.5-.3zM12 2a10 10 0 0 0-8.5 15.3L2 22l4.8-1.5A10 10 0 1 0 12 2z"/></svg></a>
        </div>
      </div>
      <div>
        <h4>{{ __('Buy') }}</h4>
        <ul>
          @foreach($categories as $cat)
          @if ($loop->index < 5)
            <li><a href="{{ route('shop.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
          @endif
          @endforeach
        </ul>
      </div>
      <div>
        <h4>{{ __('Support') }}</h4>
        <ul>
          <li><a href="{{ route('shop.tracking') }}">{{ __('Track my order') }}</a></li>
          <li><a href="#" onclick="showToast('{{ __('Contact us') }}','phone')">{{ __('Contact us') }}</a></li>
          <li><a href="#" onclick="showToast('{{ __('Return policy') }}','info')">{{ __('Return policy') }}</a></li>
          <li><a href="#" onclick="showToast('{{ __('Delivery info') }}','info')">{{ __('Delivery info') }}</a></li>
        </ul>
      </div>
      <div>
        <h4>{{ __('Visit our store') }}</h4>
        <ul>
          <li>{{ __('Location') }}</li>
          <li>{{ __('Opening hours') }}</li>
          <li>+255 717 358 865</li>
          <li>info@feedtanstore.com</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© {{ date('Y') }} Feedtan Store. Haki zote zimehifadhiwa.</span>
      <span>Imeundwa kwa usikivu kwa wanunuzi wa kila siku.</span>
    </div>
  </div>
</footer>

<aside class="cart-drawer" id="cartDrawer" aria-label="{{ __('Your Cart') }}">
  <div class="drawer-head">
    <h3>{{ __('Your Cart') }}</h3>
    <button class="close-x" onclick="closeCart()" aria-label="{{ __('Close cart') }}">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M18 6 6 18M6 6l12 12"/></svg>
    </button>
  </div>
  <div class="cart-list" id="cartList"></div>
  <div class="drawer-foot" id="cartFoot" style="display:none;">
    <div class="sum-row"><span>{{ __('Subtotal') }}</span><span id="cartSubtotal">TZS 0</span></div>
    <div class="sum-row"><span>{{ __('Delivery estimate') }}</span><span id="cartDeliveryEst">{{ __('Calculate at checkout') }}</span></div>
    <div class="sum-row total"><span>{{ __('Total') }}</span><span id="cartTotal">TZS 0</span></div>
    <a href="{{ route('shop.checkout') }}" class="btn btn-primary btn-block" style="margin-top:14px;">{{ __('Proceed to Checkout') }}</a>
    <button class="btn btn-ghost btn-block" style="margin-top:10px;" onclick="closeCart()">{{ __('Continue Shopping') }}</button>
  </div>
</aside>

<div id="scrim" class="scrim" onclick="closeAllOverlays()"></div>
<div id="toast" class="toast"></div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let cart = [];
let userLocation = { lat: null, lng: null };
let checkoutMap = null;
let checkoutMarker = null;
let checkoutMapOther = null;
let checkoutMarkerOther = null;
let selectedLocation = { lat: null, lng: null };
let currentDeliveryFee = 0;
let needDelivery = 'yes';

const DELIVERY_FEE = 3000;
const FREE_DELIVERY_THRESHOLD = 50000;

function normalizeCart(raw) {
  if (Array.isArray(raw)) return raw;
  if (raw && typeof raw === 'object') {
    return Object.entries(raw).map(([id, quantity]) => {
      return { id: String(id), name: 'Item', price: 0, quantity: Number(quantity) || 0 };
    }).filter(i => i.quantity > 0);
  }
  return [];
}

function initCart() {
  const saved = localStorage.getItem('shopCart');
  if (saved) {
    try {
      cart = normalizeCart(JSON.parse(saved));
      if (cart.length === 0) {
        showEmptyCartState();
      } else {
        renderCheckoutItems();
        updateTotal();
        fetchDeliveryFee();
      }
    } catch(e) {
      cart = [];
      localStorage.removeItem('shopCart');
      showEmptyCartState();
    }
  } else {
    cart = [];
    showEmptyCartState();
  }
}

function renderCheckoutItems() {
  const container = document.getElementById('checkoutItems');
  let html = '';
  cart.forEach(item => {
    const total = item.price * item.quantity;
    html += `
      <div class="mini-item">
        <div>
          <b>${item.name}</b>
          <div style="font-size:12px;color:var(--ink-soft);">${item.quantity} × TZS ${item.price.toLocaleString()}</div>
        </div>
        <div><b>TZS ${total.toLocaleString()}</b></div>
      </div>
    `;
  });
  container.innerHTML = html;
}

function calculateTotal() {
  return cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
}

async function fetchDeliveryFee() {
  console.log('fetchDeliveryFee called');
  const locationType = document.querySelector('input[name="location_type"]:checked')?.value;
  console.log('locationType:', locationType);
  let lat = null, lng = null;

  if (needDelivery === 'yes') {
    if (locationType === 'current') {
      lat = userLocation.lat;
      lng = userLocation.lng;
      console.log('Using current location:', { lat, lng });
    } else {
      lat = selectedLocation.lat;
      lng = selectedLocation.lng;
      console.log('Using selected location:', { lat, lng });
    }

    if (lat && lng) {
      const subtotal = calculateTotal();
      console.log('Subtotal:', subtotal);
      try {
        const response = await fetch('{{ route('shop.calculate-delivery-fee') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({
            delivery_latitude: lat,
            delivery_longitude: lng,
            subtotal: subtotal
          })
        });
        console.log('API Response status:', response.status);
        const data = await response.json();
        console.log('API Response data:', data);
        if (data.success) {
          currentDeliveryFee = data.delivery_fee;
          document.getElementById('deliveryDistanceDisplay').textContent = data.formatted_distance;
          document.getElementById('deliveryFeeDisplay').textContent = data.is_free ? 'FREE' : data.formatted_delivery_fee;
          updateTotal();
        }
      } catch (e) {
        console.error('Failed to calculate delivery fee', e);
      }
    } else {
      currentDeliveryFee = 0;
      document.getElementById('deliveryDistanceDisplay').textContent = 'Select location to calculate';
      document.getElementById('deliveryFeeDisplay').textContent = 'Select location to calculate';
      updateTotal();
    }
  } else {
    currentDeliveryFee = 0;
    document.getElementById('deliveryDistanceDisplay').textContent = 'Store Pickup';
    document.getElementById('deliveryFeeDisplay').textContent = 'FREE';
    updateTotal();
  }
}

function updateTotal() {
  const subtotal = calculateTotal();
  const total = subtotal + currentDeliveryFee;
  
  document.getElementById('subtotal').textContent = 'TZS ' + subtotal.toLocaleString();
  if (needDelivery === 'no') {
    document.getElementById('deliveryFeeDisplay').textContent = 'FREE';
  }
  document.getElementById('checkoutTotal').textContent = 'TZS ' + total.toLocaleString();
}

// Toggle delivery options
function toggleDeliveryOptions() {
  needDelivery = document.querySelector('input[name="need_delivery"]:checked').value;
  const deliveryAddressSection = document.getElementById('deliveryAddressSection');
  const optDelivery = document.getElementById('opt-delivery');
  const optPickup = document.getElementById('opt-pickup');
  
  if (needDelivery === 'yes') {
    deliveryAddressSection.style.display = 'block';
    optDelivery.classList.add('selected');
    optPickup.classList.remove('selected');
    toggleLocationType(); // Initialize location type
  } else {
    deliveryAddressSection.style.display = 'none';
    optPickup.classList.add('selected');
    optDelivery.classList.remove('selected');
  }
  fetchDeliveryFee();
}



// Get user location
function detectLocation() {
  const statusEl = document.getElementById('locStatus');
  const coordsEl = document.getElementById('locCoords');
  const iconEl = statusEl.querySelector('svg');
  
  statusEl.classList.remove('pending', 'error');
  statusEl.querySelector('span').textContent = 'Detecting your location...';
  iconEl.setAttribute('viewBox', '0 0 24 24');
  iconEl.innerHTML = '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>';
  
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        userLocation.lat = position.coords.latitude;
        userLocation.lng = position.coords.longitude;
        document.getElementById('deliveryAddress').value = `Auto-detected: ${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}`;
        coordsEl.textContent = `${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}`;
        statusEl.querySelector('span').textContent = 'Location detected!';
        statusEl.classList.remove('pending', 'error');
        initializeCheckoutMap();
        updateCheckoutMap(userLocation.lat, userLocation.lng);
        fetchDeliveryFee();
        setFieldError('deliveryAddress', '');
      },
      (error) => {
        console.error('Geolocation error:', error);
        statusEl.classList.add('error');
        statusEl.querySelector('span').textContent = 'Failed to detect location';
        iconEl.innerHTML = '<circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>';
        setFieldError('deliveryAddress', 'Please allow location access or choose another location');
      }
    );
  } else {
    statusEl.classList.add('error');
    statusEl.querySelector('span').textContent = 'Geolocation not supported';
  }
}

function initializeCheckoutMap() {
  if (checkoutMap || typeof L === 'undefined') return;
  const mapEl = document.getElementById('mapPreview');
  if (!mapEl) return;

  const initialLat = userLocation.lat || -6.7924;
  const initialLng = userLocation.lng || 39.2083;

  checkoutMap = L.map('mapPreview', {
    zoomControl: true,
    attributionControl: true,
  }).setView([initialLat, initialLng], 12);

  const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  });

  osmLayer.addTo(checkoutMap);

  checkoutMarker = L.marker([initialLat, initialLng]).addTo(checkoutMap)
    .bindPopup('Delivery location preview')
    .openPopup();

  setTimeout(() => checkoutMap.invalidateSize(), 150);
}

function updateCheckoutMap(lat, lng, zoom = 15) {
  initializeCheckoutMap();
  if (!checkoutMap || !checkoutMarker) return;
  checkoutMarker.setLatLng([lat, lng]);
  checkoutMap.setView([lat, lng], zoom);
  checkoutMarker.bindPopup(`Delivery location<br>${lat.toFixed(6)}, ${lng.toFixed(6)}`).openPopup();
  setTimeout(() => checkoutMap.invalidateSize(), 100);
}

function initializeCheckoutMapOther() {
  if (checkoutMapOther || typeof L === 'undefined') return;
  const mapEl = document.getElementById('mapPreviewOther');
  if (!mapEl) return;

  const initialLat = selectedLocation.lat || -6.7924;
  const initialLng = selectedLocation.lng || 39.2083;

  checkoutMapOther = L.map('mapPreviewOther', {
    zoomControl: true,
    attributionControl: true,
  }).setView([initialLat, initialLng], 12);

  const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  });

  osmLayer.addTo(checkoutMapOther);

  // Add click handler to select location
  checkoutMapOther.on('click', function(e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;
    
    selectedLocation.lat = lat;
    selectedLocation.lng = lng;
    
    if (checkoutMarkerOther) {
      checkoutMarkerOther.setLatLng([lat, lng]);
    } else {
      checkoutMarkerOther = L.marker([lat, lng]).addTo(checkoutMapOther);
    }
    
    checkoutMarkerOther.bindPopup(`Selected location<br>${lat.toFixed(6)}, ${lng.toFixed(6)}`).openPopup();
    checkoutMapOther.setView([lat, lng], 15);
    
    document.getElementById('mapLocCoords').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    document.getElementById('mapLocStatus').querySelector('span').textContent = 'Location selected!';
    document.getElementById('mapLocStatus').classList.remove('pending', 'error');
    setFieldError('mapLocation', '');
    fetchDeliveryFee();
  });

  // Add initial marker if location exists
  if (selectedLocation.lat && selectedLocation.lng) {
    checkoutMarkerOther = L.marker([selectedLocation.lat, selectedLocation.lng]).addTo(checkoutMapOther)
      .bindPopup(`Selected location<br>${selectedLocation.lat.toFixed(6)}, ${selectedLocation.lng.toFixed(6)}`)
      .openPopup();
  }

  setTimeout(() => checkoutMapOther.invalidateSize(), 150);
}

function toggleLocationType() {
  const locationType = document.querySelector('input[name="location_type"]:checked').value;
  const currentLocationBox = document.getElementById('currentLocationBox');
  const otherLocationBox = document.getElementById('otherLocationBox');
  const optCurrent = document.getElementById('opt-current-location');
  const optOther = document.getElementById('opt-other-location');
  
  if (locationType === 'current') {
    currentLocationBox.style.display = 'block';
    otherLocationBox.style.display = 'none';
    optCurrent.classList.add('selected');
    optOther.classList.remove('selected');
    initializeCheckoutMap();
    setTimeout(() => {
      if (checkoutMap) checkoutMap.invalidateSize();
    }, 150);
  } else {
    currentLocationBox.style.display = 'none';
    otherLocationBox.style.display = 'block';
    optOther.classList.add('selected');
    optCurrent.classList.remove('selected');
    initializeCheckoutMapOther();
    setTimeout(() => {
      if (checkoutMapOther) checkoutMapOther.invalidateSize();
    }, 150);
  }
  
  // Clear errors
  setFieldError('deliveryAddress', '');
  setFieldError('manualAddress', '');
  setFieldError('mapLocation', '');
  
  fetchDeliveryFee();
}

// Search address using Nominatim API
async function searchAddress() {
  const searchInput = document.getElementById('addressSearch');
  const searchResults = document.getElementById('searchResults');
  const query = searchInput.value.trim();
  
  if (!query) {
    setFieldError('addressSearch', 'Please enter a location to search');
    searchResults.style.display = 'none';
    return;
  }
  
  setFieldError('addressSearch', '');
  searchResults.innerHTML = '<div style="padding:12px;text-align:center;color:var(--ink-soft);">Searching...</div>';
  searchResults.style.display = 'block';
  
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query + ', Tanzania')}&format=json&limit=5`);
    const data = await response.json();
    
    if (data.length === 0) {
      searchResults.innerHTML = '<div style="padding:12px;text-align:center;color:var(--ink-soft);">No results found. Try a different search term.</div>';
      return;
    }
    
    let html = '';
    data.forEach((result, index) => {
      html += `
        <div class="search-result-item" onclick="selectSearchResult(${result.lat}, ${result.lon}, '${result.display_name.replace(/'/g, "\\'")}')">
          <div class="search-result-name">${result.display_name.split(',')[0]}</div>
          <div class="search-result-address">${result.display_name}</div>
        </div>
      `;
    });
    searchResults.innerHTML = html;
  } catch (error) {
    console.error('Search error:', error);
    searchResults.innerHTML = '<div style="padding:12px;text-align:center;color:var(--red);">Search failed. Please try again.</div>';
  }
}

// Select search result and update map
function selectSearchResult(lat, lng, displayName) {
  selectedLocation.lat = parseFloat(lat);
  selectedLocation.lng = parseFloat(lng);
  
  // Update manual address with the selected location
  document.getElementById('manualAddress').value = displayName.split(',')[0];
  document.getElementById('addressSearch').value = '';
  document.getElementById('searchResults').style.display = 'none';
  
  // Update map
  if (checkoutMarkerOther) {
    checkoutMarkerOther.setLatLng([selectedLocation.lat, selectedLocation.lng]);
  } else {
    checkoutMarkerOther = L.marker([selectedLocation.lat, selectedLocation.lng]).addTo(checkoutMapOther);
  }
  
  checkoutMarkerOther.bindPopup(`Selected location<br>${displayName}`).openPopup();
  checkoutMapOther.setView([selectedLocation.lat, selectedLocation.lng], 15);
  
  // Update coordinates display
  document.getElementById('mapLocCoords').textContent = `${selectedLocation.lat.toFixed(6)}, ${selectedLocation.lng.toFixed(6)}`;
  document.getElementById('mapLocStatus').querySelector('span').textContent = 'Location selected!';
  document.getElementById('mapLocStatus').classList.remove('pending', 'error');
  
  // Clear errors
  setFieldError('manualAddress', '');
  setFieldError('mapLocation', '');
  fetchDeliveryFee();
}

function showEmptyCartState() {
  const emptyState = document.getElementById('emptyCartState');
  const form = document.getElementById('checkoutForm');
  if (emptyState) emptyState.style.display = 'block';
  if (form) form.style.display = 'none';
}

function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute('content') : '';
}

function setFieldError(fieldId, message) {
  const err = document.getElementById('err-' + fieldId);
  const input = document.getElementById(fieldId);
  if (err) err.textContent = message || '';
  if (input) {
    const wrapper = input.closest('.field');
    if (wrapper) wrapper.classList.toggle('has-error', Boolean(message));
  }
}

function validateCustomer() {
  let ok = true;
  const name = document.getElementById('customerName').value.trim();
  const phone = document.getElementById('customerPhone').value.trim();
  const email = document.getElementById('customerEmail').value.trim();

  if (!name) { setFieldError('customerName', 'Full Name is required'); ok = false; } else { setFieldError('customerName', ''); }
  if (!phone) { setFieldError('customerPhone', 'Phone Number is required'); ok = false; } else { setFieldError('customerPhone', ''); }
  if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setFieldError('customerEmail', 'Enter a valid email'); ok = false; } else { setFieldError('customerEmail', ''); }
  return ok;
}

function validateAddressIfNeeded() {
  const needDeliveryVal = document.querySelector('input[name="need_delivery"]:checked').value;
  if (needDeliveryVal !== 'yes') {
    document.getElementById('deliveryAddress').value = 'Store Pickup';
    setFieldError('deliveryAddress', '');
    return true;
  }

  const locationType = document.querySelector('input[name="location_type"]:checked').value;
  
  if (locationType === 'current') {
    if (!userLocation.lat || !userLocation.lng) {
      setFieldError('deliveryAddress', 'Your location must be captured automatically for delivery.');
      return false;
    }
    document.getElementById('deliveryAddress').value = `Auto-detected location: ${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}`;
    setFieldError('deliveryAddress', '');
    return true;
  } else {
    // Other location validation
    const manualAddress = document.getElementById('manualAddress').value.trim();
    if (!manualAddress) {
      setFieldError('manualAddress', 'Delivery address is required');
      return false;
    }
    setFieldError('manualAddress', '');
    
    if (!selectedLocation.lat || !selectedLocation.lng) {
      setFieldError('mapLocation', 'Please select a location on the map');
      return false;
    }
    setFieldError('mapLocation', '');
    
    document.getElementById('deliveryAddress').value = `${manualAddress} (${selectedLocation.lat.toFixed(6)}, ${selectedLocation.lng.toFixed(6)})`;
    return true;
  }
}

function addToCart(id, name, price) {
  const existing = cart.find(i => String(i.id) === String(id));
  if (existing) {
    existing.quantity += 1;
    existing.name = name;
    existing.price = Number(price) || 0;
  } else {
    cart.push({ id: String(id), name, price: Number(price) || 0, quantity: 1 });
  }
  saveCart();
  updateCartUI();
  showToast(name + ' added to cart', 'cart');
}

function changeQty(id, delta, name = null, price = null) {
  const idx = cart.findIndex(i => String(i.id) === String(id));
  if (idx === -1) return;
  cart[idx].quantity += delta;
  if (name !== null) cart[idx].name = name;
  if (price !== null) cart[idx].price = Number(price) || 0;
  if (cart[idx].quantity <= 0) cart.splice(idx, 1);
  saveCart();
  updateCartUI();
}

function removeFromCart(id) {
  cart = cart.filter(i => String(i.id) !== String(id));
  saveCart();
  updateCartUI();
}

function saveCart() {
  localStorage.setItem('shopCart', JSON.stringify(cart));
}

function cartCount() { return cart.reduce((sum, item) => sum + (Number(item.quantity) || 0), 0); }
function cartSubtotal() {
  return cart.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 0), 0);
}

function updateCartUI() {
  const count = cartCount();
  const badge = document.getElementById('cartBadge');
  badge.style.display = count > 0 ? 'flex' : 'none';
  badge.textContent = count;
  renderCartList();
  renderCheckoutItems();
  updateTotal();
}

function renderCartList() {
  const list = document.getElementById('cartList');
  const foot = document.getElementById('cartFoot');
  if (cart.length === 0) {
    list.innerHTML = '<div class="cart-empty">' +
      '<svg width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>' +
      '<b>{{ __("Your cart is empty") }}</b>' +
      '<span>{{ __("Explore the menu and add something nice.") }}</span>' +
      '<button class="btn btn-primary btn-sm" onclick="closeCart()">{{ __("Start shopping") }}</button>' +
      '</div>';
    foot.style.display = 'none';
    return;
  }
  foot.style.display = 'block';
  list.innerHTML = cart.map(item => {
    let img = 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=500&q=80';
    return '<div class="cart-row">' +
      '<img src="' + img + '" alt="' + item.name + '">' +
      '<div class="cart-row-info">' +
        '<b>' + item.name + '</b>' +
        '<span class="cr-meta">{{ __("per item") }} · TZS ' + item.price.toLocaleString() + ' {{ __("each") }}</span>' +
        '<div class="cart-row-bottom">' +
          '<div class="qty-stepper">' +
            '<button onclick="changeQty(\'' + item.id + '\', -1, \'' + item.name + '\', ' + item.price + ')" aria-label="Decrease quantity">−</button>' +
            '<span>' + item.quantity + '</span>' +
            '<button onclick="changeQty(\'' + item.id + '\', 1, \'' + item.name + '\', ' + item.price + ')" aria-label="Increase quantity">+</button>' +
          '</div>' +
          '<span class="cr-price">TZS ' + (item.price * item.quantity).toLocaleString() + '</span>' +
        '</div>' +
        '<button class="cr-remove" onclick="removeFromCart(\'' + item.id + '\')">{{ __("Remove") }}</button>' +
      '</div>' +
    '</div>';
  }).join('');
  const subtotal = cartSubtotal();
  document.getElementById('cartSubtotal').textContent = 'TZS ' + subtotal.toLocaleString();
  document.getElementById('cartTotal').textContent = 'TZS ' + subtotal.toLocaleString();
  document.getElementById('cartDeliveryEst').textContent = subtotal >= FREE_DELIVERY_THRESHOLD ? '{{ __("Free (order qualifies)") }}' : 'TZS ' + DELIVERY_FEE.toLocaleString() + ' {{ __("if delivered") }}';
}

function openCart() {
  document.getElementById('cartDrawer').classList.add('open');
  document.getElementById('scrim').classList.add('open');
}

function closeCart() {
  document.getElementById('cartDrawer').classList.remove('open');
  document.getElementById('scrim').classList.remove('open');
}

// Update closeAllOverlays to also close the add to cart modal
function closeAllOverlays() {
  closeCart();
}

function showToast(msg, icon) {
  const toast = document.getElementById('toast');
  const icons = {
    heart:'<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7-4.6-9.5-9C0.7 8.6 2 5 5.3 4.2 7.5 3.6 9.6 4.8 12 7.5c2.4-2.7 4.5-3.9 6.7-3.3C22 5 23.3 8.6 21.5 12 19 16.4 12 21 12 21z"/></svg>',
    info:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>',
    phone:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.58 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
    cart:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>'
  };
  toast.innerHTML = (icons[icon] || icons.info) + '<span>' + msg + '</span>';
  toast.classList.add('show');
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(() => toast.classList.remove('show'), 2800);
}

function toggleMobileSearch() {
  const box = document.getElementById('mobileSearchBox');
  box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

function hidePageLoader() {
  const loader = document.getElementById('pageLoader');
  if (!loader) return;
  loader.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
  initCart();
  setTimeout(hidePageLoader, 350);

  // Initialize location detection on load
  detectLocation();

  // Checkout form submission
  document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!validateCustomer() || !validateAddressIfNeeded()) {
      return;
    }

    const placeOrderBtn = document.getElementById('placeOrderBtn');
    placeOrderBtn.disabled = true;
    placeOrderBtn.innerHTML = '<svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> Placing Order...';

    try {
      const requestBody = {
        customer_name: document.getElementById('customerName').value.trim(),
        customer_phone: document.getElementById('customerPhone').value.trim(),
        customer_email: document.getElementById('customerEmail').value.trim(),
        delivery_address: document.getElementById('deliveryAddress').value.trim(),
        delivery_latitude: needDelivery === 'yes' ? (document.querySelector('input[name="location_type"]:checked').value === 'current' ? userLocation.lat : selectedLocation.lat) : null,
        delivery_longitude: needDelivery === 'yes' ? (document.querySelector('input[name="location_type"]:checked').value === 'current' ? userLocation.lng : selectedLocation.lng) : null,
        delivery_fee: currentDeliveryFee,
        payment_method: document.querySelector('input[name="payment_method"]:checked').value,
        items: cart.map(item => ({
          product_id: item.id,
          quantity: item.quantity
        }))
      };

      const response = await fetch('{{ route('shop.place-order') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(requestBody)
      });

      const data = await response.json();

      if (data.success) {
        localStorage.removeItem('shopCart');
        Swal.fire({
          icon: 'success',
          title: 'Order placed!',
          html: data.payment_message || 'Your order has been placed successfully!',
          confirmButtonText: 'Track Order'
        }).then(() => {
          window.location.href = data.tracking_url;
        });
      } else {
        throw new Error(data.message || 'Failed to place order');
      }
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: error.message || 'Something went wrong!',
        confirmButtonText: 'Try Again'
      });
    } finally {
      placeOrderBtn.disabled = false;
      placeOrderBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> Place Order';
    }
  });
});

window.addEventListener('load', hidePageLoader);
</script>
</body>
</html>
