<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>{{ $product->name }} - Feedtan Store</title>
<meta name="description" content="{{ $product->description ?? 'Discover quality products at unbeatable prices, delivered right to your door.' }}">
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

.pill{display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:700;padding:5px 12px;border-radius:999px;letter-spacing:.02em;}
.pill-green{background:var(--green-100);color:var(--green-700);}
.pill-orange{background:#FCEADB;color:var(--orange-dark);}
.pill-red{background:var(--red-dim);color:var(--red);}

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

.section{padding:54px 0;}
.section-head{display:flex;align-items:baseline;justify-content:space-between;gap:16px;margin-bottom:28px;flex-wrap:wrap;}
.section-head h2{font-size:clamp(24px,3vw,32px);font-weight:700;}
.section-head .eyebrow{display:block;font-family:var(--font-body);font-size:12.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--orange-dark);margin-bottom:6px;}
.section-head p{color:var(--ink-soft);font-size:14.5px;margin:6px 0 0;}
.back-link{display:inline-flex;align-items:center;gap:6px;font-size:13.5px;font-weight:700;color:var(--green-700);}

.pd-grid{display:grid;grid-template-columns:1fr 1fr;gap:40px;}
@media(max-width:900px){.pd-grid{grid-template-columns:1fr;}}
.pd-img{border-radius:var(--radius-l);aspect-ratio:1/1;object-fit:cover;width:100%;background:var(--parchment-dim);}
.pd-cat{font-size:12px;font-weight:700;color:var(--green-700);text-transform:uppercase;letter-spacing:.04em;}
.pd-title{font-size:36px;margin:6px 0 16px;line-height:1.1;}
.pd-price{font-family:var(--font-display);font-size:32px;font-weight:700;margin:10px 0;}
.pd-price-old{font-size:18px;color:#A39E8C;text-decoration:line-through;font-weight:500;}
.pd-desc{font-size:16px;color:var(--ink-soft);line-height:1.7;margin-bottom:24px;}
.pd-meta-list{display:flex;flex-direction:column;gap:8px;font-size:14px;margin-bottom:24px;}
.pd-meta-list li{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--parchment-dim);list-style:none;}
.pd-meta-list li span:first-child{color:var(--ink-soft);}
.pd-actions{display:flex;gap:12px;align-items:stretch;margin-bottom:20px;}
.pd-actions .qty-stepper{flex:0 0 140px;}
.qty-stepper{display:flex;align-items:center;border:1.5px solid var(--line);border-radius:999px;overflow:hidden;flex:1;background:#fff;}
.qty-stepper button{width:40px;height:48px;background:transparent;border:none;font-size:18px;font-weight:700;color:var(--green-700);}
.qty-stepper button:hover{background:var(--green-100);}
.qty-stepper span{flex:1;text-align:center;font-weight:700;font-size:16px;}

.pd-thumbs{display:flex;gap:12px;margin-top:16px;overflow-x:auto;padding-bottom:4px;}
.pd-thumb{width:80px;height:80px;border-radius:var(--radius-m);border:2px solid var(--line);background:var(--parchment-dim);cursor:pointer;transition:border-color .15s;overflow:hidden;flex-shrink:0;}
.pd-thumb img{width:100%;height:100%;object-fit:cover;}
.pd-thumb:hover, .pd-thumb.active{border-color:var(--green-700);}

.stock-low{font-size:14px;color:var(--red);font-weight:700;}

footer{background:var(--green-900);color:#BFD6C8;padding:54px 0 0;margin-top:40px;}
.footer-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1.2fr;gap:34px;padding-bottom:40px;}
.footer-grid h4{color:#fff;font-family:var(--font-body);font-size:13.5px;letter-spacing:.04em;text-transform:uppercase;margin-bottom:16px;}
.footer-grid ul{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;font-size:13.5px;}
.footer-grid ul a:hover{color:#fff;}
.footer-logo{display:flex;align-items:center;gap:10px;color:#fff;font-family:var(--font-display);font-weight:800;font-size:20px;margin-bottom:12px;}
.footer-bottom{border-top:1px solid rgba(255,255,255,0.1);padding:18px 24px;display:flex;justify-content:space-between;font-size:12.5px;flex-wrap:wrap;gap:8px;}
@media(max-width:760px){.footer-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:480px){.footer-grid{grid-template-columns:1fr;}}

.scrim{position:fixed;inset:0;background:rgba(13,27,18,0.55);z-index:200;opacity:0;visibility:hidden;transition:opacity .25s ease;}
.scrim.open{opacity:1;visibility:visible;}

.cart-drawer{
  position:fixed;top:0;right:0;height:100%;width:420px;max-width:92vw;background:#fff;z-index:210;
  box-shadow:-12px 0 40px rgba(0,0,0,0.18);transform:translateX(100%);transition:transform .3s ease;
  display:flex;flex-direction:column;
}
.cart-drawer.open{transform:translateX(0);}
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

.toast{
  position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--green-900);color:#fff;
  padding:13px 22px;border-radius:999px;font-size:13.5px;font-weight:600;z-index:400;box-shadow:var(--shadow-pop);
  display:flex;align-items:center;gap:10px;opacity:0;visibility:hidden;transition:all .25s ease;
}
.toast.show{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0);}
.toast svg{color:var(--orange);flex-shrink:0;}

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
  .pd-title{font-size:28px;}
  .pd-price{font-size:26px;}
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
      <button class="icon-btn" aria-label="Open cart" onclick="openCart()">
        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="badge" id="cartBadge" style="display:none;">0</span>
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
      @foreach($categories ?? [] as $cat)
        <a href="{{ route('shop.index', ['category' => $cat->id]) }}">{{ $cat->name }}</a>
      @endforeach
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
      @php
        $primaryImage = $product->images->firstWhere('is_primary', true);
        $imageToShow = $primaryImage ? asset('storage/' . $primaryImage->image_path) : ($product->image ? asset($product->image) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=500&q=80');
        $oldPrice = $product->old_price ?? null;
      @endphp
      <div class="pd-grid">
        <div>
          <img id="mainImage" class="pd-img" src="{{ $imageToShow }}" alt="{{ $product->name }}">
          @if($product->images->count() > 1)
            <div class="pd-thumbs">
              @foreach($product->images as $img)
                <button class="pd-thumb {{ $img->is_primary ? 'active' : '' }}" onclick="changeImage('{{ asset('storage/' . $img->image_path) }}', this)">
                  <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $product->name }}">
                </button>
              @endforeach
            </div>
          @endif
        </div>
        <div>
          <span class="pd-cat">{{ $product->category->name ?? 'Uncategorized' }}{{ $product->brand ? ' · ' . $product->brand->name : '' }}</span>
          <h1 class="pd-title">{{ $product->name }}</h1>
          <div class="pd-price">
            TZS {{ number_format($product->selling_price, 0) }}
            @if($oldPrice)
              <span class="pd-price-old">TZS {{ number_format($oldPrice, 0) }}</span>
            @endif
          </div>
          @if($product->description)
            <p class="pd-desc">{{ $product->description }}</p>
          @endif
          <ul class="pd-meta-list">
            <li><span>Availability</span><span class="{{ $product->quantity > 0 ? 'text-green-700' : 'text-red-600' }}">{{ $product->quantity > 0 ? $product->quantity . ' in stock' : 'Out of stock' }}</span></li>
            <li><span>Delivery</span><span>TZS 3,000 or free pickup</span></li>
          </ul>
          @if($product->quantity <= 5 && $product->quantity > 0)
            <p class="stock-low">Only {{ $product->quantity }} left in stock</p>
          @endif
          <div class="pd-actions">
            <div class="qty-stepper">
              <button onclick="changeProductQty(-1)" aria-label="Decrease quantity">−</button>
              <span id="productQty">1</span>
              <button onclick="changeProductQty(1)" aria-label="Increase quantity">+</button>
            </div>
            <button class="btn btn-primary btn-block" onclick="addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }})" {{ $product->quantity <= 0 ? 'disabled' : '' }}>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              Add to cart
            </button>
          </div>
          <div class="pd-actions">
            <a href="{{ route('shop.index') }}" class="btn btn-outline btn-block">Continue shopping</a>
            <a href="{{ route('shop.checkout') }}" class="btn btn-dark btn-block">Checkout</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <div class="footer-logo"><span class="logo-mark" style="background:var(--orange);color:var(--green-900);">F</span> Feedtan Store</div>
        <p style="font-size:13.5px;line-height:1.7;max-width:280px;">Quality products at unbeatable prices, delivered right to your door — or ready when you walk in.</p>
        <div style="display:flex;gap:10px;margin-top:16px;">
          <a href="#" class="icon-btn" style="background:rgba(255,255,255,0.08);color:#fff;" aria-label="Facebook"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1 0 2 .1 2.3.2v2.7h-1.6c-1.2 0-1.5.6-1.5 1.4V12h2.9l-.4 2.9h-2.5v7A10 10 0 0 0 22 12z"/></svg></a>
          <a href="#" class="icon-btn" style="background:rgba(255,255,255,0.08);color:#fff;" aria-label="Instagram"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg></a>
          <a href="#" class="icon-btn" style="background:rgba(255,255,255,0.08);color:#fff;" aria-label="WhatsApp"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.6-.8-1.9-.9-.2-.1-.4-.1-.6.1-.2.2-.6.9-.8 1-.1.2-.3.2-.5.1-1.4-.7-2.3-1.3-3.3-2.8-.1-.2-.1-.4.1-.5.2-.2.4-.5.6-.7.1-.2.1-.4 0-.5-.1-.2-.7-1.6-.9-2.2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9 1-.9 2.3 0 1.4 1 2.7 1.1 2.9.1.2 1.9 3 4.6 4.1 2.3.9 2.3.6 2.7.6.4 0 1.4-.6 1.6-1.1.2-.6.2-1.1.1-1.2 0-.1-.2-.2-.5-.3zM12 2a10 10 0 0 0-8.5 15.3L2 22l4.8-1.5A10 10 0 1 0 12 2z"/></svg></a>
        </div>
      </div>
      <div>
        <h4>Shop</h4>
        <ul>
          @foreach($categories ?? [] as $cat)
            <li><a href="{{ route('shop.index', ['category' => $cat->id]) }}">{{ $cat->name }}</a></li>
          @endforeach
        </ul>
      </div>
      <div>
        <h4>Support</h4>
        <ul>
          <li><a href="{{ route('shop.tracking') }}">Track my order</a></li>
          <li><a href="#" onclick="showToast('Reach us on +255 700 000 000','phone')">Contact us</a></li>
          <li><a href="#" onclick="showToast('Returns accepted within 48 hours of delivery','info')">Returns policy</a></li>
          <li><a href="#" onclick="showToast('Delivery available across Dar es Salaam and nearby regions','info')">Delivery info</a></li>
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

<aside class="cart-drawer" id="cartDrawer" aria-label="Shopping cart">
  <div class="drawer-head">
    <h3>Your Cart</h3>
    <button class="close-x" onclick="closeCart()" aria-label="Close cart">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M18 6 6 18M6 6l12 12"/></svg>
    </button>
  </div>
  <div class="cart-list" id="cartList"></div>
  <div class="drawer-foot" id="cartFoot" style="display:none;">
    <div class="sum-row"><span>Subtotal</span><span id="cartSubtotal">TZS 0</span></div>
    <div class="sum-row"><span>Estimated delivery</span><span id="cartDeliveryEst">Calculated at checkout</span></div>
    <div class="sum-row total"><span>Total</span><span id="cartTotal">TZS 0</span></div>
    <a href="{{ route('shop.checkout') }}" class="btn btn-primary btn-block" style="margin-top:14px;">Proceed to Checkout</a>
    <button class="btn btn-ghost btn-block" style="margin-top:10px;" onclick="closeCart()">Continue Shopping</button>
  </div>
</aside>

<div id="scrim" class="scrim" onclick="closeAllOverlays()"></div>
<div id="toast" class="toast"></div>

<script>
let cart = {};
const DELIVERY_FEE = 3000;
const FREE_DELIVERY_THRESHOLD = 50000;
let productQty = 1;
const productMaxQty = {{ $product->quantity ?? 999 }};

function initCart() {
  const saved = localStorage.getItem('shopCart');
  if (saved) {
    try {
      cart = JSON.parse(saved);
    } catch(e) {
      cart = {};
    }
  }
  updateCartUI();
}

function addToCart(id, name, price) {
  const qtyToAdd = productQty;
  cart[id] = (cart[id] || 0) + qtyToAdd;
  if (productMaxQty > 0) {
    cart[id] = Math.min(cart[id], productMaxQty);
  }
  saveCart();
  updateCartUI();
  showToast(qtyToAdd + 'x ' + name + ' added to cart', 'cart');
}

function changeProductQty(delta) {
  productQty += delta;
  productQty = Math.max(1, productQty);
  if (productMaxQty > 0) productQty = Math.min(productQty, productMaxQty);
  document.getElementById('productQty').textContent = productQty;
}

function changeQty(id, delta) {
  cart[id] = (cart[id] || 0) + delta;
  if (cart[id] <= 0) {
    delete cart[id];
  }
  saveCart();
  updateCartUI();
}

function removeFromCart(id) {
  delete cart[id];
  saveCart();
  updateCartUI();
}

function saveCart() {
  localStorage.setItem('shopCart', JSON.stringify(cart));
}

function cartCount() { return Object.values(cart).reduce((a,b)=>a+b,0); }

function updateCartUI() {
  const count = cartCount();
  const badge = document.getElementById('cartBadge');
  badge.style.display = count > 0 ? 'flex' : 'none';
  badge.textContent = count;
  renderCartList();
}

function renderCartList() {
  const list = document.getElementById('cartList');
  const foot = document.getElementById('cartFoot');
  const entries = Object.entries(cart);
  if (entries.length === 0) {
    list.innerHTML = '<div class="cart-empty">' +
      '<svg width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>' +
      '<b>Your cart is empty</b>' +
      '<span>Browse the catalog and add something tasty.</span>' +
      '<button class="btn btn-primary btn-sm" onclick="closeCart()">Start shopping</button>' +
      '</div>';
    foot.style.display = 'none';
    return;
  }
  foot.style.display = 'block';
  let subtotal = 0;
  list.innerHTML = entries.map(([id, qty]) => {
    let name = 'Product #' + id;
    let img = 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=500&q=80';
    let price = 0;
    if (id == '{{ $product->id }}') {
      name = '{{ addslashes($product->name) }}';
      img = '{{ $imageToShow }}';
      price = {{ $product->selling_price }};
    }
    subtotal += price * qty;
    return '<div class="cart-row">' +
      '<img src="'+img+'" alt="'+name+'">' +
      '<div class="cart-row-info">' +
        '<b>'+name+'</b>' +
        '<span class="cr-meta">per item · TZS '+price.toLocaleString()+' each</span>' +
        '<div class="cart-row-bottom">' +
          '<div class="qty-stepper">' +
            '<button onclick="changeQty(\''+id+'\', -1)" aria-label="Decrease quantity">−</button>' +
            '<span>'+qty+'</span>' +
            '<button onclick="changeQty(\''+id+'\', 1)" aria-label="Increase quantity">+</button>' +
          '</div>' +
          '<span class="cr-price">TZS '+(price*qty).toLocaleString()+'</span>' +
        '</div>' +
        '<button class="cr-remove" onclick="removeFromCart(\''+id+'\')">Remove</button>' +
      '</div>' +
    '</div>';
  }).join('');
  document.getElementById('cartSubtotal').textContent = 'TZS ' + subtotal.toLocaleString();
  document.getElementById('cartTotal').textContent = 'TZS ' + subtotal.toLocaleString();
  document.getElementById('cartDeliveryEst').textContent = subtotal >= FREE_DELIVERY_THRESHOLD ? 'Free (order qualifies)' : 'TZS ' + DELIVERY_FEE.toLocaleString() + ' if delivered';
}

function openCart() {
  document.getElementById('cartDrawer').classList.add('open');
  document.getElementById('scrim').classList.add('open');
}

function closeCart() {
  document.getElementById('cartDrawer').classList.remove('open');
  document.getElementById('scrim').classList.remove('open');
}

function closeAllOverlays() {
  closeCart();
}

function changeImage(src, btn) {
  document.getElementById('mainImage').src = src;
  document.querySelectorAll('.pd-thumb').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
}

function showToast(msg, icon) {
  const toast = document.getElementById('toast');
  const icons = {
    heart:'<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7-4.6-9.5-9C0.7 8.6 2 5 5.3 4.2 7.5 3.6 9.6 4.8 12 7.5c2.4-2.7 4.5-3.9 6.7-3.3C22 5 23.3 8.6 21.5 12 19 16.4 12 21 12 21z"/></svg>',
    info:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>',
    phone:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.58 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
    cart:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>'
  };
  toast.innerHTML = (icons[icon] || icons.info) + '<span>'+msg+'</span>';
  toast.classList.add('show');
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(() => toast.classList.remove('show'), 2800);
}

function toggleMobileSearch() {
  const box = document.getElementById('mobileSearchBox');
  box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', initCart);
</script>
</body>
</html>
