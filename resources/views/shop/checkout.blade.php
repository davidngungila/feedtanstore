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
@include('shop.partials.styles')
<style>
/* ---------- Layout ---------- */
.checkout-bottom-grid{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(320px,0.85fr);gap:24px;align-items:start;}
@media(max-width:900px){.checkout-bottom-grid{grid-template-columns:1fr;}}
.option-grid-two{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}
@media(max-width:480px){.option-grid-two{grid-template-columns:1fr;}}

/* ---------- Step indicator ---------- */
.steps{display:flex;align-items:center;gap:0;margin:26px 0 30px;}
.step{flex:1;display:flex;align-items:center;gap:10px;position:relative;}
.step:not(:last-child)::after{content:'';flex:1;height:2px;background:var(--line);margin:0 12px;border-radius:2px;}
.step.done:not(:last-child)::after{background:var(--green-700);}
.step .st-ic{
  width:38px;height:38px;border-radius:50%;background:#fff;border:2px solid var(--line);color:var(--ink-soft);
  display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;flex-shrink:0;transition:all .2s;
}
.step.active .st-ic{background:var(--green-700);border-color:var(--green-700);color:#fff;box-shadow:0 0 0 5px rgba(27,67,50,.12);}
.step.done .st-ic{background:var(--green-100);border-color:var(--green-700);color:var(--green-700);}
.step .st-label{font-size:12.5px;font-weight:700;color:var(--ink-soft);white-space:nowrap;}
.step.active .st-label{color:var(--ink);}
.step.done .st-label{color:var(--green-700);}
@media(max-width:640px){
  .step .st-label{display:none;}
  .step:not(:last-child)::after{margin:0 8px;}
}

/* ---------- Cards / location ---------- */
.card h2.card-title{font-size:19px;margin-bottom:16px;display:flex;align-items:center;gap:10px;}
.card h2.card-title .n{
  width:26px;height:26px;border-radius:50%;background:var(--green-100);color:var(--green-700);
  display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;font-family:var(--font-body);
}
.location-box{border:1.5px dashed var(--line);border-radius:var(--radius-m);padding:16px;margin:6px 0 0;background:var(--parchment);}
.location-box-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;}
.location-status{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:var(--green-700);}
.location-status.pending{color:var(--ink-soft);}
.location-status.error{color:var(--red);}
.location-coords{font-family:var(--font-mono);font-size:12px;color:var(--ink-soft);margin-top:6px;word-break:break-all;}
.search-result-item{padding:10px 12px;cursor:pointer;border-bottom:1px solid var(--line);transition:background .15s;}
.search-result-item:hover{background:var(--green-100);}
.search-result-item:last-child{border-bottom:none;}
.search-result-name{font-size:13px;font-weight:600;color:var(--ink);}
.search-result-address{font-size:11px;color:var(--ink-soft);margin-top:2px;line-height:1.4;}

/* ---------- Summary ---------- */
.summary-card{background:var(--parchment);border-radius:var(--radius-m);padding:18px;}
.summary-card h4{font-size:14px;margin-bottom:12px;}
.mini-item{display:flex;justify-content:space-between;gap:12px;font-size:13px;padding:7px 0;color:var(--ink-soft);border-bottom:1px dashed var(--line);}
.mini-item:last-child{border-bottom:none;}
.mini-item b{color:var(--ink);font-weight:600;}
.pay-note{
  display:flex;gap:10px;align-items:flex-start;background:var(--green-100);border-radius:var(--radius-m);
  padding:14px 16px;margin-bottom:16px;font-size:13px;color:var(--green-900);line-height:1.55;
}
.pay-note svg{flex-shrink:0;color:var(--green-700);margin-top:1px;}

/* ---------- Sticky mobile pay bar ---------- */
.pay-sticky{
  position:fixed;left:0;right:0;bottom:0;z-index:120;display:none;align-items:center;gap:12px;
  background:rgba(255,255,255,.98);backdrop-filter:blur(12px);border-top:1px solid var(--line);
  padding:10px 14px calc(10px + env(safe-area-inset-bottom));box-shadow:0 -8px 24px rgba(15,42,31,.12);
}
.pay-sticky .ps-left{flex:1;min-width:0;}
.pay-sticky .ps-left b{font-family:var(--font-display);font-size:17px;display:block;line-height:1.15;}
.pay-sticky .ps-left span{font-size:11.5px;color:var(--ink-soft);}
.pay-sticky .btn{height:48px;padding:0 20px;flex-shrink:0;}
@media(max-width:900px){
  .pay-sticky{display:flex;}
  .with-pay-sticky{padding-bottom:84px;}
}

.empty-state{padding:52px 24px;text-align:center;}
.empty-state .ic{width:72px;height:72px;border-radius:50%;background:var(--green-100);color:var(--green-700);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;}
.empty-state h2{font-size:22px;margin-bottom:8px;}
.empty-state p{color:var(--ink-soft);font-size:14px;margin:0 0 22px;}
.empty-state .actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}
</style>
</head>
<body class="with-pay-sticky">

@include('shop.partials.header', ['activeNav' => 'shop'])

<main id="mainContent">
  <section class="section">
    <div class="wrap">
      <a href="{{ route('shop.index') }}" class="back-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 18-6-6 6-6"/></svg> {{ __('Back to store') }}
      </a>

      <div class="section-head" style="margin-top:18px;">
        <div>
          <span class="eyebrow">{{ __('Checkout') }}</span>
          <h1>{{ __('Complete your order') }}</h1>
        </div>
      </div>

      <ol class="steps" aria-label="Checkout steps">
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

      <div id="emptyCartState" class="card empty-state" style="display:none;">
        <div class="ic">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        </div>
        <h2>{{ __('Your cart is empty') }}</h2>
        <p>{{ __('Add at least one product to continue.') }}</p>
        <div class="actions">
          <a href="{{ route('shop.index') }}" class="btn btn-primary">{{ __('Go to store') }}</a>
          <a href="{{ route('shop.tracking') }}" class="btn btn-ghost">{{ __('Track order') }}</a>
        </div>
      </div>

      <form id="checkoutForm">
        <!-- Delivery Options -->
        <div class="card" id="stepDelivery">
          <h2 class="card-title"><span class="n">1</span> {{ __('Delivery Option') }}</h2>
          <div class="option-grid-two">
            <label class="option-card selected" id="opt-delivery">
              <input type="radio" name="need_delivery" value="yes" checked onchange="toggleDeliveryOptions()">
              <div class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="1.5"/><circle cx="18.5" cy="18.5" r="1.5"/></svg>
              </div>
              <div>
                <b>{{ __('Home Delivery') }}</b>
                <span>{{ __('Your order will be delivered to your door') }}</span>
              </div>
            </label>
            <label class="option-card" id="opt-pickup">
              <input type="radio" name="need_delivery" value="no" onchange="toggleDeliveryOptions()">
              <div class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v9h14v-9"/><path d="M9 19v-6h6v6"/></svg>
              </div>
              <div>
                <b>{{ __('Pickup') }}</b>
                <span>{{ __('Pick up your order from our store') }}</span>
              </div>
            </label>
          </div>
        </div>

        <!-- Customer Info -->
        <div class="card" id="stepCustomer">
          <h2 class="card-title"><span class="n">2</span> {{ __('Customer Information') }}</h2>
          <div class="form-grid">
            <div class="field">
              <label for="customerName">{{ __('Full Name') }} *</label>
              <input type="text" id="customerName" required autocomplete="name">
              <div class="field-error" id="err-customerName"></div>
            </div>
            <div class="field">
              <label for="customerPhone">{{ __('Phone Number') }} *</label>
              <input type="tel" id="customerPhone" required autocomplete="tel">
              <div class="field-error" id="err-customerPhone"></div>
            </div>
            <div class="field" style="grid-column:1/-1;">
              <label for="customerEmail">{{ __('Email') }} ({{ __('optional') }})</label>
              <input type="email" id="customerEmail" autocomplete="email">
              <div class="field-error" id="err-customerEmail"></div>
            </div>
          </div>
        </div>

        <!-- Delivery Address & Location -->
        <div class="card" id="deliveryAddressSection">
          <h2 class="card-title"><span class="n">3</span> {{ __('Delivery Location') }}</h2>

          <div class="option-grid-two" style="margin-bottom:14px;">
            <label class="option-card selected" id="opt-current-location">
              <input type="radio" name="location_type" value="current" checked onchange="toggleLocationType()">
              <div class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
              </div>
              <div>
                <b>{{ __('Current Location') }}</b>
                <span>{{ __('Use your current GPS location') }}</span>
              </div>
            </label>
            <label class="option-card" id="opt-other-location">
              <input type="radio" name="location_type" value="other" onchange="toggleLocationType()">
              <div class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </div>
              <div>
                <b>{{ __('Other Location') }}</b>
                <span>{{ __('Choose a location on the map or enter an address') }}</span>
              </div>
            </label>
          </div>

          <!-- Current Location Section -->
          <div class="location-box" id="currentLocationBox">
            <div class="field">
              <label for="deliveryAddress">{{ __('Delivery Address') }} *</label>
              <input type="text" id="deliveryAddress" placeholder="{{ __('Searching your location automatically...') }}">
              <div class="field-error" id="err-deliveryAddress" style="margin-top:8px;"></div>
            </div>
            <div class="location-box-head">
              <div class="location-status pending" id="locStatus">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <span>{{ __('Detecting your location...') }}</span>
              </div>
              <button type="button" class="btn btn-outline btn-sm" onclick="detectLocation()">{{ __('Refresh') }}</button>
            </div>
            <div class="location-coords" id="locCoords"></div>
            <div class="mini-map" id="mapPreview"></div>
          </div>

          <!-- Other Location Section -->
          <div class="location-box" id="otherLocationBox" style="display:none;">
            <div class="field">
              <label for="addressSearch">{{ __('Search Location') }}</label>
              <div style="display:flex;gap:8px;">
                <input type="text" id="addressSearch" placeholder="{{ __('Search for a location (e.g., Kariakoo Market)') }}" style="flex:1;" onkeypress="if(event.key === 'Enter') searchAddress()">
                <button type="button" class="btn btn-primary btn-sm" onclick="searchAddress()">{{ __('Search') }}</button>
              </div>
              <div class="field-error" id="err-addressSearch"></div>
            </div>

            <div id="searchResults" style="display:none;margin-bottom:12px;max-height:200px;overflow-y:auto;border:1px solid var(--line);border-radius:8px;"></div>

            <div class="field">
              <label for="manualAddress">{{ __('Delivery Address') }} *</label>
              <input type="text" id="manualAddress" placeholder="{{ __('Enter delivery address (e.g., Kiboriloni, Moshi)') }}">
              <div class="field-error" id="err-manualAddress"></div>
            </div>

            <div class="location-box-head">
              <div class="location-status pending" id="mapLocStatus">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><path d="M12 8h.01M11 12h2v4h-2z"/></svg>
                <span>{{ __('Tap on the map to select a delivery location') }}</span>
              </div>
            </div>
            <div class="location-coords" id="mapLocCoords"></div>
            <div class="mini-map" id="mapPreviewOther"></div>
            <div class="field-error" id="err-mapLocation" style="margin-top:8px;"></div>
          </div>
        </div>

        <div class="checkout-bottom-grid">
          <!-- Order Summary -->
          <div class="card" id="stepSummary">
            <h2 class="card-title"><span class="n">4</span> {{ __('Order Summary') }}</h2>
            <div id="checkoutItems"></div>
            <div style="border-top:1px solid var(--line);padding-top:16px;">
              <div class="sum-row"><span>{{ __('Subtotal') }}</span><span id="subtotal">TZS 0</span></div>
              <div class="sum-row"><span>{{ __('Delivery Distance') }}</span><span id="deliveryDistanceDisplay">{{ __('Choose location to calculate') }}</span></div>
              <div class="sum-row"><span>{{ __('Delivery Fee') }}</span><span id="deliveryFeeDisplay">{{ __('Choose location to calculate') }}</span></div>
              <div class="sum-row total"><span>{{ __('Total Now') }}</span><span id="checkoutTotal">TZS 0</span></div>
            </div>
          </div>

          <!-- Payment Section -->
          <div>
            <div class="pay-note">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/><path d="M6 15h4"/></svg>
              <span>{{ __('Pay securely using mobile money. After placing the order, you will be asked to enter a valid phone number such as 2557XXXXXXXX, 07XXXXXXXX, or 7XXXXXXXX.') }}</span>
            </div>
            <input type="hidden" name="payment_method" value="online">
            <button type="submit" id="placeOrderBtn" class="btn btn-primary" style="display:flex;justify-content:center;width:100%;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
              {{ __('Pay Now') }}
            </button>
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
    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>
    {{ __('Pay Now') }}
  </button>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let userLocation = { lat: null, lng: null };
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
          <div style="font-size:12px;color:var(--ink-soft);">${item.quantity} × TZS ${Number(item.price).toLocaleString()}</div>
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
          document.getElementById('deliveryFeeDisplay').textContent = data.is_free ? '{{ __('FREE') }}' : data.formatted_delivery_fee;
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
    document.getElementById('deliveryFeeDisplay').textContent = '{{ __('FREE') }}';
    updateTotal();
  }
}

function updateTotal() {
  const subtotal = calculateTotal();
  const total = subtotal + currentDeliveryFee;
  document.getElementById('subtotal').textContent = 'TZS ' + subtotal.toLocaleString();
  if (needDelivery === 'no') {
    document.getElementById('deliveryFeeDisplay').textContent = '{{ __('FREE') }}';
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

function detectLocation() {
  const statusEl = document.getElementById('locStatus');
  const coordsEl = document.getElementById('locCoords');
  const iconEl = statusEl.querySelector('svg');

  statusEl.classList.remove('pending', 'error');
  statusEl.querySelector('span').textContent = '{{ __('Detecting your location...') }}';
  iconEl.setAttribute('viewBox', '0 0 24 24');
  iconEl.innerHTML = '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>';

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      async (position) => {
        userLocation.lat = position.coords.latitude;
        userLocation.lng = position.coords.longitude;
        coordsEl.textContent = `${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}`;
        statusEl.querySelector('span').textContent = '{{ __('Location detected! Resolving address...') }}';
        initializeCheckoutMap();
        updateCheckoutMap(userLocation.lat, userLocation.lng);
        userLocationName = await reverseGeocode(userLocation.lat, userLocation.lng);
        document.getElementById('deliveryAddress').value = userLocationName;
        statusEl.querySelector('span').textContent = '{{ __('Location detected!') }}';
        fetchDeliveryFee();
        setFieldError('deliveryAddress', '');
      },
      (error) => {
        console.error('Geolocation error:', error);
        statusEl.classList.add('error');
        statusEl.querySelector('span').textContent = '{{ __('Failed to detect location') }}';
        iconEl.innerHTML = '<circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>';
        setFieldError('deliveryAddress', '{{ __('Please allow location access or choose another location') }}');
      }
    );
  } else {
    statusEl.classList.add('error');
    statusEl.querySelector('span').textContent = '{{ __('Geolocation not supported') }}';
  }
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

document.addEventListener('DOMContentLoaded', function() {
  initCart();
  setTimeout(hidePageLoader, 350);
  detectLocation();

  document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    if (!validateCustomer() || !validateAddressIfNeeded()) {
      return;
    }

    const placeOrderBtn = document.getElementById('placeOrderBtn');
    placeOrderBtn.disabled = true;
    placeOrderBtn.innerHTML = '<svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> {{ __('Placing Order...') }}';

    try {
      const requestBody = {
        customer_name: document.getElementById('customerName').value.trim(),
        customer_phone: document.getElementById('customerPhone').value.trim(),
        customer_email: document.getElementById('customerEmail').value.trim(),
        delivery_address: document.getElementById('deliveryAddress').value.trim(),
        delivery_latitude: needDelivery === 'yes' ? (document.querySelector('input[name="location_type"]:checked').value === 'current' ? userLocation.lat : selectedLocation.lat) : null,
        delivery_longitude: needDelivery === 'yes' ? (document.querySelector('input[name="location_type"]:checked').value === 'current' ? userLocation.lng : selectedLocation.lng) : null,
        delivery_fee: currentDeliveryFee,
        payment_method: 'online',
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
          title: '{{ __('Order placed!') }}',
          html: data.payment_message || '{{ __('Your order has been placed successfully!') }}',
          confirmButtonText: '{{ __('Track Order') }}'
        }).then(() => {
          window.location.href = data.tracking_url;
        });
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
