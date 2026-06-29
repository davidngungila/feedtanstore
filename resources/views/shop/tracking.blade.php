<!DOCTYPE html>
<html lang="en">
<head>
@php
  $logoUrl = asset('logo-image-feedtan-store.png');
  $trackingCanonicalUrl = request()->fullUrl();
  $trackingTitle = isset($order)
      ? 'Track Order ' . $order->order_number . ' - Feedtan Store'
      : 'Track Order - Feedtan Store';
  $trackingDescription = isset($order)
      ? 'Track delivery updates, payment status, and order progress for ' . $order->order_number . ' at Feedtan Store.'
      : 'Track your Feedtan Store order status, delivery progress, and payment updates online.';
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>{{ $trackingTitle }}</title>
<meta name="description" content="{{ $trackingDescription }}">
<meta name="robots" content="noindex,nofollow,noarchive">
<meta name="author" content="Feedtan Store">
<meta name="theme-color" content="#1B4332">
<link rel="canonical" href="{{ $trackingCanonicalUrl }}">
<link rel="icon" type="image/png" href="{{ $logoUrl }}">
<link rel="apple-touch-icon" href="{{ $logoUrl }}">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="Feedtan Store">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $trackingTitle }}">
<meta property="og:description" content="{{ $trackingDescription }}">
<meta property="og:url" content="{{ $trackingCanonicalUrl }}">
<meta property="og:image" content="{{ $logoUrl }}">
<meta property="og:image:secure_url" content="{{ $logoUrl }}">
<meta property="og:image:type" content="image/png">
<meta property="og:image:alt" content="Feedtan Store logo">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $trackingTitle }}">
<meta name="twitter:description" content="{{ $trackingDescription }}">
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

.field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px;}
.field label{font-size:12.5px;font-weight:700;color:var(--ink-soft);}
.field input, .field select, .field textarea{
  border:1.5px solid var(--line);border-radius:var(--radius-s);padding:11px 13px;font-size:14px;color:var(--ink);
  background:var(--white);outline:none;transition:border-color .15s;width:100%;
}
.field input:focus, .field select:focus, .field textarea:focus{border-color:var(--green-700);}

.timeline{
  position:relative;padding-left:30px;margin:20px 0;
}
.timeline::before{
  content:'';position:absolute;left:9px;top:0;bottom:0;width:2px;background:var(--line);
}
.timeline-item{
  position:relative;margin-bottom:20px;
}
.timeline-dot{
  position:absolute;left:-29px;top:3px;width:20px;height:20px;border-radius:50%;background:var(--green-100);border:3px solid var(--green-700);
}
.timeline-item.completed .timeline-dot{
  background:var(--green-700);border-color:var(--green-900);
}
.timeline-time{font-family:var(--font-mono);font-size:12.5px;color:var(--ink-soft);margin-bottom:4px;}
.timeline-title{font-weight:700;font-size:14.5px;margin-bottom:2px;}
.timeline-desc{font-size:13px;color:var(--ink-soft);}

.order-summary{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin:16px 0;
}
.order-summary .stat{
  padding:14px;background:var(--parchment);border-radius:var(--radius-m);
}
.order-summary .stat .label{font-size:12.5px;font-weight:600;color:var(--ink-soft);margin-bottom:4px;}
.order-summary .stat .value{font-size:17px;font-weight:800;}

.map-container{
  width:100%;height:400px;border-radius:var(--radius-m);overflow:hidden;border:1px solid var(--line);
}
.map-container .leaflet-control-container .leaflet-control{border-radius:10px;overflow:hidden;}
.map-container .leaflet-control-layers,
.map-container .leaflet-bar{
  box-shadow:0 8px 22px rgba(15,42,31,0.12);
}

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
      <a href="{{ route('shop.tracking') }}" class="active">Track Order</a>
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
          <span class="eyebrow">Track</span>
          <h1>Track your order</h1>
        </div>
      </div>

      <div class="card">
        <form id="trackForm">
          <div class="field">
            <label for="orderNumber">Order Number</label>
            <input type="text" id="orderNumber" placeholder="Enter your order number" value="{{ request('order', '') }}">
          </div>
          <button type="submit" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Track Order
          </button>
        </form>
      </div>

      @if(isset($order))
      <script>
        window.shortCustomerReference = @json($order->short_customer_reference);
      </script>
      <div class="card" id="orderDetails">
        <h2 style="margin-bottom:8px;">Order {{ $order->short_customer_reference }}</h2>
        <p style="margin:0 0 16px 0;color:var(--ink-soft);font-size:14px;">Placed on {{ $order->created_at->format('M d, Y • h:i A') }}</p>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin:0 0 16px 0;">
          <a href="{{ route('shop.tracking.pdf', ['orderNumber' => $order->tracking_token ?? $order->order_number]) }}" class="btn btn-ghost">Download PDF</a>
          @if(($order->payment_method ?? 'cash') === 'online' && ($order->payment_status ?? 'pending') !== 'paid')
            <button type="button" class="btn btn-primary" id="payNowBtn" data-order="{{ $order->order_number }}" data-phone="{{ $order->customer_phone }}">Pay Now</button>
          @endif
        </div>

        <div class="order-summary">
          <div class="stat">
            <div class="label">Status</div>
            <div class="value" style="color:var(--green-700);">{{ ucfirst($order->status) }}</div>
          </div>
          <div class="stat">
            <div class="label">Customer</div>
            <div class="value">{{ $order->customer_name }}</div>
          </div>
          <div class="stat">
            <div class="label">Total</div>
            <div class="value">TZS {{ number_format($order->total, 0) }}</div>
          </div>
          <div class="stat">
            <div class="label">Payment Method</div>
            <div class="value">{{ ucfirst($order->payment_method ?? 'Cash') }}</div>
          </div>
        </div>

        <div style="margin-top:24px;">
          <h3 style="margin-bottom:16px;">Order Timeline</h3>
          <div class="timeline">
            <div class="timeline-item completed">
              <div class="timeline-dot"></div>
              <div class="timeline-time">{{ $order->created_at->format('M d, h:i A') }}</div>
              <div class="timeline-title">Order Placed</div>
              <div class="timeline-desc">Your order has been placed successfully.</div>
            </div>

            @if(in_array($order->status, ['confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered']))
            <div class="timeline-item completed">
              <div class="timeline-dot"></div>
              <div class="timeline-time">{{ $order->created_at->format('M d, h:i A') }}</div>
              <div class="timeline-title">Order Confirmed</div>
              <div class="timeline-desc">We've received and confirmed your order.</div>
            </div>
            @endif

            @if(in_array($order->status, ['preparing', 'ready', 'out_for_delivery', 'delivered']))
            <div class="timeline-item completed">
              <div class="timeline-dot"></div>
              <div class="timeline-time">{{ $order->created_at->addMinutes(30)->format('M d, h:i A') }}</div>
              <div class="timeline-title">Preparing Order</div>
              <div class="timeline-desc">Your order is being prepared.</div>
            </div>
            @endif

            @if(in_array($order->status, ['ready', 'out_for_delivery', 'delivered']))
            <div class="timeline-item completed">
              <div class="timeline-dot"></div>
              <div class="timeline-time">{{ $order->created_at->addMinutes(60)->format('M d, h:i A') }}</div>
              <div class="timeline-title">Ready for Delivery</div>
              <div class="timeline-desc">Your order is ready to be delivered.</div>
            </div>
            @endif

            @if(in_array($order->status, ['out_for_delivery', 'delivered']))
            <div class="timeline-item completed">
              <div class="timeline-dot"></div>
              <div class="timeline-time">{{ $order->created_at->addMinutes(90)->format('M d, h:i A') }}</div>
              <div class="timeline-title">Out for Delivery</div>
              <div class="timeline-desc">Your order is on its way to you.</div>
            </div>
            @endif

            @if($order->status === 'delivered')
            <div class="timeline-item completed">
              <div class="timeline-dot"></div>
              <div class="timeline-time">{{ $order->updated_at->format('M d, h:i A') }}</div>
              <div class="timeline-title">Delivered</div>
              <div class="timeline-desc">Your order has been delivered. Thank you!</div>
            </div>
            @endif
          </div>
        </div>

        @if($order->delivery_address || ($order->delivery_latitude && $order->delivery_longitude))
        <div style="margin-top:24px;">
          <h3 style="margin-bottom:16px;">Delivery Location & Route</h3>
          <div class="card" style="margin-bottom:0;background:var(--parchment);">
            @if($order->delivery_address)
            <p style="margin:0;font-size:14.5px;">{{ $order->delivery_address }}</p>
            @else
            <p style="margin:0;font-size:14.5px;color:var(--ink-soft);">Location captured from customer device.</p>
            @endif
            @if($order->delivery_latitude && $order->delivery_longitude)
            <div class="map-container" style="margin-top:16px;">
              <div id="tracking-map" style="width:100%;height:100%;"></div>
            </div>
            <p style="margin:12px 0 0;font-size:13px;color:var(--ink-soft);">Location: {{ number_format($order->delivery_latitude, 6) }}, {{ number_format($order->delivery_longitude, 6) }}</p>
            @endif
          </div>
        </div>
        @endif
      </div>
      @endif
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

@if($order && $order->delivery_latitude && $order->delivery_longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
@endif
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if($order && $order->delivery_latitude && $order->delivery_longitude)
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endif
<script>
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

function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute('content') : '';
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

function buildPaymentHtml(orderNumber, status, trackingUrl, pdfUrl, remainingSeconds) {
  const s = formatPaymentStatus(status);
  const note = (s === 'PENDING' || s === 'PROCESSING')
    ? 'Check your phone to confirm the USSD push.'
    : (s === 'SUCCESS' || s === 'SETTLED')
      ? 'Payment completed successfully.'
      : (s === 'FAILED' || s === 'DECLINED' || s === 'CANCELLED')
        ? 'Payment did not complete. You can try again later.'
        : 'Processing payment...';
  const timer = typeof remainingSeconds === 'number' ? ('<div style="margin-top:8px;color:#6b7280;">Time remaining: ' + remainingSeconds + 's</div>') : '';
  return 'Order number: <b>' + (window.shortCustomerReference || orderNumber) + '</b><br>' +
    'Payment status: <b>' + s + '</b><br><span style="color:#6b7280;">' + note + '</span>' +
    timer +
    '<div style="margin-top:10px;">' +
    '<a href="' + trackingUrl + '">Track your order</a> · <a href="' + pdfUrl + '">Download order PDF</a>' +
    '</div>';
}

async function initiatePayment(trackingIdentifier, phoneNumber = '') {
  const bodyPayload = {};
  if (phoneNumber) {
    bodyPayload.phone_number = phoneNumber;
  }
  const res = await fetch('/api/shop/orders/' + encodeURIComponent(trackingIdentifier) + '/initiate-payment', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': getCsrfToken()
    },
    credentials: 'same-origin',
    body: JSON.stringify(bodyPayload)
  });
  const payload = await res.json().catch(() => ({}));
  if (!res.ok) {
    const message = payload && payload.message ? payload.message : 'Failed to initiate payment.';
    throw new Error(message);
  }
  return payload;
}

async function promptPaymentPhoneNumber(defaultPhone = '') {
  if (window.Swal) {
    const result = await Swal.fire({
      title: 'Choose payment number',
      input: 'text',
      inputValue: defaultPhone || '',
      inputLabel: 'Phone number',
      inputPlaceholder: '255712345678',
      confirmButtonText: 'Continue',
      showCancelButton: true,
      inputValidator: (value) => {
        if (!value || !value.trim()) {
          return 'Enter the number to receive the USSD prompt.';
        }
        const digits = value.replace(/\D+/g, '');
        if (!(digits.length === 12 && digits.startsWith('255')) && !(digits.length === 10 && digits.startsWith('0')) && !(digits.length === 9 && digits.startsWith('7'))) {
          return 'Use a valid mobile money number like 255712345678.';
        }
        return null;
      }
    });

    if (!result.isConfirmed) {
      return null;
    }

    return result.value.trim();
  }

  const fallback = window.prompt('Enter the number to receive the USSD prompt', defaultPhone || '');
  return fallback ? fallback.trim() : null;
}

function openPaymentProgressModal(trackingIdentifier, trackingUrl, pdfUrl) {
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
        html: buildPaymentHtml(trackingIdentifier, finalStatus, trackingUrl, pdfUrl),
        showConfirmButton: true,
        confirmButtonText: success ? 'Continue' : 'Close',
        showCancelButton: false
      });
    };

    Swal.fire({
      title: 'Processing mobile money payment',
      html: buildPaymentHtml(trackingIdentifier, 'PENDING', trackingUrl, pdfUrl, 60),
      allowOutsideClick: false,
      showCancelButton: true,
      cancelButtonText: 'Close',
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
            html: buildPaymentHtml(trackingIdentifier, 'PENDING', trackingUrl, pdfUrl, 0) + '<div style="margin-top:8px;color:#6b7280;">Payment status check stopped after 1 minute.</div>',
            showConfirmButton: true,
            confirmButtonText: 'OK',
            showCancelButton: false
          });
        }, 60000);

        intervalId = setInterval(async () => {
          try {
            const res = await fetch('/api/shop/orders/' + encodeURIComponent(trackingIdentifier) + '/payment-status', {
              method: 'GET',
              headers: { 'Accept': 'application/json' },
              credentials: 'same-origin'
            });
            const payload = await res.json().catch(() => ({}));
            const status = extractPaymentStatus(payload);
            const elapsed = Math.floor((Date.now() - startMs) / 1000);
            const remaining = Math.max(0, 60 - elapsed);

            if (!status) {
              Swal.update({ html: buildPaymentHtml(trackingIdentifier, 'PROCESSING', trackingUrl, pdfUrl, remaining) });
              return;
            }

            const normalized = formatPaymentStatus(status);
            Swal.update({ html: buildPaymentHtml(trackingIdentifier, normalized, trackingUrl, pdfUrl, remaining) });

            if (normalized === 'SUCCESS' || normalized === 'SETTLED' || ['FAILED', 'DECLINED', 'CANCELLED'].includes(normalized)) {
              finish(normalized);
            }
          } catch (e) {}
        }, 3000);
      },
      willClose: () => stop()
    }).then((modalResult) => {
      stop();
      resolve({ result: modalResult, status: finalStatus });
    });
  });
}

document.getElementById('trackForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const orderNumber = document.getElementById('orderNumber').value.trim();
  if (orderNumber) {
    window.location.href = `{{ route('shop.tracking') }}?order=${encodeURIComponent(orderNumber)}`;
  }
});

@if($order)
const payNowBtn = document.getElementById('payNowBtn');
if (payNowBtn) {
            payNowBtn.addEventListener('click', async () => {
                const orderNumber = payNowBtn.getAttribute('data-order');
                const defaultPhone = payNowBtn.getAttribute('data-phone') || '';
                const baseUrl = @json($settings->store_url ?? config('app.url'));
                const trackingIdentifier = @json($order->tracking_token ?? $order->order_number);
                const trackingUrl = `${baseUrl}/shop/tracking/${encodeURIComponent(trackingIdentifier)}`;
                const pdfUrl = `${baseUrl}/shop/tracking/${encodeURIComponent(trackingIdentifier)}/pdf`;
    try {
      const phoneNumber = await promptPaymentPhoneNumber(defaultPhone);
      if (!phoneNumber) {
        return;
      }
      if (window.Swal) {
        Swal.fire({ title: 'Starting payment', text: 'Sending USSD push to your phone...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
      }
      await initiatePayment(trackingIdentifier, phoneNumber);
      if (window.Swal) Swal.close();
      await openPaymentProgressModal(trackingIdentifier, trackingUrl, pdfUrl);
    } catch (e) {
      if (window.Swal) Swal.fire({ icon: 'error', title: 'Payment not started', text: e.message || 'Failed to initiate payment.' });
    }
  });

  const params = new URLSearchParams(window.location.search);
  if (params.get('pay') === '1') {
    setTimeout(() => payNowBtn.click(), 300);
  }
}
@endif

@if($order && $order->delivery_latitude && $order->delivery_longitude)
const trackingStoreLat = {{ $settings->store_latitude ?? -3.3869 }};
const trackingStoreLng = {{ $settings->store_longitude ?? 36.6883 }};
const trackingOrderLat = {{ $order->delivery_latitude }};
const trackingOrderLng = {{ $order->delivery_longitude }};
const trackingRoute = @json($route);

const trackingMap = L.map('tracking-map').setView([(trackingStoreLat + trackingOrderLat) / 2, (trackingStoreLng + trackingOrderLng) / 2], 12);

const trackingOsmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
});

const trackingImageryLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
  attribution: 'Tiles &copy; Esri'
});

trackingOsmLayer.addTo(trackingMap);
L.control.layers({
  'OpenStreetMap': trackingOsmLayer,
  'World Imagery': trackingImageryLayer
}).addTo(trackingMap);

L.marker([trackingStoreLat, trackingStoreLng])
  .addTo(trackingMap)
  .bindPopup('<strong>Store</strong>');

L.circleMarker([trackingOrderLat, trackingOrderLng], {
  radius: 8,
  fillColor: '#f97316',
  color: '#fff',
  weight: 2,
  fillOpacity: 0.85
})
  .addTo(trackingMap)
  .bindPopup('<strong>Delivery location</strong><br>{{ addslashes($order->customer_name) }}<br>{{ addslashes($order->delivery_address) }}');

if (trackingRoute && trackingRoute.features && trackingRoute.features.length > 0) {
  const routePoints = trackingRoute.features[0].geometry.coordinates.map(point => [point[1], point[0]]);
  L.polyline(routePoints, { color: '#3b82f6', weight: 4, opacity: 0.75 }).addTo(trackingMap);
  trackingMap.fitBounds(routePoints, { padding: [36, 36] });
} else {
  const bounds = L.latLngBounds(
    [trackingStoreLat, trackingStoreLng],
    [trackingOrderLat, trackingOrderLng]
  );
  trackingMap.fitBounds(bounds, { padding: [36, 36] });
}

setTimeout(() => trackingMap.invalidateSize(), 150);
@endif

function hidePageLoader() {
  const loader = document.getElementById('pageLoader');
  if (!loader) return;
  loader.classList.add('hidden');
}

setTimeout(hidePageLoader, 350);
window.addEventListener('load', hidePageLoader);
</script>
</body>
</html>
