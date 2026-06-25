<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Track Order - Feedtan Store</title>
<meta name="description" content="Track your order with Feedtan Store">
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
.header-inner{display:flex;align-items:center;gap:20px;padding:14px 24px;}
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
  width:100%;height:320px;border-radius:var(--radius-m);overflow:hidden;border:1px solid var(--line);
  background:linear-gradient(135deg,var(--green-100),var(--parchment-dim));
  display:flex;align-items:center;justify-content:center;flex-direction:column;
  font-size:14px;color:var(--ink-soft);
}
.map-placeholder{
  text-align:center;
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
</style>
</head>
<body>

<div class="topbar">
  <div class="wrap">
    <div class="topbar-msg">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
      <span>Free delivery on orders over TZS 50,000</span>
    </div>
    <div class="topbar-msg" id="topbarPhone">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
      <span>+255 700 000 000</span>
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
      <div class="card" id="orderDetails">
        <h2 style="margin-bottom:8px;">Order #{{ $order->order_number }}</h2>
        <p style="margin:0 0 16px 0;color:var(--ink-soft);font-size:14px;">Placed on {{ $order->created_at->format('M d, Y • h:i A') }}</p>

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

        @if($order->delivery_address)
        <div style="margin-top:24px;">
          <h3 style="margin-bottom:16px;">Delivery Address</h3>
          <div class="card" style="margin-bottom:0;background:var(--parchment);">
            <p style="margin:0;font-size:14.5px;">{{ $order->delivery_address }}</p>
            @if($order->delivery_latitude && $order->delivery_longitude)
            <div class="map-container" style="margin-top:16px;">
              <div class="map-placeholder">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--green-700)" stroke-width="1.5" style="margin-bottom:8px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                <p style="margin:0;">Location: {{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}</p>
              </div>
            </div>
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
          <li><a href="#" onclick="showToast('Reach us on +255 700 000 000','phone')">Contact us</a></li>
          <li><a href="#" onclick="showToast('Returns accepted within 48 hours of delivery','info')">Returns policy</a></li>
        </ul>
      </div>
      <div>
        <h4>Visit our store</h4>
        <ul>
          <li>Mlimani City Road, Dar es Salaam</li>
          <li>Open daily · 8:00 AM – 9:00 PM</li>
          <li>+255 700 000 000</li>
          <li>hello@feedtan.store</li>
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

document.getElementById('trackForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const orderNumber = document.getElementById('orderNumber').value.trim();
  if (orderNumber) {
    window.location.href = `{{ route('shop.tracking') }}?order=${encodeURIComponent(orderNumber)}`;
  }
});
</script>
</body>
</html>
