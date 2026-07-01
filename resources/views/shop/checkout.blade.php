<!DOCTYPE html>
<html lang="en">
<head>
@php
  $logoUrl = asset('logo-image-feedtan-store.png');
  $checkoutCanonicalUrl = route('shop.checkout');
  $checkoutTitle = 'Checkout - Feedtan Store';
  $checkoutDescription = 'Complete your Feedtan Store order with secure checkout, delivery location capture, and flexible payment options.';
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>{{ $checkoutTitle }}</title>
<meta name="description" content="{{ $checkoutDescription }}">
<meta name="robots" content="noindex,nofollow,noarchive">
<meta name="author" content="Feedtan Store">
<meta name="theme-color" content="#1B4332">
<link rel="canonical" href="{{ $checkoutCanonicalUrl }}">
<link rel="icon" type="image/png" href="{{ $logoUrl }}">
<link rel="apple-touch-icon" href="{{ $logoUrl }}">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="Feedtan Store">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $checkoutTitle }}">
<meta property="og:description" content="{{ $checkoutDescription }}">
<meta property="og:url" content="{{ $checkoutCanonicalUrl }}">
<meta property="og:image" content="{{ $logoUrl }}">
<meta property="og:image:secure_url" content="{{ $logoUrl }}">
<meta property="og:image:type" content="image/png">
<meta property="og:image:alt" content="Feedtan Store logo">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $checkoutTitle }}">
<meta name="twitter:description" content="{{ $checkoutDescription }}">
<meta name="twitter:image" content="{{ $logoUrl }}">
<meta name="twitter:image:alt" content="Feedtan Store logo">
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
.mono{font-family:var(--font-mono);}

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
.btn-block{width:100%;}
.btn-sm{padding:9px 16px;font-size:13.5px;}
.btn:disabled{opacity:.45;cursor:not-allowed;transform:none;box-shadow:none;}

.visually-hidden{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);}

.topbar{background:var(--green-900);color:#CFE3D7;font-size:13px;}
.topbar .wrap{display:flex;align-items:center;justify-content:space-between;padding:7px 24px;gap:12px;}
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
.option-card .ic{width:40px;height:40px;border-radius:10px;background:var(--parchment-dim);display:flex;align-items:center;justify-content:center;color:var(--green-700);flex-shrink:0;}
.option-card b{display:block;font-size:14.5px;}
.option-card span{font-size:12.5px;color:var(--ink-soft);}
.option-grid-two{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}
.option-grid-three{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;}
.option-grid-two .option-card,
.option-grid-three .option-card{margin-bottom:0;height:100%;}
.checkout-bottom-grid{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(320px,0.85fr);gap:24px;align-items:start;}
.checkout-bottom-grid > .card{margin-bottom:0;}

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

.loc-box{border:1.5px dashed var(--line);border-radius:var(--radius-m);padding:16px;margin:6px 0 18px;background:var(--parchment);}
.loc-box-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;}
.loc-status{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:var(--green-700);}
.loc-status.pending{color:var(--ink-soft);}
.loc-status.error{color:var(--red);}
.loc-coords{font-family:var(--font-mono);font-size:12px;color:var(--ink-soft);margin-top:6px;}
.mini-map{width:100%;height:280px;border-radius:var(--radius-s);margin-top:10px;overflow:hidden;border:1px solid var(--line);position:relative;z-index:1;}
.mini-map .leaflet-control-container .leaflet-control{border-radius:10px;overflow:hidden;}
.mini-map .leaflet-control-layers,
.mini-map .leaflet-bar{box-shadow:0 8px 22px rgba(15,42,31,0.12);}
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
@media(max-width:760px){
  .option-grid-three{grid-template-columns:1fr;}
}
@media(max-width:640px){
  .option-grid-two{grid-template-columns:1fr;}
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
</style>
</head>
<body>

<div id="pageLoader" class="page-loader" aria-live="polite" aria-label="Page loading">
  <div class="page-loader-card">
    <div class="page-loader-ring">
      <img src="{{ asset('logo-image-feedtan-store.png') }}" alt="Feedtan Store" class="page-loader-logo">
    </div>
    <div style="font-weight:700;color:var(--green-700);font-size:18px;">Loading...</div>
  </div>
</div>

<div class="topbar">
  <div class="wrap">
    <div class="topbar-msg">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
      <span>Free delivery on orders over TZS 50,000</span>
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
      <label for="searchInput" class="visually-hidden">Search products</label>
      <input type="search" id="searchInput" name="search" placeholder="Search for rice, oil, fruits, electronics…" autocomplete="off" value="{{ request('search', '') }}">
      <button type="submit" aria-label="Search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
      </button>
    </form>
    <div class="header-actions">
      <button class="icon-btn" id="mobileSearchToggle" aria-label="Toggle search" onclick="toggleMobileSearch()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
      </button>
      <button class="icon-btn" aria-label="Wishlist" onclick="showToast('Saved items live in your wishlist','heart')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
      </button>
      <a href="{{ route('shop.tracking') }}" class="icon-btn" aria-label="Track my order" title="Track order">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/></svg>
      </a>
      <button class="icon-btn" aria-label="Open cart" onclick="showToast('Cart is on previous page','info')">
        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      </button>
    </div>
  </div>
  <div class="mobile-search" id="mobileSearchBox" style="display:none;">
    <form class="search-bar" action="{{ route('shop.index') }}">
      <input type="search" id="searchInputMobile" name="search" placeholder="Search products…" autocomplete="off" value="{{ request('search', '') }}">
      <button type="submit" aria-label="Search"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg></button>
    </form>
  </div>
  <nav class="nav-strip" aria-label="Primary">
    <div class="wrap">
      <a href="{{ route('shop.index') }}">Home</a>
      <a href="{{ route('shop.index') }}#shop">Shop All</a>
      <a href="{{ route('shop.tracking') }}">Track Order</a>
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
        <p style="margin:0 0 16px;color:var(--ink-soft);">Add at least one item to continue to checkout.</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <a href="{{ route('shop.index') }}" class="btn btn-primary">Go to shop</a>
          <a href="{{ route('shop.tracking') }}" class="btn btn-ghost">Track an order</a>
        </div>
      </div>

      <form id="checkoutForm" class="space-y-6">
        <!-- Delivery Options -->
        <div class="card" id="stepDelivery">
          <h2 class="text-lg font-bold mb-4">Delivery Option</h2>
          <div class="option-grid-two">
            <label class="option-card selected" id="opt-delivery">
              <input type="radio" name="need_delivery" value="yes" checked onchange="toggleDeliveryOptions()">
              <div class="ic">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/></svg>
              </div>
              <div>
                <b>Home Delivery</b>
                <span>Get your order delivered to your doorstep</span>
              </div>
            </label>
            <label class="option-card" id="opt-pickup">
              <input type="radio" name="need_delivery" value="no" onchange="toggleDeliveryOptions()">
              <div class="ic">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v9h14v-9"/><path d="M9 19v-6h6v6"/></svg>
              </div>
              <div>
                <b>Pickup</b>
                <span>Pick up your order from our store</span>
              </div>
            </label>
          </div>
        </div>
        <div class="card" style="padding:16px;display:none;justify-content:flex-end;" id="stepDeliveryActions">
          <button type="button" class="btn btn-dark" id="btnNextDelivery">Next</button>
        </div>

        <!-- Customer Info -->
        <div class="card" id="stepCustomer">
          <h2 class="text-lg font-bold mb-4">Customer Information</h2>
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
        <div class="card" style="padding:16px;display:none;justify-content:space-between;" id="stepCustomerActions">
          <button type="button" class="btn btn-ghost" id="btnBackCustomer">Back</button>
          <button type="button" class="btn btn-dark" id="btnNextCustomer">Next</button>
        </div>

        <!-- Delivery Address & Location -->
        <div class="card" id="deliveryAddressSection">
          <h2 class="text-lg font-bold mb-4">Delivery Location</h2>
          <textarea id="deliveryAddress" rows="3" style="display:none;"></textarea>
          
          <!-- Location Type Selection -->
          <div class="option-grid-two mb-4">
            <label class="option-card selected" id="opt-current-location">
              <input type="radio" name="location_type" value="current" checked onchange="toggleLocationType()">
              <div class="ic">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
              </div>
              <div>
                <b>Current Location</b>
                <span>Use your current GPS location</span>
              </div>
            </label>
            <label class="option-card" id="opt-other-location">
              <input type="radio" name="location_type" value="other" onchange="toggleLocationType()">
              <div class="ic">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </div>
              <div>
                <b>Other Location</b>
                <span>Select location on map or enter address</span>
              </div>
            </label>
          </div>

          <!-- Current Location Section -->
          <div class="loc-box" id="currentLocationBox">
            <div class="loc-box-head">
              <div class="loc-status pending" id="locStatus">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <span>Waiting to detect your location…</span>
              </div>
              <button type="button" class="btn btn-outline btn-sm" onclick="detectLocation()">Use my current location</button>
            </div>
            <div class="loc-coords" id="locCoords"></div>
            <div class="mini-map" id="mapPreview">
              <div class="pin">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
              </div>
            </div>
            <div class="field-error" id="err-deliveryAddress" style="margin-top:8px;"></div>
          </div>

          <!-- Other Location Section -->
          <div class="loc-box" id="otherLocationBox" style="display:none;">
            <div class="form-grid">
              <div class="field">
                <label for="manualAddress">Delivery Address *</label>
                <input type="text" id="manualAddress" placeholder="Enter your delivery address (e.g., Kiboriloni, Moshi)">
                <div class="field-error" id="err-manualAddress"></div>
              </div>
              
              <!-- Address Search -->
              <div class="field">
                <label for="addressSearch">Search Location</label>
                <div style="display:flex;gap:8px;">
                  <input type="text" id="addressSearch" placeholder="Search for a place (e.g., Kariakoo market)" style="flex:1;" onkeypress="if(event.key === 'Enter') searchAddress()">
                  <button type="button" class="btn btn-outline btn-sm" onclick="searchAddress()">Search</button>
                </div>
                <div class="field-error" id="err-addressSearch"></div>
              </div>
            </div>
            
            <!-- Search Results -->
            <div id="searchResults" style="display:none;margin-bottom:12px;max-height:200px;overflow-y:auto;border:1px solid var(--line);border-radius:8px;"></div>
            
            <div class="loc-box-head">
              <div class="loc-status pending" id="mapLocStatus">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><path d="M12 8h.01M11 12h2v4h-2z"/></svg>
                <span>Click on the map to select delivery location</span>
              </div>
            </div>
            <div class="loc-coords" id="mapLocCoords"></div>
            <div class="mini-map" id="mapPreviewOther">
              <div class="pin">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
              </div>
            </div>
            <div class="field-error" id="err-mapLocation" style="margin-top:8px;"></div>
          </div>


        </div>
        <div class="card" style="padding:16px;display:none;justify-content:space-between;" id="stepAddressActions">
          <button type="button" class="btn btn-ghost" id="btnBackAddress">Back</button>
          <button type="button" class="btn btn-dark" id="btnNextAddress">Next</button>
        </div>

        <div class="checkout-bottom-grid">
          <!-- Payment Method -->
          <div class="card" id="stepPayment">
            <h2 class="text-lg font-bold mb-4">Payment Method</h2>
            <div class="option-grid-three">
              <label class="option-card selected" id="pay-cash">
                <input type="radio" name="payment_method" value="cash" checked onchange="selectPaymentMethod('cash')">
                <div class="ic">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/></svg>
                </div>
                <div>
                  <b>Cash</b>
                  <span>Pay on delivery/pickup</span>
                </div>
              </label>
              <label class="option-card" id="pay-online">
                <input type="radio" name="payment_method" value="online" onchange="selectPaymentMethod('online')">
                <div class="ic">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                </div>
                <div>
                  <b>Online Payment</b>
                  <span>Pay via mobile money</span>
                </div>
              </label>
              <label class="option-card" id="pay-bank">
                <input type="radio" name="payment_method" value="bank" onchange="selectPaymentMethod('bank')">
                <div class="ic">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7v10l10 5 10-5V7L12 2z"/><path d="M12 22V12"/></svg>
                </div>
                <div>
                  <b>Bank Transfer</b>
                  <span>Pay via bank deposit</span>
                </div>
              </label>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="card" id="stepSummary">
            <h2 class="text-lg font-bold mb-4">Order Summary</h2>
            <div id="checkoutItems" class="space-y-3 mb-4"></div>
            <div class="border-t border-gray-100 pt-4 space-y-2">
              <div class="sum-row"><span>Subtotal</span><span id="subtotal">TZS 0</span></div>
              <div class="sum-row"><span>Delivery Fee</span><span id="deliveryFeeDisplay">Assigned by admin</span></div>
              <div class="sum-row total"><span>Current Total</span><span id="checkoutTotal">TZS 0</span></div>
            </div>
          </div>
        </div>
        <div class="card" style="padding:16px;display:none;justify-content:space-between;" id="stepPaymentActions">
          <button type="button" class="btn btn-ghost" id="btnBackPayment">Back</button>
          <button type="button" class="btn btn-dark" id="btnNextPayment">Next</button>
        </div>

        <!-- Place Order -->
        <button type="submit" id="placeOrderBtn" class="btn btn-primary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
          Place Order
        </button>
      </form>
    </div>
  </section>
</main>

<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <div class="footer-logo"><span class="logo-mark" style="background:var(--orange);color:var(--green-900);">F</span> Feedtan Store</div>
        <p style="font-size:13.5px;line-height:1.7;max-width:280px;">Quality products at unbeatable prices, delivered right to your door — or ready when you walk in.</p>
      </div>
      <div>
        <h4>Shop</h4>
        <ul>
          <li><a href="{{ route('shop.index') }}">All Products</a></li>
          <li><a href="{{ route('shop.tracking') }}">Track Order</a></li>
        </ul>
      </div>
      <div>
        <h4>Support</h4>
        <ul>
          <li><a href="#" onclick="showToast('Reach us on +255 717 358 865','phone')">Contact us</a></li>
          <li><a href="#" onclick="showToast('Returns accepted within 48 hours of delivery','info')">Returns policy</a></li>
        </ul>
      </div>
      <div>
        <h4>Visit our store</h4>
        <ul>
          <li>Kiboriloni, Moshi, Kilimanjaro, Tanzania</li>
          <li>Open daily · 8:00 AM – 9:00 PM</li>
          <li>+255 717 358 865</li>
          <li>feedtanstore@gmail.com</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© {{ date('Y') }} Feedtan Store. All rights reserved.</span>
      <span>Built with care for everyday shoppers.</span>
    </div>
  </div>
</footer>

<div id="toast" class="toast" style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--green-900);color:#fff;padding:13px 22px;border-radius:999px;font-size:13.5px;font-weight:600;z-index:400;box-shadow:var(--shadow-pop);display:flex;align-items:center;gap:10px;opacity:0;visibility:hidden;transition:all .25s ease;"></div>

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

  const worldImageryLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles &copy; Esri'
  });

  osmLayer.addTo(checkoutMap);
  L.control.layers({
    'OpenStreetMap': osmLayer,
    'World Imagery': worldImageryLayer
  }).addTo(checkoutMap);

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

  const worldImageryLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles &copy; Esri'
  });

  osmLayer.addTo(checkoutMapOther);
  L.control.layers({
    'OpenStreetMap': osmLayer,
    'World Imagery': worldImageryLayer
  }).addTo(checkoutMapOther);

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

function normalizeCart(raw) {
  if (Array.isArray(raw)) return raw;
  if (raw && typeof raw === 'object') {
    return Object.entries(raw).map(([id, quantity]) => {
      return { id: String(id), name: 'Item', price: 0, quantity: Number(quantity) || 0 };
    }).filter(i => i.quantity > 0);
  }
  return [];
}

function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute('content') : '';
}

function showStep(stepKey) {
  const ids = ['stepDelivery', 'stepDeliveryActions', 'stepCustomer', 'stepCustomerActions', 'deliveryAddressSection', 'stepAddressActions', 'stepPayment', 'stepPaymentActions', 'stepSummary', 'placeOrderBtn'];
  const map = {
    delivery: ['stepDelivery', 'stepDeliveryActions'],
    customer: ['stepDelivery', 'stepDeliveryActions', 'stepCustomer', 'stepCustomerActions'],
    address: ['stepDelivery', 'stepDeliveryActions', 'stepCustomer', 'stepCustomerActions', 'deliveryAddressSection', 'stepAddressActions'],
    payment: ['stepDelivery', 'stepDeliveryActions', 'stepCustomer', 'stepCustomerActions', 'stepPayment', 'stepPaymentActions'],
    summary: ['stepDelivery', 'stepDeliveryActions', 'stepCustomer', 'stepCustomerActions', 'stepPayment', 'stepPaymentActions', 'stepSummary', 'placeOrderBtn']
  };
  ids.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    const visible = (map[stepKey] || []).includes(id);
    el.style.display = visible ? (id.endsWith('Actions') ? 'flex' : 'block') : 'none';
  });
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

  if (!name) { setFieldError('customerName', 'Full name is required'); ok = false; } else { setFieldError('customerName', ''); }
  if (!phone) { setFieldError('customerPhone', 'Phone number is required'); ok = false; } else { setFieldError('customerPhone', ''); }
  if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setFieldError('customerEmail', 'Enter a valid email'); ok = false; } else { setFieldError('customerEmail', ''); }
  return ok;
}

function validateAddressIfNeeded() {
  const needDelivery = document.querySelector('input[name="need_delivery"]:checked').value;
  if (needDelivery !== 'yes') {
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

// Initialize cart from localStorage
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
  const locationType = document.querySelector('input[name="location_type"]:checked')?.value;
  let lat = null, lng = null;

  if (needDelivery === 'yes') {
    if (locationType === 'current') {
      lat = userLocation.lat;
      lng = userLocation.lng;
    } else {
      lat = selectedLocation.lat;
      lng = selectedLocation.lng;
    }

    if (lat && lng) {
      const subtotal = calculateTotal();
      try {
        const response = await fetch('/api/shop/calculate-delivery-fee', {
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
        const data = await response.json();
        if (data.success) {
          currentDeliveryFee = data.delivery_fee;
          document.getElementById('deliveryFeeDisplay').textContent = data.is_free ? 'FREE' : data.formatted_delivery_fee;
          updateTotal();
        }
      } catch (e) {
        console.error('Failed to calculate delivery fee', e);
      }
    } else {
      currentDeliveryFee = 0;
      document.getElementById('deliveryFeeDisplay').textContent = 'Select location to calculate';
      updateTotal();
    }
  } else {
    currentDeliveryFee = 0;
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

// Select payment method
function selectPaymentMethod(method) {
  const payCash = document.getElementById('pay-cash');
  const payOnline = document.getElementById('pay-online');
  const payBank = document.getElementById('pay-bank');
  
  payCash.classList.toggle('selected', method === 'cash');
  payOnline.classList.toggle('selected', method === 'online');
  payBank.classList.toggle('selected', method === 'bank');
}

// Get user location
function detectLocation() {
  const statusEl = document.getElementById('locStatus');
  const coordsEl = document.getElementById('locCoords');
  const iconEl = statusEl.querySelector('svg');
  
  statusEl.classList.remove('pending', 'error');
  statusEl.querySelector('span').textContent = 'Detecting your location…';
  iconEl.setAttribute('viewBox', '0 0 24 24');
  iconEl.innerHTML = '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>';
  
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        userLocation.lat = position.coords.latitude;
        userLocation.lng = position.coords.longitude;
        
        statusEl.querySelector('span').textContent = 'Location captured!';
        coordsEl.textContent = `${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}`;
        document.getElementById('deliveryAddress').value = `Auto-detected location: ${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}`;
        setFieldError('deliveryAddress', '');
        updateCheckoutMap(userLocation.lat, userLocation.lng);
        fetchDeliveryFee();
      },
      (error) => {
        let errorMsg = 'Unable to get location.';
        switch(error.code) {
          case error.PERMISSION_DENIED:
            errorMsg = 'Permission denied. Please allow location access.';
            break;
          case error.POSITION_UNAVAILABLE:
            errorMsg = 'Location information unavailable.';
            break;
          case error.TIMEOUT:
            errorMsg = 'Location request timed out.';
            break;
        }
        statusEl.classList.add('error');
        statusEl.querySelector('span').textContent = errorMsg;
        document.getElementById('deliveryAddress').value = '';
      },
      {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 60000
      }
    );
  } else {
    statusEl.classList.add('error');
    statusEl.querySelector('span').textContent = 'Geolocation is not supported by your browser.';
  }
}

function showToast(msg, icon) {
  const toast = document.getElementById('toast');
  const icons = {
    heart:'<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7-4.6-9.5-9C0.7 8.6 2 5 5.3 4.2 7.5 3.6 9.6 4.8 12 7.5c2.4-2.7 4.5-3.9 6.7-3.3C22 5 23.3 8.6 21.5 12 19 16.4 12 21 12 21z"/></svg>',
    info:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>',
    phone:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
    cart:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>'
  };
  toast.innerHTML = (icons[icon] || icons.info) + '<span>'+msg+'</span>';
  toast.classList.add('show');
  toast.style.opacity = '1';
  toast.style.visibility = 'visible';
  toast.style.transform = 'translateX(-50%) translateY(0)';
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(() => {
    toast.classList.remove('show');
    toast.style.opacity = '0';
    toast.style.visibility = 'hidden';
    toast.style.transform = 'translateX(-50%) translateY(20px)';
  }, 2800);
}

function toggleMobileSearch() {
  const box = document.getElementById('mobileSearchBox');
  box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

function extractPaymentStatus(payload) {
  if (!payload) return null;
  if (payload.data && payload.data.status) return payload.data.status;
  if (payload.status) return payload.status;
  if (payload.data && payload.data.clickpesa_status) return payload.data.clickpesa_status;
  return null;
}

function formatPaymentStatus(status) {
  if (!status) return 'UNKNOWN';
  return String(status).toUpperCase();
}

function buildPaymentHtml(orderNumber, status, trackingUrl, pdfUrl) {
  const s = formatPaymentStatus(status);
  const note = (s === 'PENDING' || s === 'PROCESSING')
    ? 'Check your phone to confirm the USSD push.'
    : (s === 'SUCCESS' || s === 'SETTLED')
      ? 'Payment completed successfully.'
      : (s === 'FAILED' || s === 'DECLINED' || s === 'CANCELLED')
        ? 'Payment did not complete. You can try again or pay cash.'
        : 'Processing payment...';
  return 'Order number: <b>' + orderNumber + '</b><br>' +
    'Payment status: <b>' + s + '</b><br><span style="color:#6b7280;">' + note + '</span>' +
    '<div style="margin-top:10px;">' +
    '<a href="' + trackingUrl + '">Track your order</a> · <a href="' + pdfUrl + '">Download order PDF</a>' +
    '</div>';
}

function openPaymentProgressModal(orderNumber, trackingUrl, pdfUrl) {
  return new Promise((resolve) => {
    if (!window.Swal) {
      resolve({ result: 'no_swal' });
      return;
    }

    let intervalId = null;
    let timeoutId = null;
    let finalStatus = null;

    const stop = () => {
      if (intervalId) {
        clearInterval(intervalId);
        intervalId = null;
      }
      if (timeoutId) {
        clearTimeout(timeoutId);
        timeoutId = null;
      }
    };

    const finish = (status) => {
      finalStatus = formatPaymentStatus(status);
      stop();
      const success = finalStatus === 'SUCCESS' || finalStatus === 'SETTLED';
      const failed = ['FAILED', 'DECLINED', 'CANCELLED'].includes(finalStatus);

      Swal.hideLoading();
      Swal.update({
        icon: success ? 'success' : (failed ? 'error' : 'info'),
        title: success ? 'Payment successful' : (failed ? 'Payment failed' : 'Payment status'),
        html: buildPaymentHtml(orderNumber, finalStatus, trackingUrl, pdfUrl),
        showConfirmButton: true,
        confirmButtonText: success ? 'Continue' : 'Close',
        showCancelButton: false
      });
    };

    Swal.fire({
      title: 'Processing mobile money payment',
      html: buildPaymentHtml(orderNumber, 'PENDING', trackingUrl, pdfUrl),
      allowOutsideClick: false,
      showCancelButton: true,
      cancelButtonText: 'Pay later',
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
        const startMs = Date.now();
        timeoutId = setTimeout(() => {
          stop();
          Swal.hideLoading();
          Swal.update({
            icon: 'info',
            title: 'Payment window ended',
            html: buildPaymentHtml(orderNumber, 'PENDING', trackingUrl, pdfUrl) + '<div style="margin-top:8px;color:#6b7280;">Payment status check stopped after 1 minute.</div>',
            showConfirmButton: true,
            confirmButtonText: 'Track Order',
            showCancelButton: true,
            cancelButtonText: 'Close'
          });
        }, 60000);

        intervalId = setInterval(async () => {
          try {
            const res = await fetch('/api/shop/orders/' + encodeURIComponent(orderNumber) + '/payment-status', {
              method: 'GET',
              headers: { 'Accept': 'application/json' },
              credentials: 'same-origin'
            });
            const payload = await res.json().catch(() => ({}));
            const status = extractPaymentStatus(payload);

            if (!status) {
              const extra = payload && payload.message ? ('<div style="margin-top:8px;color:#6b7280;">' + payload.message + '</div>') : '';
              const elapsed = Math.floor((Date.now() - startMs) / 1000);
              const remaining = Math.max(0, 60 - elapsed);
              const timer = '<div style="margin-top:8px;color:#6b7280;">Time remaining: ' + remaining + 's</div>';
              Swal.update({ html: buildPaymentHtml(orderNumber, 'PROCESSING', trackingUrl, pdfUrl) + timer + extra });
              return;
            }

            const normalized = formatPaymentStatus(status);
            const elapsed = Math.floor((Date.now() - startMs) / 1000);
            const remaining = Math.max(0, 60 - elapsed);
            const timer = '<div style="margin-top:8px;color:#6b7280;">Time remaining: ' + remaining + 's</div>';
            Swal.update({ html: buildPaymentHtml(orderNumber, normalized, trackingUrl, pdfUrl) + timer });

            if (normalized === 'SUCCESS' || normalized === 'SETTLED' || ['FAILED', 'DECLINED', 'CANCELLED'].includes(normalized)) {
              finish(normalized);
            }
          } catch (e) {
            const elapsed = Math.floor((Date.now() - startMs) / 1000);
            const remaining = Math.max(0, 60 - elapsed);
            const timer = '<div style="margin-top:8px;color:#6b7280;">Time remaining: ' + remaining + 's</div>';
            Swal.update({ html: buildPaymentHtml(orderNumber, 'PROCESSING', trackingUrl, pdfUrl) + timer });
          }
        }, 3000);
      },
      willClose: () => stop()
    }).then((modalResult) => {
      stop();
      resolve({ result: modalResult, status: finalStatus });
    });
  });
}

function openOrderProcessingModal() {
  if (!window.Swal) return { close() {} };

  const stages = [
    'Validating your order details...',
    'Saving your order securely...',
    'Preparing notifications and tracking...'
  ];
  let stageIndex = 0;
  let stageTimer = null;

  Swal.fire({
    title: 'Processing your order',
    html: '<div id="orderProcessingStatus" style="color:#6b7280;">' + stages[0] + '</div>',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
      stageTimer = setInterval(() => {
        stageIndex = (stageIndex + 1) % stages.length;
        const statusEl = document.getElementById('orderProcessingStatus');
        if (statusEl) statusEl.textContent = stages[stageIndex];
      }, 1300);
    },
    willClose: () => {
      if (stageTimer) clearInterval(stageTimer);
    }
  });

  return {
    close() {
      if (stageTimer) clearInterval(stageTimer);
      Swal.close();
    }
  };
}

document.getElementById('btnNextDelivery').addEventListener('click', () => {
  showStep('customer');
  document.getElementById('customerName').focus();
});

document.getElementById('btnBackCustomer').addEventListener('click', () => showStep('delivery'));
document.getElementById('btnNextCustomer').addEventListener('click', () => {
  if (!validateCustomer()) {
    if (window.Swal) Swal.fire({ icon: 'error', title: 'Please fill required fields', text: 'Check your customer information.' });
    return;
  }
  const needDelivery = document.querySelector('input[name="need_delivery"]:checked').value;
  if (needDelivery === 'yes') {
    showStep('address');
    document.getElementById('locStatus').scrollIntoView({ behavior: 'smooth', block: 'center' });
  } else {
    showStep('payment');
  }
});

document.getElementById('btnBackAddress').addEventListener('click', () => showStep('customer'));
document.getElementById('btnNextAddress').addEventListener('click', () => {
  if (!validateAddressIfNeeded()) {
    detectLocation();
    if (window.Swal) Swal.fire({ icon: 'error', title: 'Location required', text: 'Please allow location access to continue with delivery.' });
    return;
  }
  showStep('payment');
});

document.getElementById('btnBackPayment').addEventListener('click', () => {
  const needDelivery = document.querySelector('input[name="need_delivery"]:checked').value;
  showStep(needDelivery === 'yes' ? 'address' : 'customer');
});
document.getElementById('btnNextPayment').addEventListener('click', () => showStep('summary'));

['customerName','customerPhone','customerEmail'].forEach(id => {
  const el = document.getElementById(id);
  if (el) el.addEventListener('blur', validateCustomer);
});

document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const needDelivery = document.querySelector('input[name="need_delivery"]:checked').value;
  
  if (!validateCustomer()) {
    if (window.Swal) Swal.fire({ icon: 'error', title: 'Missing information', text: 'Please complete customer information.' });
    document.getElementById('customerName').focus();
    return;
  }
  if (!validateAddressIfNeeded()) {
    const locationType = document.querySelector('input[name="location_type"]:checked').value;
    if (locationType === 'current') {
      detectLocation();
    }
    if (window.Swal) Swal.fire({ icon: 'error', title: 'Location required', text: 'Please complete location information for delivery.' });
    return;
  }

  // Determine which location to use
  const locationType = document.querySelector('input[name="location_type"]:checked').value;
  const finalLocation = locationType === 'current' ? userLocation : selectedLocation;
  
  const placeOrderBtn = document.getElementById('placeOrderBtn');
  placeOrderBtn.disabled = true;
  placeOrderBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Processing...';
  const processingModal = openOrderProcessingModal();
  
  const items = cart.map(item => ({ product_id: item.id, quantity: item.quantity }));
  const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
  
  const orderData = {
    customer_name: document.getElementById('customerName').value,
    customer_phone: document.getElementById('customerPhone').value,
    customer_email: document.getElementById('customerEmail').value,
    delivery_address: needDelivery === 'yes' ? (document.getElementById('deliveryAddress').value || '') : 'Store Pickup',
    delivery_latitude: finalLocation.lat,
    delivery_longitude: finalLocation.lng,
    payment_method: paymentMethod,
    delivery_fee: needDelivery === 'yes' ? currentDeliveryFee : 0,
    items: items
  };
  
  try {
    const response = await fetch('/api/shop/orders', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      credentials: 'same-origin',
      body: JSON.stringify(orderData)
    });

    const data = await response.json().catch(() => ({}));
    processingModal.close();

    if (!response.ok) {
      const message = data && data.message ? data.message : 'Failed to place order. Please check your details and try again.';
      if (window.Swal) {
        await Swal.fire({ icon: 'error', title: 'Order not submitted', text: message });
      } else {
        alert(message);
      }
      placeOrderBtn.disabled = false;
      placeOrderBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> Place Order';
      return;
    }

    if (data.success) {
      cart = [];
      localStorage.setItem('shopCart', JSON.stringify(cart));
      const trackingUrl = data.tracking_url || `/shop/tracking/${data.order_number}`;
      const pdfUrl = data.pdf_url || `/shop/tracking/${data.order_number}/pdf`;
      if (paymentMethod === 'online') {
        if (!data.payment_initiated) {
          const paymentNote = data.payment_message || 'Payment request could not be started right now. You can track the order and pay later.';
          if (window.Swal) {
            await Swal.fire({
              icon: 'info',
              title: 'Order saved, payment pending',
              html: 'Order number: <b>' + data.order_number + '</b><br>' + paymentNote + '<br><br><a href="' + trackingUrl + '">Track your order</a> · <a href="' + pdfUrl + '">Download PDF</a>',
              confirmButtonText: 'Track Order',
              showCancelButton: true,
              cancelButtonText: 'Close'
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = trackingUrl;
              }
            });
          } else {
            alert(paymentNote);
          }
          placeOrderBtn.disabled = false;
          placeOrderBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> Place Order';
          return;
        }
        const outcome = await openPaymentProgressModal(data.order_number, trackingUrl, pdfUrl);
        const finalStatus = outcome && outcome.status ? String(outcome.status).toUpperCase() : null;
        if (finalStatus === 'SUCCESS' || finalStatus === 'SETTLED' || !window.Swal) {
          window.location.href = trackingUrl;
          return;
        }
        placeOrderBtn.disabled = false;
        placeOrderBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> Place Order';
        return;
      }
      if (window.Swal) {
        const result = await Swal.fire({
          icon: 'success',
          title: 'Order placed successfully',
          html: 'Order number: <b>' + data.order_number + '</b><br><a href="' + trackingUrl + '">Track your order</a> · <a href="' + pdfUrl + '">Download PDF</a>',
          confirmButtonText: 'Track Order',
          showCancelButton: true,
          cancelButtonText: 'Close'
        });
        if (result.isConfirmed) {
          window.location.href = trackingUrl;
          return;
        }
      }
      window.location.href = trackingUrl;
      return;
    }
  } catch (err) {
    console.error(err);
    processingModal.close();
    placeOrderBtn.disabled = false;
    placeOrderBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> Place Order';
    if (window.Swal) {
      Swal.fire({ icon: 'error', title: 'Network error', text: 'Failed to place order. Please try again.' });
    } else {
      alert('Failed to place order. Please try again.');
    }
  }
});

function hidePageLoader() {
  const loader = document.getElementById('pageLoader');
  if (!loader) return;
  loader.classList.add('hidden');
}

initCart();
if (document.getElementById('checkoutForm') && document.getElementById('checkoutForm').style.display !== 'none') {
  toggleDeliveryOptions();
  // Don't auto-detect location anymore since user can choose between current and other location
}

setTimeout(hidePageLoader, 350);
window.addEventListener('load', hidePageLoader);
</script>
</body>
</html>
