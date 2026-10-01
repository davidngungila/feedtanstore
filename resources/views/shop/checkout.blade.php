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
<meta name="theme-color" content="#123328">
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
@include('shop.partials.styles')
<style>
/* ---------- Section cards ---------- */
.checkout-card{
  background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);
  padding:22px;margin-bottom:20px;box-shadow:var(--shadow-card);
}
.card-title{
  font-family:var(--font-display);font-size:19px;margin:0 0 18px;
  display:flex;align-items:center;gap:11px;color:var(--green-900);
}
.card-title .n{
  width:28px;height:28px;border-radius:50%;background:var(--green-100);color:var(--green-700);
  display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;
  font-family:var(--font-mono);font-size:13px;font-weight:700;
}
.option-card .opt-text{display:flex;flex-direction:column;gap:2px;min-width:0;}
.option-card .opt-text b{font-size:14px;color:var(--green-900);}
.option-card .opt-text span{font-size:12px;font-weight:500;color:var(--ink-soft);line-height:1.45;}

/* ---------- Steps ---------- */
.steps{display:flex;align-items:center;gap:0;margin:0 0 26px;}
.step{flex:1;display:flex;align-items:center;gap:10px;}
.step:not(:last-child)::after{content:'';flex:1;height:2px;background:var(--line);margin:0 12px;border-radius:2px;}
.st-ic{
  width:36px;height:36px;border-radius:50%;background:#fff;border:2px solid var(--line);
  color:var(--ink-soft);display:flex;align-items:center;justify-content:center;
  font-family:var(--font-mono);font-size:13px;font-weight:700;flex-shrink:0;
}
.step.active .st-ic{background:var(--orange-600);border-color:var(--orange-600);color:#fff;box-shadow:0 0 0 5px var(--orange-100);}
.st-label{font-size:12.5px;font-weight:700;color:var(--ink-soft);white-space:nowrap;}
.step.active .st-label{color:var(--green-900);}

/* ---------- Location ---------- */
.location-box{background:var(--green-050);border:1px solid var(--line);border-radius:var(--radius-m);padding:16px;}
.location-box-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;}
.location-status{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--ink-soft);margin-bottom:0;}
.location-status.ok{color:var(--success);}
.location-status.error{color:var(--red);}
.location-coords{font-family:var(--font-mono);font-size:11.5px;color:var(--ink-faint);margin-top:8px;word-break:break-all;}
.search-result-item{padding:11px 12px;cursor:pointer;border-bottom:1px solid var(--line);transition:background .15s;background:none;border-left:none;border-right:none;width:100%;text-align:left;}
.search-result-item:last-child{border-bottom:none;}
.search-result-item:hover{background:var(--green-050);}
.search-result-name{font-size:13px;font-weight:700;color:var(--green-900);display:block;}
.search-result-address{font-size:11.5px;color:var(--ink-soft);display:block;margin-top:2px;line-height:1.4;}

/* ---------- Summary ---------- */
.summary-card{background:var(--paper);}
.summary-card h4{font-size:15px;margin-bottom:12px;}

/* ---------- Sticky mobile pay bar ---------- */
.pay-sticky{
  position:fixed;left:0;right:0;bottom:0;z-index:120;display:none;align-items:center;gap:12px;
  flex-direction:row;justify-content:space-between;
  background:var(--paper);border-top:1px solid var(--line);
  padding:10px 16px calc(10px + env(safe-area-inset-bottom));
  box-shadow:0 -10px 30px -18px rgba(18,51,40,.5);
}
.pay-sticky .ps-left{flex:1;min-width:0;}
.pay-sticky .ps-left b{font-family:var(--font-mono);font-size:17px;font-weight:800;display:block;line-height:1.2;color:var(--green-900);}
.pay-sticky .ps-left span{font-size:11.5px;color:var(--ink-faint);}
.pay-sticky .btn{flex-shrink:0;}

/* ---------- Order placed / waiting for payment popup ---------- */
.pay-modal{position:fixed;inset:0;z-index:220;display:flex;align-items:center;justify-content:center;padding:20px;}
.pay-modal[hidden]{display:none;}
.pay-modal-backdrop{position:absolute;inset:0;background:rgba(12,32,25,.62);backdrop-filter:blur(3px);-webkit-backdrop-filter:blur(3px);}
.pay-modal-box{
  position:relative;width:100%;max-width:400px;max-height:calc(100vh - 40px);overflow-y:auto;
  background:var(--paper);border:1px solid var(--line);border-radius:var(--radius-l);
  box-shadow:var(--shadow-pop);padding:26px 22px 22px;text-align:center;
}
.pay-modal-x{
  position:absolute;top:10px;right:10px;width:34px;height:34px;border-radius:50%;
  border:none;background:transparent;color:var(--ink-faint);font-size:15px;
  display:flex;align-items:center;justify-content:center;
}
.pay-modal-x:hover{background:var(--green-050);color:var(--ink);}
.pay-modal-icon{
  width:60px;height:60px;border-radius:50%;margin:0 auto 14px;
  display:flex;align-items:center;justify-content:center;font-size:26px;
  background:var(--green-100);color:var(--green-700);
}
.pay-modal-box.tone-failed .pay-modal-icon{background:var(--red-dim);color:var(--red);}
.pay-modal-box.tone-timeout .pay-modal-icon{background:var(--orange-100);color:var(--orange-700);}
.pay-modal-title{font-size:20px;margin-bottom:14px;line-height:1.25;}
.pay-modal-rows{display:flex;flex-direction:column;gap:8px;margin-bottom:4px;}
.pay-modal-row{
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  background:var(--green-050);border-radius:var(--radius-m);padding:9px 12px;
  font-size:13px;color:var(--ink-soft);text-align:left;
}
.pay-modal-row b{font-family:var(--font-mono);font-size:13px;color:var(--green-900);word-break:break-all;text-align:right;}
.pay-modal-count{
  font-family:var(--font-mono);font-size:38px;font-weight:800;letter-spacing:1px;
  color:var(--green-900);margin-top:16px;line-height:1;
}
.pay-modal-bar{height:6px;border-radius:99px;background:var(--line);overflow:hidden;margin-top:12px;}
.pay-modal-bar span{display:block;height:100%;width:100%;background:var(--green-600);transition:width 1s linear;}
.pay-modal-hint{font-size:13.5px;color:var(--ink-soft);margin-top:14px;line-height:1.55;}
.pay-modal-actions{display:flex;gap:10px;margin-top:20px;}
.pay-modal-actions .btn{flex:1;justify-content:center;}
.pay-modal-actions .btn[hidden]{display:none;}
html.pay-modal-open,html.pay-modal-open body{overflow:hidden;}

/* ---------- Empty state ---------- */
.empty-state{text-align:center;padding:56px 24px;}
.empty-state .ic{
  width:72px;height:72px;border-radius:50%;background:var(--green-100);color:var(--green-700);
  display:flex;align-items:center;justify-content:center;margin:0 auto 18px;
}
.empty-state h2{font-size:22px;margin-bottom:8px;}
.empty-state p{color:var(--ink-soft);font-size:14px;margin:0 0 22px;}
.empty-state .actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}

/* ---------- Responsive ---------- */
.checkout-bottom-grid{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(300px,.9fr);gap:20px;align-items:start;}
@media (max-width:900px){
  .checkout-bottom-grid{grid-template-columns:1fr;}
  .pay-sticky{display:flex;}
  body.with-pay-sticky{padding-bottom:var(--pay-bar-h,88px);}
  .summary-card{position:static;}
}
@media (max-width:640px){
  .checkout-card{padding:18px 16px;}
  .st-label{display:none;}
  .step:not(:last-child)::after{margin:0 8px;}
}
@media (max-width:420px){
  .pay-sticky .btn{padding:12px 16px;font-size:13.5px;}
  .empty-state{padding:40px 14px;}
}
</style>
</head>
<body class="with-pay-sticky">

@include('shop.partials.header', ['activeNav' => 'shop'])

<main id="mainContent">
  <section class="section" style="padding-top:30px;">
    <div class="wrap">
      <a href="{{ route('shop.index') }}" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> {{ __('Back to store') }}
      </a>

      <div class="sec-head" style="margin-bottom:22px;">
        <span class="eyebrow">{{ __('Checkout') }}</span>
        <h1 style="font-size:clamp(26px,3.4vw,36px);">{{ __('Complete your order') }}</h1>
        <p>{{ __('Tell us where to deliver and we will confirm the fee before you pay.') }}</p>
      </div>

      <ol class="steps" aria-label="{{ __('Checkout steps') }}">
        <li class="step active" data-step="1">
          <span class="st-ic">1</span>
          <span class="st-label">{{ __('Delivery') }}</span>
        </li>
        <li class="step" data-step="2">
          <span class="st-ic">2</span>
          <span class="st-label">{{ __('Details') }}</span>
        </li>
        <li class="step" data-step="3">
          <span class="st-ic">3</span>
          <span class="st-label">{{ __('Pay') }}</span>
        </li>
      </ol>

      <div id="emptyCartState" class="checkout-card empty-state" style="display:none;">
        <div class="ic"><i class="fa-solid fa-cart-shopping" style="font-size:28px;"></i></div>
        <h2>{{ __('Your cart is empty') }}</h2>
        <p>{{ __('Add at least one product to continue.') }}</p>
        <div class="actions">
          <a href="{{ route('shop.index') }}" class="btn btn-primary">{{ __('Go to store') }}</a>
          <a href="{{ route('shop.tracking') }}" class="btn btn-ghost">{{ __('Track order') }}</a>
        </div>
      </div>

      <form id="checkoutForm">
        <!-- ===== DELIVERY OPTION ===== -->
        <div class="checkout-card" id="stepDelivery">
          <h2 class="card-title"><span class="n">1</span> {{ __('Delivery Option') }}</h2>
          <div class="option-grid">
            <label class="option-card selected" id="opt-delivery">
              <input type="radio" name="need_delivery" value="yes" checked onchange="toggleDeliveryOptions()">
              <span class="icon"><i class="fa-solid fa-truck-fast"></i></span>
              <span class="opt-text">
                <b>{{ __('Home Delivery') }}</b>
                <span>{{ __('We bring the order to your door') }}</span>
              </span>
            </label>
            <label class="option-card" id="opt-pickup">
              <input type="radio" name="need_delivery" value="no" onchange="toggleDeliveryOptions()">
              <span class="icon"><i class="fa-solid fa-store"></i></span>
              <span class="opt-text">
                <b>{{ __('Store Pickup') }}</b>
                <span>{{ __('Collect it from our store') }}</span>
              </span>
            </label>
          </div>
        </div>

        <!-- ===== CUSTOMER INFO ===== -->
        <div class="checkout-card" id="stepCustomer">
          <h2 class="card-title"><span class="n">2</span> {{ __('Customer Information') }}</h2>
          <div class="form-grid">
            <div class="field">
              <label for="customerName">{{ __('Full Name') }} *</label>
              <input type="text" id="customerName" required autocomplete="name" placeholder="{{ __('e.g. Asha Mwakalinga') }}">
              <div class="field-error" id="err-customerName"></div>
            </div>
            <div class="field">
              <label for="customerPhone">{{ __('Phone Number') }} *</label>
              <input type="tel" id="customerPhone" required autocomplete="tel" placeholder="{{ __('e.g. 0717 358 865') }}">
              <div class="field-error" id="err-customerPhone"></div>
            </div>
            <div class="field" style="grid-column:1/-1;">
              <label for="customerEmail">{{ __('Email') }} ({{ __('optional') }})</label>
              <input type="email" id="customerEmail" autocomplete="email" placeholder="name@example.com">
              <div class="field-error" id="err-customerEmail"></div>
            </div>
          </div>
        </div>

        <!-- ===== DELIVERY LOCATION ===== -->
        <div class="checkout-card" id="deliveryAddressSection">
          <h2 class="card-title"><span class="n">3</span> {{ __('Delivery Location') }}</h2>

          <div class="option-grid" style="margin-bottom:16px;">
            <label class="option-card selected" id="opt-current-location">
              <input type="radio" name="location_type" value="current" checked onchange="toggleLocationType()">
              <span class="icon"><i class="fa-solid fa-location-crosshairs"></i></span>
              <span class="opt-text">
                <b>{{ __('Current Location') }}</b>
                <span>{{ __('Use your GPS location') }}</span>
              </span>
            </label>
            <label class="option-card" id="opt-other-location">
              <input type="radio" name="location_type" value="other" onchange="toggleLocationType()">
              <span class="icon"><i class="fa-solid fa-map-location-dot"></i></span>
              <span class="opt-text">
                <b>{{ __('Other Location') }}</b>
                <span>{{ __('Pick a spot on the map or type an address') }}</span>
              </span>
            </label>
          </div>

          <!-- Current location -->
          <div class="location-box" id="currentLocationBox">
            <div class="field">
              <label for="deliveryAddress">{{ __('Delivery Address') }} *</label>
              <input type="text" id="deliveryAddress" placeholder="{{ __('Detecting your location automatically...') }}">
              <div class="field-error" id="err-deliveryAddress" style="margin-top:8px;"></div>
            </div>
            <div class="location-box-head">
              <div class="location-status" id="locStatus">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <span>{{ __('Detecting your location...') }}</span>
              </div>
              <button type="button" class="btn btn-outline btn-sm" onclick="detectLocation()">{{ __('Refresh') }}</button>
            </div>
            <div class="location-coords" id="locCoords"></div>
            <div class="mini-map" id="mapPreview"></div>
          </div>

          <!-- Other location -->
          <div class="location-box" id="otherLocationBox" style="display:none;">
            <div class="field">
              <label for="addressSearch">{{ __('Search Location') }}</label>
              <div style="display:flex;gap:8px;">
                <input type="text" id="addressSearch" placeholder="{{ __('Search for a location (e.g. Kiboriloni)') }}" style="flex:1;" onkeypress="if(event.key === 'Enter') searchAddress()">
                <button type="button" class="btn btn-primary btn-sm" onclick="searchAddress()">{{ __('Search') }}</button>
              </div>
              <div class="field-error" id="err-addressSearch"></div>
            </div>

            <div id="searchResults" style="display:none;margin-bottom:12px;max-height:200px;overflow-y:auto;border:1px solid var(--line);border-radius:10px;background:var(--white);"></div>

            <div class="field">
              <label for="manualAddress">{{ __('Delivery Address') }} *</label>
              <input type="text" id="manualAddress" placeholder="{{ __('e.g. Kiboriloni, Moshi') }}">
              <div class="field-error" id="err-manualAddress"></div>
            </div>

            <div class="location-box-head">
              <div class="location-status" id="mapLocStatus">
                <i class="fa-solid fa-hand-pointer"></i>
                <span>{{ __('Tap on the map to select a delivery location') }}</span>
              </div>
            </div>
            <div class="location-coords" id="mapLocCoords"></div>
            <div class="mini-map" id="mapPreviewOther"></div>
            <div class="field-error" id="err-mapLocation" style="margin-top:8px;"></div>
          </div>
        </div>

        <div class="checkout-bottom-grid">
          <!-- ===== ORDER SUMMARY ===== -->
          <div class="checkout-card summary-card" id="stepSummary">
            <h2 class="card-title"><span class="n">4</span> {{ __('Order Summary') }}</h2>
            <div id="checkoutItems"></div>
            <div style="border-top:1px solid var(--line);padding-top:14px;margin-top:6px;">
              <div class="sum-row"><span>{{ __('Subtotal') }}</span><span id="subtotal">TZS 0</span></div>
              <div class="sum-row"><span>{{ __('Delivery Distance') }}</span><span id="deliveryDistanceDisplay">{{ __('Choose location to calculate') }}</span></div>
              <div class="sum-row"><span>{{ __('Delivery Fee') }}</span><span id="deliveryFeeDisplay">{{ __('Choose location to calculate') }}</span></div>
              <div class="sum-row total"><span>{{ __('Total Now') }}</span><span id="checkoutTotal">TZS 0</span></div>
            </div>
          </div>

          <!-- ===== PAYMENT ===== -->
          <div class="checkout-card">
            <h2 class="card-title"><span class="n">5</span> {{ __('Payment') }}</h2>
            <div class="pay-note">
              <i class="fa-solid fa-lock" style="flex-shrink:0;color:var(--green-700);margin-top:2px;"></i>
              <span>{{ __('Pay securely using mobile money. After placing the order you will be asked for a valid phone number such as 2557XXXXXXXX, 07XXXXXXXX or 7XXXXXXXX.') }}</span>
            </div>
            <input type="hidden" name="payment_method" value="online">
            <button type="submit" id="placeOrderBtn" class="btn btn-primary btn-lg btn-block">
              <i class="fa-solid fa-circle-check"></i> {{ __('Pay Now') }}
            </button>
            <p class="field-hint" style="text-align:center;margin-top:12px;">
              {{ __('You will receive an order number to track your delivery.') }}
            </p>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>

@include('shop.partials.footer')
@include('shop.partials.cart-drawer', ['showBottomBar' => false])
@include('shop.partials.cart-js')

<!-- Sticky mobile pay bar -->
<div class="pay-sticky" id="payStickyBar">
  <div class="ps-left">
    <b id="psTotal">TZS 0</b>
    <span id="psSub">{{ __('Checkout') }}</span>
  </div>
  <button class="btn btn-primary" form="checkoutForm" type="submit">
    <i class="fa-solid fa-circle-check"></i> {{ __('Pay Now') }}
  </button>
</div>

<!-- Order placed / waiting for payment popup -->
<div class="pay-modal" id="payModal" hidden>
  <div class="pay-modal-backdrop" data-pay-modal-close></div>
  <div class="pay-modal-box" role="dialog" aria-modal="true" aria-labelledby="payModalTitle">
    <button type="button" class="pay-modal-x" data-pay-modal-close aria-label="{{ __('Close') }}">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="pay-modal-icon" id="payModalIcon"><i class="fa-solid fa-circle-check"></i></div>
    <h2 class="pay-modal-title" id="payModalTitle">{{ __('Order placed!') }}</h2>
    <div class="pay-modal-rows" id="payModalBody"></div>

    <div class="pay-modal-count" id="payModalCount" hidden>01:00</div>
    <div class="pay-modal-bar" id="payModalBar" hidden><span></span></div>
    <p class="pay-modal-hint" id="payModalHint" hidden></p>

    <div class="pay-modal-actions">
      <button type="button" class="btn btn-outline" id="payModalCancel" data-pay-modal-close>{{ __('Close') }}</button>
      <a class="btn btn-primary" id="payModalConfirm" href="#">{{ __('Track Order') }}</a>
    </div>
  </div>
</div>


<script>
(function syncPayBarSpace() {
  var bar = document.getElementById('payStickyBar');
  if (!bar) return;
  function apply() {
    var h = bar.offsetHeight;
    if (h > 0) {
      document.documentElement.style.setProperty('--pay-bar-h', h + 'px');
    }
  }
  apply();
  window.addEventListener('resize', apply);
  window.addEventListener('orientationchange', apply);
  if (window.ResizeObserver) new ResizeObserver(apply).observe(bar);
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(apply);
})();
</script>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let userLocation = { lat: null, lng: null, accuracy: null };
let userLocationName = '';
let checkoutMap = null;
let checkoutMarker = null;
let checkoutMapOther = null;
let checkoutMarkerOther = null;
let selectedLocation = { lat: null, lng: null };
let currentDeliveryFee = 0;
let needDelivery = 'yes';

async function reverseGeocode(lat, lng) {
  try {
    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1&zoom=18`);
    const data = await res.json();
    if (data && data.display_name) {
      const parts = data.display_name.split(', ');
      return parts.length > 3 ? parts.slice(0, 3).join(', ') : data.display_name;
    }
  } catch (e) {
    console.error('Reverse geocode error:', e);
  }
  return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
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
  updateCartUI();
}

function updateCartUI() {
  const count = cartCount();
  const badge = document.getElementById('cartBadge');
  if (badge) {
    badge.style.display = count > 0 ? 'flex' : 'none';
    badge.textContent = count;
  }
  renderCartList();
  renderCheckoutItems();
  updateTotal();
}

function renderCheckoutItems() {
  const container = document.getElementById('checkoutItems');
  if (!container) return;
  let html = '';
  cart.forEach(item => {
    const total = item.price * item.quantity;
    html += `
      <div class="mini-item">
        <div>
          <b>${item.name}</b>
          <div style="font-size:12px;color:var(--ink-soft);">${item.quantity} Ã— TZS ${Number(item.price).toLocaleString()}</div>
        </div>
        <div><b>TZS ${total.toLocaleString()}</b></div>
      </div>
    `;
  });
  container.innerHTML = html;
}

function calculateTotal() {
  return cart.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 0), 0);
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
        const data = await response.json();
        if (data.success) {
          currentDeliveryFee = data.delivery_fee;
          document.getElementById('deliveryDistanceDisplay').textContent = data.formatted_distance;
          document.getElementById('deliveryFeeDisplay').textContent = data.formatted_delivery_fee;
          updateTotal();
        }
      } catch (e) {
        console.error('Failed to calculate delivery fee', e);
      }
    } else {
      currentDeliveryFee = 0;
      document.getElementById('deliveryDistanceDisplay').textContent = '{{ __('Choose location to calculate') }}';
      document.getElementById('deliveryFeeDisplay').textContent = '{{ __('Choose location to calculate') }}';
      updateTotal();
    }
  } else {
    currentDeliveryFee = 0;
    document.getElementById('deliveryDistanceDisplay').textContent = '{{ __('Store Pickup') }}';
    document.getElementById('deliveryFeeDisplay').textContent = 'TZS 0';
    updateTotal();
  }
}

function updateTotal() {
  const subtotal = calculateTotal();
  const total = subtotal + currentDeliveryFee;
  document.getElementById('subtotal').textContent = 'TZS ' + subtotal.toLocaleString();
  if (needDelivery === 'no') {
    document.getElementById('deliveryFeeDisplay').textContent = 'TZS 0';
  }
  document.getElementById('checkoutTotal').textContent = 'TZS ' + total.toLocaleString();
  const psTotal = document.getElementById('psTotal');
  if (psTotal) psTotal.textContent = 'TZS ' + total.toLocaleString();
  const psSub = document.getElementById('psSub');
  if (psSub) psSub.textContent = subtotal + ' {{ __('items') }}';
}

function toggleDeliveryOptions() {
  needDelivery = document.querySelector('input[name="need_delivery"]:checked').value;
  const deliveryAddressSection = document.getElementById('deliveryAddressSection');
  const optDelivery = document.getElementById('opt-delivery');
  const optPickup = document.getElementById('opt-pickup');

  if (needDelivery === 'yes') {
    deliveryAddressSection.style.display = 'block';
    optDelivery.classList.add('selected');
    optPickup.classList.remove('selected');
    toggleLocationType();
  } else {
    deliveryAddressSection.style.display = 'none';
    optPickup.classList.add('selected');
    optDelivery.classList.remove('selected');
  }
  fetchDeliveryFee();
}

const GEO_OPTIONS = { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 };
let locRetryCount = 0;
let locRetryTimer = null;

function setLocStatus(state, message) {
  const statusEl = document.getElementById('locStatus');
  if (!statusEl) return;
  const icons = {
    pending: 'fa-solid fa-spinner fa-spin',
    ok: 'fa-solid fa-circle-check',
    error: 'fa-solid fa-triangle-exclamation',
  };
  statusEl.classList.remove('pending', 'ok', 'error');
  if (state) statusEl.classList.add(state);
  const iconEl = statusEl.querySelector('i');
  if (iconEl) iconEl.className = icons[state] || icons.pending;
  const textEl = statusEl.querySelector('span');
  if (textEl) textEl.textContent = message;
}

function detectLocation() {
  const coordsEl = document.getElementById('locCoords');
  if (coordsEl) coordsEl.textContent = '';

  if (!navigator.geolocation) {
    setLocStatus('error', '{{ __('Geolocation not supported') }}');
    setFieldError('deliveryAddress', '{{ __('Your browser cannot share your location. Please choose another location.') }}');
    return Promise.resolve(false);
  }

  setLocStatus('pending', '{{ __('Getting your live location...') }}');

  return new Promise((resolve) => {
    navigator.geolocation.getCurrentPosition(
      async (position) => {
        locRetryCount = 0;
        if (locRetryTimer) { clearTimeout(locRetryTimer); locRetryTimer = null; }
        userLocation.lat = position.coords.latitude;
        userLocation.lng = position.coords.longitude;
        userLocation.accuracy = position.coords.accuracy;

        const coordsText = document.getElementById('locCoords');
        if (coordsText) {
          const acc = userLocation.accuracy ? ` (±${Math.round(userLocation.accuracy)}m)` : '';
          coordsText.textContent = `${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}${acc}`;
        }

        setLocStatus('pending', '{{ __('Location captured! Resolving address...') }}');
        initializeCheckoutMap();
        updateCheckoutMap(userLocation.lat, userLocation.lng);

        try {
          userLocationName = await reverseGeocode(userLocation.lat, userLocation.lng);
        } catch (e) {
          userLocationName = '';
        }

        const addressEl = document.getElementById('deliveryAddress');
        if (addressEl) {
          addressEl.value = userLocationName || `${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}`;
        }
        setLocStatus('ok', '{{ __('Live location captured') }}');
        setFieldError('deliveryAddress', '');
        fetchDeliveryFee();
        resolve(true);
      },
      (error) => {
        console.error('Geolocation error:', error);
        const messages = {
          1: '{{ __('Location permission denied') }}',
          2: '{{ __('Location unavailable') }}',
          3: '{{ __('Location request timed out') }}',
        };
        setLocStatus('error', messages[error.code] || '{{ __('Failed to detect location') }}');
        setFieldError('deliveryAddress', '{{ __('Could not capture your live location. Tap Refresh to try again, or choose another location.') }}');

        const retriable = error.code === 2 || error.code === 3;
        if (retriable && locRetryCount < 2) {
          locRetryCount++;
          locRetryTimer = setTimeout(() => detectLocation(), 1500 * locRetryCount);
        }
        resolve(false);
      },
      GEO_OPTIONS
    );
  });
}

function initializeCheckoutMap() {
  if (checkoutMap || typeof L === 'undefined') return;
  const mapEl = document.getElementById('mapPreview');
  if (!mapEl) return;

  const initialLat = userLocation.lat || -3.3430;
  const initialLng = userLocation.lng || 37.3507;

  checkoutMap = L.map('mapPreview', {
    zoomControl: true,
    attributionControl: true,
  }).setView([initialLat, initialLng], 12);

  const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  });

  osmLayer.addTo(checkoutMap);

  checkoutMarker = L.marker([initialLat, initialLng]).addTo(checkoutMap)
    .bindPopup('{{ __('Delivery location preview') }}')
    .openPopup();

  setTimeout(() => checkoutMap.invalidateSize(), 150);
}

function updateCheckoutMap(lat, lng, zoom = 15) {
  initializeCheckoutMap();
  if (!checkoutMap || !checkoutMarker) return;
  checkoutMarker.setLatLng([lat, lng]);
  checkoutMap.setView([lat, lng], zoom);
  checkoutMarker.bindPopup(`{{ __('Delivery location') }}<br>${lat.toFixed(6)}, ${lng.toFixed(6)}`).openPopup();
  setTimeout(() => checkoutMap.invalidateSize(), 100);
}

function initializeCheckoutMapOther() {
  if (checkoutMapOther || typeof L === 'undefined') return;
  const mapEl = document.getElementById('mapPreviewOther');
  if (!mapEl) return;

  const initialLat = selectedLocation.lat || -3.3430;
  const initialLng = selectedLocation.lng || 37.3507;

  checkoutMapOther = L.map('mapPreviewOther', {
    zoomControl: true,
    attributionControl: true,
  }).setView([initialLat, initialLng], 12);

  const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  });

  osmLayer.addTo(checkoutMapOther);

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

    checkoutMarkerOther.bindPopup(`{{ __('Selected location') }}<br>${lat.toFixed(6)}, ${lng.toFixed(6)}`).openPopup();
    checkoutMapOther.setView([lat, lng], 15);

    document.getElementById('mapLocCoords').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    document.getElementById('mapLocStatus').querySelector('span').textContent = '{{ __('Location selected!') }}';
    document.getElementById('mapLocStatus').classList.remove('pending', 'error');
    setFieldError('mapLocation', '');
    fetchDeliveryFee();
  });

  if (selectedLocation.lat && selectedLocation.lng) {
    checkoutMarkerOther = L.marker([selectedLocation.lat, selectedLocation.lng]).addTo(checkoutMapOther)
      .bindPopup(`{{ __('Selected location') }}<br>${selectedLocation.lat.toFixed(6)}, ${selectedLocation.lng.toFixed(6)}`)
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
    setTimeout(() => { if (checkoutMap) checkoutMap.invalidateSize(); }, 150);
  } else {
    currentLocationBox.style.display = 'none';
    otherLocationBox.style.display = 'block';
    optOther.classList.add('selected');
    optCurrent.classList.remove('selected');
    initializeCheckoutMapOther();
    setTimeout(() => { if (checkoutMapOther) checkoutMapOther.invalidateSize(); }, 150);
  }

  setFieldError('deliveryAddress', '');
  setFieldError('manualAddress', '');
  setFieldError('mapLocation', '');

  fetchDeliveryFee();
}

async function searchAddress() {
  const searchInput = document.getElementById('addressSearch');
  const searchResults = document.getElementById('searchResults');
  const query = searchInput.value.trim();

  if (!query) {
    setFieldError('addressSearch', '{{ __('Please enter a location to search') }}');
    searchResults.style.display = 'none';
    return;
  }

  setFieldError('addressSearch', '');
  searchResults.innerHTML = '<div style="padding:12px;text-align:center;color:var(--ink-soft);">{{ __('Searching...') }}</div>';
  searchResults.style.display = 'block';

  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query + ', Tanzania')}&format=json&limit=5`);
    const data = await response.json();

    if (data.length === 0) {
      searchResults.innerHTML = '<div style="padding:12px;text-align:center;color:var(--ink-soft);">{{ __('No results found. Try a different search term.') }}</div>';
      return;
    }

    let html = '';
    data.forEach(result => {
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
    searchResults.innerHTML = '<div style="padding:12px;text-align:center;color:var(--red);">{{ __('Search failed. Please try again.') }}</div>';
  }
}

function selectSearchResult(lat, lng, displayName) {
  selectedLocation.lat = parseFloat(lat);
  selectedLocation.lng = parseFloat(lng);

  document.getElementById('manualAddress').value = displayName.split(',')[0];
  document.getElementById('addressSearch').value = '';
  document.getElementById('searchResults').style.display = 'none';

  initializeCheckoutMapOther();
  if (checkoutMarkerOther) {
    checkoutMarkerOther.setLatLng([selectedLocation.lat, selectedLocation.lng]);
  } else {
    checkoutMarkerOther = L.marker([selectedLocation.lat, selectedLocation.lng]).addTo(checkoutMapOther);
  }

  checkoutMarkerOther.bindPopup(`{{ __('Selected location') }}<br>${displayName}`).openPopup();
  checkoutMapOther.setView([selectedLocation.lat, selectedLocation.lng], 15);

  document.getElementById('mapLocCoords').textContent = `${selectedLocation.lat.toFixed(6)}, ${selectedLocation.lng.toFixed(6)}`;
  document.getElementById('mapLocStatus').querySelector('span').textContent = '{{ __('Location selected!') }}';
  document.getElementById('mapLocStatus').classList.remove('pending', 'error');

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

  if (!name) { setFieldError('customerName', '{{ __('Full Name is required') }}'); ok = false; } else { setFieldError('customerName', ''); }
  if (!phone) { setFieldError('customerPhone', '{{ __('Phone Number is required') }}'); ok = false; } else { setFieldError('customerPhone', ''); }
  if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setFieldError('customerEmail', '{{ __('Enter a valid email') }}'); ok = false; } else { setFieldError('customerEmail', ''); }
  return ok;
}

function validateAddressIfNeeded() {
  const needDeliveryVal = document.querySelector('input[name="need_delivery"]:checked').value;
  if (needDeliveryVal !== 'yes') {
    document.getElementById('deliveryAddress').value = '{{ __('Store Pickup') }}';
    setFieldError('deliveryAddress', '');
    return true;
  }

  const locationType = document.querySelector('input[name="location_type"]:checked').value;

  if (locationType === 'current') {
    if (!userLocation.lat || !userLocation.lng) {
      setFieldError('deliveryAddress', '{{ __('Your location must be captured automatically for delivery.') }}');
      return false;
    }
    document.getElementById('deliveryAddress').value = userLocationName || `${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}`;
    setFieldError('deliveryAddress', '');
    return true;
  } else {
    const manualAddress = document.getElementById('manualAddress').value.trim();
    if (!manualAddress) {
      setFieldError('manualAddress', '{{ __('Delivery address is required') }}');
      return false;
    }
    setFieldError('manualAddress', '');

    if (!selectedLocation.lat || !selectedLocation.lng) {
      setFieldError('mapLocation', '{{ __('Please select a location on the map') }}');
      return false;
    }
    setFieldError('mapLocation', '');

    document.getElementById('deliveryAddress').value = `${manualAddress} (${selectedLocation.lat.toFixed(6)}, ${selectedLocation.lng.toFixed(6)})`;
    return true;
  }
}

const PAY_WAIT_SECONDS = 60;
let payWaitTimer = null;
let payPollTimer = null;
let payRedirectTimer = null;
const PAY_REDIRECT_DELAY = 2500;

const PAY_MODAL_TONES = {
  placed:  { icon: 'fa-solid fa-circle-check',    tone: 'placed' },
  waiting: { icon: 'fa-solid fa-spinner fa-spin', tone: 'waiting' },
  success: { icon: 'fa-solid fa-circle-check',    tone: 'success' },
  failed:  { icon: 'fa-solid fa-circle-xmark',    tone: 'failed' },
  timeout: { icon: 'fa-solid fa-clock',           tone: 'timeout' },
};

const PAY_MODAL_COPY = {
  placed:  { title: '{{ __('Order placed!') }}',                                    hint: '{{ __('Your order has been received.') }}' },
  waiting: { title: '{{ __('Waiting for payment') }}',                              hint: '{{ __('Check your phone and approve the mobile money prompt to finish paying.') }}' },
  success: { title: '{{ __('Payment complete!') }}',                               hint: '{{ __('Payment completed successfully. Taking you to your order tracking page...') }}' },
  failed:  { title: '{{ __('Payment not completed') }}',                           hint: '{{ __('The payment was not completed. You can pay again from the tracking page.') }}' },
  timeout: { title: '{{ __('Still waiting for payment') }}',                       hint: '{{ __('We could not confirm the payment yet. If you already paid it may take a little longer to show.') }}' },
};

const payModal = { root: null, box: null, icon: null, title: null, body: null, count: null, bar: null, hint: null, confirm: null, cancel: null };
const payState = { phase: 'waiting', remaining: PAY_WAIT_SECONDS, status: '', orderRef: '', trackingUrl: '', phone: '' };
let payRetrying = false;

function formatCountdown(totalSeconds) {
  const s = Math.max(0, Math.floor(totalSeconds));
  return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
}

function extractPaymentStatus(payload) {
  if (!payload) return null;
  if (payload.data && payload.data.status) return payload.data.status;
  if (payload.status) return payload.status;
  if (payload.data && payload.data.clickpesa_status) return payload.data.clickpesa_status;
  return null;
}

function stopPayWaitTimers() {
  if (payWaitTimer) { clearInterval(payWaitTimer); payWaitTimer = null; }
  if (payPollTimer) { clearInterval(payPollTimer); payPollTimer = null; }
  if (payRedirectTimer) { clearTimeout(payRedirectTimer); payRedirectTimer = null; }
}

function cachePayModal() {
  payModal.root = document.getElementById('payModal');
  if (!payModal.root) return;
  payModal.box = payModal.root.querySelector('.pay-modal-box');
  payModal.icon = document.getElementById('payModalIcon');
  payModal.title = document.getElementById('payModalTitle');
  payModal.body = document.getElementById('payModalBody');
  payModal.count = document.getElementById('payModalCount');
  payModal.bar = document.getElementById('payModalBar');
  payModal.hint = document.getElementById('payModalHint');
  payModal.confirm = document.getElementById('payModalConfirm');
  payModal.cancel = document.getElementById('payModalCancel');
}

function renderPayModal() {
  if (!payModal.root) return;
  const tone = PAY_MODAL_TONES[payState.phase] || PAY_MODAL_TONES.waiting;
  const copy = PAY_MODAL_COPY[payState.phase] || PAY_MODAL_COPY.waiting;
  const showTimer = payState.phase === 'waiting';

  payModal.box.className = 'pay-modal-box tone-' + tone.tone;
  payModal.icon.innerHTML = '<i class="' + tone.icon + '"></i>';
  payModal.title.textContent = copy.title;
  payModal.hint.textContent = copy.hint;
  payModal.hint.hidden = false;

  const rows = ['<div class="pay-modal-row"><span>' + '{{ __('Order number') }}' + '</span><b>' + payState.orderRef + '</b></div>'];
  if (payState.status) {
    rows.push('<div class="pay-modal-row"><span>' + '{{ __('Gateway status') }}' + '</span><b>' + payState.status + '</b></div>');
  }
  payModal.body.innerHTML = rows.join('');

  payModal.count.hidden = !showTimer;
  payModal.bar.hidden = !showTimer;
  if (showTimer) {
    payModal.count.textContent = formatCountdown(payState.remaining);
    const pct = Math.max(0, Math.min(100, (payState.remaining / PAY_WAIT_SECONDS) * 100));
    payModal.bar.firstElementChild.style.width = pct + '%';
  }

  payModal.confirm.hidden = payState.phase === 'waiting';
  payModal.confirm.href = payState.trackingUrl || '#';
  payModal.confirm.textContent = (payState.phase === 'timeout' || payState.phase === 'failed')
    ? '{{ __('Retry payment') }}'
    : '{{ __('Track Order') }}';
  payModal.cancel.hidden = payState.phase !== 'waiting';
}

function openPayModal() {
  if (!payModal.root) return;
  payModal.root.hidden = false;
  document.documentElement.classList.add('pay-modal-open');
  renderPayModal();
}

function closePayModal() {
  stopPayWaitTimers();
  if (!payModal.root) return;
  payModal.root.hidden = true;
  document.documentElement.classList.remove('pay-modal-open');
}

function goToTracking() {
  stopPayWaitTimers();
  window.location.href = payState.trackingUrl;
}

function scheduleTrackingRedirect() {
  if (payRedirectTimer) return;
  payRedirectTimer = setTimeout(() => {
    payRedirectTimer = null;
    goToTracking();
  }, PAY_REDIRECT_DELAY);
}

function showOrderPlaced(data) {
  payState.phase = data.payment_initiated ? 'waiting' : 'placed';
  payState.remaining = PAY_WAIT_SECONDS;
  payState.status = '';
  payState.trackingUrl = data.tracking_url || '';
  payState.orderRef = data.order_number || '';
  payState.phone = (document.getElementById('customerPhone')?.value || '').trim();
  try {
    const trackingLocation = new URL(data.tracking_url, window.location.origin);
    // Stay on the site where this order was created. The API may return the
    // public store domain while staff or customers are checking out elsewhere.
    payState.trackingUrl = trackingLocation.pathname + trackingLocation.search + trackingLocation.hash;
    payState.orderRef = trackingLocation.pathname.split('/').filter(Boolean).pop() || payState.orderRef;
  } catch (e) {}
  openPayModal();
  if (!data.payment_initiated) return;
  startPayWait();
}

function startPayWait() {
  stopPayWaitTimers();
  const startedAt = Date.now();
  const identifier = payState.orderRef;

  payWaitTimer = setInterval(() => {
    payState.remaining = Math.max(0, PAY_WAIT_SECONDS - Math.floor((Date.now() - startedAt) / 1000));
    if (payState.remaining === 0) {
      payState.phase = 'timeout';
      stopPayWaitTimers();
      renderPayModal();
      return;
    }
    renderPayModal();
  }, 1000);

  payPollTimer = setInterval(async () => {
    if (!identifier) return;
    try {
      const res = await fetch('/api/shop/orders/' + encodeURIComponent(identifier) + '/payment-status', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
      });
      if (!res.ok) return;
      const payload = await res.json().catch(() => ({}));
      const raw = extractPaymentStatus(payload);
      if (!raw) return;

      const normalized = String(raw).toUpperCase();
      payState.status = normalized;

      if (normalized === 'SUCCESS' || normalized === 'SETTLED') {
        payState.phase = 'success';
        stopPayWaitTimers();
        renderPayModal();
        scheduleTrackingRedirect();
      } else if (['FAILED', 'DECLINED', 'CANCELLED'].includes(normalized)) {
        payState.phase = 'failed';
        stopPayWaitTimers();
        renderPayModal();
      } else {
        renderPayModal();
      }
    } catch (e) {}
  }, 3000);
}

async function retryPayNow() {
  if (payRetrying || !payState.orderRef) return;
  payRetrying = true;
  payState.phase = 'waiting';
  payState.remaining = PAY_WAIT_SECONDS;
  payState.status = 'PROCESSING';
  renderPayModal();
  try {
    const res = await fetch('/api/shop/orders/' + encodeURIComponent(payState.orderRef) + '/initiate-payment', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      credentials: 'same-origin',
      body: JSON.stringify({ phone_number: payState.phone || '' })
    });
    const payload = await res.json().catch(() => ({}));
    if (!res.ok || payload.success === false) {
      throw new Error(payload.message || '{{ __('Failed to start the payment. Please try again.') }}');
    }
    startPayWait();
  } catch (e) {
    payState.phase = 'failed';
    payState.status = '';
    stopPayWaitTimers();
    renderPayModal();
  } finally {
    payRetrying = false;
  }
}


document.addEventListener('DOMContentLoaded', function() {
  initCart();
  setTimeout(hidePageLoader, 350);
  detectLocation();

  cachePayModal();
  document.querySelectorAll('[data-pay-modal-close]').forEach(function(el) {
    el.addEventListener('click', closePayModal);
  });
  payModal.confirm.addEventListener('click', function(e) {
    e.preventDefault();
    if (payState.phase === 'timeout' || payState.phase === 'failed') {
      retryPayNow();
      return;
    }
    goToTracking();
  });
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && payModal.root && !payModal.root.hidden) closePayModal();
  });

  document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const placeOrderBtn = document.getElementById('placeOrderBtn');
    const restingHtml = placeOrderBtn.innerHTML;

    const locationType = document.querySelector('input[name="location_type"]:checked').value;
    if (needDelivery === 'yes' && locationType === 'current' && !userLocation.lat) {
      placeOrderBtn.disabled = true;
      placeOrderBtn.innerHTML = '<svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> {{ __('Locating you...') }}';
      const captured = await detectLocation();
      placeOrderBtn.disabled = false;
      placeOrderBtn.innerHTML = restingHtml;
      if (!captured) {
        setFieldError('deliveryAddress', '{{ __('We could not capture your live location. Please enable location access and try again, or choose another location.') }}');
        document.getElementById('deliveryAddressSection').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }
    }

    if (!validateCustomer() || !validateAddressIfNeeded()) {
      return;
    }

    placeOrderBtn.disabled = true;
    placeOrderBtn.innerHTML = '<svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> {{ __('Placing Order...') }}';

    try {
      const orderableItems = cart.filter(item => item.key && !/^\d+$/.test(String(item.key)));
      if (orderableItems.length === 0) {
        throw new Error('{{ __('Your saved cart is no longer valid. Please return to the shop and add the items again.') }}');
      }
      if (orderableItems.length !== cart.length) {
        cart = orderableItems;
        saveCart();
        updateCartUI();
      }
      const requestBody = {
        fulfillment_method: needDelivery === 'yes' ? 'delivery' : 'pickup',
        customer_name: document.getElementById('customerName').value.trim(),
        customer_phone: document.getElementById('customerPhone').value.trim(),
        customer_email: document.getElementById('customerEmail').value.trim(),
        delivery_address: document.getElementById('deliveryAddress').value.trim(),
        delivery_latitude: needDelivery === 'yes' ? (document.querySelector('input[name="location_type"]:checked').value === 'current' ? userLocation.lat : selectedLocation.lat) : null,
        delivery_longitude: needDelivery === 'yes' ? (document.querySelector('input[name="location_type"]:checked').value === 'current' ? userLocation.lng : selectedLocation.lng) : null,
        delivery_fee: currentDeliveryFee,
        payment_method: 'online',
        items: cart.map(item => ({
          product_key: item.key,
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
        showOrderPlaced(data);
      } else {
        throw new Error(data.message || '{{ __('Failed to place order') }}');
      }
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: error.message || '{{ __('Something went wrong!') }}',
        confirmButtonText: '{{ __('Try Again') }}'
      });
    } finally {
      placeOrderBtn.disabled = false;
      placeOrderBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> {{ __('Pay Now') }}';
    }
  });
});

window.addEventListener('load', hidePageLoader);
</script>
</body>
</html>
