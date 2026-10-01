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
  $trackingCanonicalUrl = request()->fullUrl();
  $trackingTitle = isset($order)
      ? 'Track Order ' . $order->order_number . ' - Feedtan Store'
      : 'Track Order - Feedtan Store';
  $trackingDescription = isset($order)
      ? 'Track delivery updates, payment status, and order progress for ' . $order->order_number . ' at Feedtan Store.'
      : 'Track your Feedtan Store order status, delivery progress, and payment updates online.';

  $statusLabels = [
      'pending' => __('Pending'),
      'confirmed' => __('Confirmed'),
      'preparing' => __('Preparing'),
      'ready' => __('Ready'),
      'out_for_delivery' => __('Out for delivery'),
      'delivered' => __('Delivered'),
      'cancelled' => __('Cancelled'),
  ];
  $statusColors = [
      'pending' => 'gray',
      'confirmed' => 'green',
      'preparing' => 'orange',
      'ready' => 'orange',
      'out_for_delivery' => 'blue',
      'delivered' => 'green',
      'cancelled' => 'red',
  ];
  $statusIndex = [
      'pending' => 0,
      'confirmed' => 1,
      'preparing' => 2,
      'ready' => 3,
      'out_for_delivery' => 4,
      'delivered' => 5,
  ];
  $orderProgress = (isset($order) && $order->status !== 'cancelled')
      ? (int) round((($statusIndex[$order->status] ?? 0) / 5) * 100)
      : 0;
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>{{ $trackingTitle }}</title>
<meta name="description" content="{{ $trackingDescription }}">
<meta name="robots" content="noindex,nofollow,noarchive">
<meta name="author" content="Feedtan Store">
<meta name="theme-color" content="#123328">
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
@include('shop.partials.styles')
<style>
/* ---------- Track form ---------- */
.track-card{background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow-card);}
.track-search{display:flex;gap:10px;align-items:flex-end;}
.track-search .field{flex:1;margin-bottom:0;min-width:0;}
.track-search .field input{height:50px;}
.track-search .btn{height:50px;flex-shrink:0;}

/* ---------- Order card ---------- */
.order-card{background:var(--paper);border:1px solid var(--line);border-radius:var(--radius-xl);padding:22px;box-shadow:var(--shadow-card);}
.order-hero{
  display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;
  background:var(--green-900);color:#fff;border-radius:var(--radius);padding:20px 22px;
}
.order-hero h2{color:#fff;font-size:22px;}
.order-hero .placed-on{color:#c7d6cd;font-size:13px;margin-top:6px;font-family:var(--font-mono);}
.order-actions{display:flex;gap:8px;flex-wrap:wrap;}
.order-actions .btn-ghost{background:rgba(255,255,255,.12);color:#fff;}
.order-actions .btn-ghost:hover{background:rgba(255,255,255,.2);}
.stat b.small{font-size:13px;font-weight:600;}
.progress-wrap{margin-top:22px;}
.progress-head{
  display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px;
  font-size:12.5px;font-weight:700;color:var(--ink-soft);
}
.progress-head b{font-family:var(--font-mono);color:var(--green-800);}

/* ---------- Timeline ---------- */
.tl{padding-left:2px;}
.tl-body{padding-right:4px;}
.tl-head{display:flex;align-items:baseline;justify-content:space-between;gap:10px;flex-wrap:wrap;}
.tl-head b{font-size:14px;color:var(--green-900);}
.tl-time{font-family:var(--font-mono);font-size:11.5px;color:var(--ink-faint);}
.tl-desc{font-size:13px;color:var(--ink-soft);margin-top:3px;line-height:1.55;}

/* ---------- Items / location ---------- */
.order-items li b{color:var(--ink);font-size:14px;}
.order-items .qty{color:var(--ink-faint);font-size:12.5px;}
.order-items li > b{font-family:var(--font-mono);color:var(--green-800);white-space:nowrap;}
.loc-card{background:var(--green-050);border:1px solid var(--line);border-radius:var(--radius-m);padding:16px;}
.loc-card p{margin:0;font-size:14.5px;}

/* ---------- Responsive ---------- */
@media (max-width:900px){
  .stats{grid-template-columns:repeat(2,1fr);}
  .order-hero{padding:18px;}
  .order-hero h2{font-size:19px;}
}
@media (max-width:640px){
  .track-search{flex-direction:column;align-items:stretch;}
  .track-search .btn{width:100%;}
  .order-card{padding:16px;}
  .track-card{padding:16px;}
}
@media (max-width:420px){
  .stats{grid-template-columns:1fr;}
  .order-actions{width:100%;}
  .order-actions .btn{flex:1;}
}
</style>
</head>
<body>

@include('shop.partials.header', ['activeNav' => 'track'])

<main id="mainContent">
  <section class="section" style="padding-top:30px;">
    <div class="wrap">
      <a href="{{ route('shop.index') }}" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> {{ __('Back to store') }}
      </a>

      <div class="sec-head" style="margin-bottom:22px;">
        <span class="eyebrow">{{ __('Track') }}</span>
        <h1 style="font-size:clamp(26px,3.4vw,36px);">{{ __('Track your order') }}</h1>
        <p>{{ __('Enter your order number to see every step, from our shelf to your door.') }}</p>
      </div>

      <div class="track-card">
        <form id="trackForm">
          <div class="track-search">
            <div class="field">
              <label for="orderNumber">{{ __('Order Number') }}</label>
              <input type="text" id="orderNumber" placeholder="{{ __('Enter your order number') }}" value="{{ request('order', '') }}" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="fa-solid fa-magnifying-glass"></i> {{ __('Track Order') }}
            </button>
          </div>
        </form>
      </div>

      @if(isset($order))
      <script>
        window.shortCustomerReference = @json($order->short_customer_reference);
      </script>

      <div class="order-card reveal in" id="orderDetails" style="margin-top:22px;">
        <div class="order-hero">
          <div>
            <h2>{{ __('Order') }} {{ $order->short_customer_reference }}</h2>
            <p class="placed-on">{{ __('Placed on') }} {{ $order->created_at->format('M d, Y • h:i A') }}</p>
          </div>
          <div class="order-actions">
            <a href="{{ route('shop.tracking.pdf', ['orderNumber' => $order->tracking_token ?? $order->order_number]) }}" class="btn btn-ghost btn-sm">
              <i class="fa-solid fa-download"></i> {{ __('Download PDF') }}
            </a>
            @if(($order->payment_method ?? 'cash') === 'online' && ($order->payment_status ?? 'pending') !== 'paid')
              <button type="button" class="btn btn-primary btn-sm" id="payNowBtn" data-order="{{ $order->order_number }}" data-phone="{{ $order->customer_phone }}">
                <i class="fa-solid fa-circle-check"></i> {{ __('Pay Now') }}
              </button>
            @endif
          </div>
        </div>

        <div class="stats">
          <div class="stat">
            <span><i class="fa-regular fa-clock"></i> {{ __('Status') }}</span>
            <b><span class="pill pill-{{ $statusColors[$order->status] ?? 'gray' }}">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</span></b>
          </div>
          <div class="stat">
            <span><i class="fa-regular fa-user"></i> {{ __('Customer') }}</span>
            <b class="small">{{ $order->customer_name }}</b>
          </div>
          <div class="stat">
            <span><i class="fa-solid fa-wallet"></i> {{ __('Total') }}</span>
            <b>TZS {{ number_format($order->total, 0) }}</b>
          </div>
          <div class="stat">
            <span><i class="fa-solid fa-credit-card"></i> {{ __('Payment') }}</span>
            <b class="small">{{ ucfirst($order->payment_method ?? 'Cash') }} · {{ ucfirst($order->payment_status ?? 'Pending') }}</b>
          </div>
        </div>

        <div class="progress-wrap">
          <div class="progress-head">
            <span>{{ __('Order progress') }}</span>
            <b>{{ $orderProgress }}%</b>
          </div>
          <div class="progress-track"><div class="progress-fill" style="width:{{ $orderProgress }}%;"></div></div>
        </div>

        @if($order->status !== 'cancelled')
        <div style="margin-top:28px;">
          <h3 class="h3-title">{{ __('Order Timeline') }}</h3>
          <div class="tl">
            @php $isDone = in_array($order->status, ['confirmed','preparing','ready','out_for_delivery','delivered']); @endphp
            <div class="tl-item {{ $order->status === 'pending' ? 'current' : 'done' }}">
              <span class="tl-dot">
                @if($order->status !== 'pending')
                  <i class="fa-solid fa-check"></i>
                @else
                  <b style="font-size:10px;">1</b>
                @endif
              </span>
              <div class="tl-body">
                <div class="tl-head"><b>{{ __('Order Placed') }}</b><span class="tl-time">{{ $order->created_at->format('M d, h:i A') }}</span></div>
                <p class="tl-desc">{{ __('Your order has been placed successfully.') }}</p>
              </div>
            </div>

            @if($order->status !== 'pending')
            <div class="tl-item {{ $order->status === 'confirmed' ? 'current' : ($isDone ? 'done' : 'todo') }}">
              <span class="tl-dot">
                @if($isDone)
                  <i class="fa-solid fa-check"></i>
                @elseif($order->status === 'confirmed')
                  <b style="font-size:10px;">2</b>
                @endif
              </span>
              <div class="tl-body">
                <div class="tl-head"><b>{{ __('Order Confirmed') }}</b><span class="tl-time">{{ $order->created_at->format('M d, h:i A') }}</span></div>
                <p class="tl-desc">{{ __('We have received and confirmed your order.') }}</p>
              </div>
            </div>
            @endif

            @if(in_array($order->status, ['preparing', 'ready', 'out_for_delivery', 'delivered']))
            <div class="tl-item {{ $order->status === 'preparing' ? 'current' : 'done' }}">
              <span class="tl-dot">
                @if($order->status === 'preparing')
                  <b style="font-size:10px;">3</b>
                @else
                  <i class="fa-solid fa-check"></i>
                @endif
              </span>
              <div class="tl-body">
                <div class="tl-head"><b>{{ __('Preparing Order') }}</b><span class="tl-time">{{ $order->created_at->addMinutes(30)->format('M d, h:i A') }}</span></div>
                <p class="tl-desc">{{ __('Our team is picking and packing your items.') }}</p>
              </div>
            </div>
            @endif

            @if(in_array($order->status, ['ready', 'out_for_delivery', 'delivered']))
            <div class="tl-item {{ $order->status === 'ready' ? 'current' : 'done' }}">
              <span class="tl-dot">
                @if($order->status === 'ready')
                  <b style="font-size:10px;">4</b>
                @else
                  <i class="fa-solid fa-check"></i>
                @endif
              </span>
              <div class="tl-body">
                <div class="tl-head"><b>{{ __('Ready for Delivery') }}</b><span class="tl-time">{{ $order->created_at->addMinutes(60)->format('M d, h:i A') }}</span></div>
                <p class="tl-desc">{{ __('Your order is packed and ready to go.') }}</p>
              </div>
            </div>
            @endif

            @if(in_array($order->status, ['out_for_delivery', 'delivered']))
            <div class="tl-item {{ $order->status === 'out_for_delivery' ? 'current' : 'done' }}">
              <span class="tl-dot">
                @if($order->status === 'out_for_delivery')
                  <b style="font-size:10px;">5</b>
                @else
                  <i class="fa-solid fa-check"></i>
                @endif
              </span>
              <div class="tl-body">
                <div class="tl-head"><b>{{ __('Out for Delivery') }}</b><span class="tl-time">{{ $order->created_at->addMinutes(90)->format('M d, h:i A') }}</span></div>
                <p class="tl-desc">{{ __('Your order is on its way to you.') }}</p>
              </div>
            </div>
            @endif

            @if($order->status === 'delivered')
            <div class="tl-item done current">
              <span class="tl-dot"><i class="fa-solid fa-check"></i></span>
              <div class="tl-body">
                <div class="tl-head"><b>{{ __('Delivered') }}</b><span class="tl-time">{{ $order->updated_at->format('M d, h:i A') }}</span></div>
                <p class="tl-desc">{{ __('Your order has been delivered. Thank you!') }}</p>
              </div>
            </div>
            @endif
          </div>
        </div>
        @else
        <div style="margin-top:28px;">
          <h3 class="h3-title">{{ __('Order Timeline') }}</h3>
          <div class="alert-card">
            <i class="fa-solid fa-circle-xmark" style="flex-shrink:0;margin-top:1px;"></i>
            <span>{{ __('This order was cancelled. Please contact us if you have any questions.') }}</span>
          </div>
        </div>
        @endif

        @if($order->items && $order->items->count() > 0)
        <div style="margin-top:28px;">
          <h3 class="h3-title">{{ __('Order Items') }}</h3>
          <ul class="order-items">
            @foreach($order->items as $item)
              <li>
                <span><b>{{ $item->product->name ?? 'Product' }}</b> <span class="qty">· {{ $item->quantity }} × TZS {{ number_format($item->price ?? 0, 0) }}</span></span>
                <b>TZS {{ number_format(($item->price ?? 0) * $item->quantity, 0) }}</b>
              </li>
            @endforeach
          </ul>
        </div>
        @endif

        @if($order->delivery_address || ($order->delivery_latitude && $order->delivery_longitude))
        <div style="margin-top:28px;">
          <h3 class="h3-title">{{ __('Delivery Location') }}</h3>
          <div class="loc-card">
            @if($order->delivery_address)
              <p>{{ $order->delivery_address }}</p>
            @else
              <p style="color:var(--ink-soft);">{{ __('Location captured from customer device.') }}</p>
            @endif
            @if($order->delivery_latitude && $order->delivery_longitude)
              <div class="map-container" style="margin-top:14px;">
                <div id="tracking-map" style="width:100%;height:100%;"></div>
              </div>
              <p class="mono" style="margin-top:10px;font-size:12px;color:var(--ink-faint);">
                {{ __('Location') }}: {{ number_format($order->delivery_latitude, 6) }}, {{ number_format($order->delivery_longitude, 6) }}
              </p>
            @endif
          </div>
        </div>
        @endif
      </div>
      @endif
    </div>
  </section>
</main>

@include('shop.partials.footer')
@include('shop.partials.cart-drawer', ['showBottomBar' => true])
@include('shop.partials.cart-js')

@if($order && $order->delivery_latitude && $order->delivery_longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
@endif
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if($order && $order->delivery_latitude && $order->delivery_longitude)
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endif
<script>
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
    ? '{{ __('Check your phone to confirm the USSD push.') }}'
    : (s === 'SUCCESS' || s === 'SETTLED')
      ? '{{ __('Payment completed successfully.') }}'
      : (s === 'FAILED' || s === 'DECLINED' || s === 'CANCELLED')
        ? '{{ __('Payment did not complete. You can try again later.') }}'
        : '{{ __('Processing payment...') }}';
  const timer = typeof remainingSeconds === 'number' ? ('<div style="margin-top:8px;color:#6b7280;">{{ __('Time remaining:') }} ' + remainingSeconds + 's</div>') : '';
  return '{{ __('Order number') }}: <b>' + (window.shortCustomerReference || orderNumber) + '</b><br>' +
    '{{ __('Payment status') }}: <b>' + s + '</b><br><span style="color:#6b7280;">' + note + '</span>' +
    timer +
    '<div style="margin-top:10px;">' +
    '<a href="' + trackingUrl + '">{{ __('Track your order') }}</a> Â· <a href="' + pdfUrl + '">{{ __('Download order PDF') }}</a>' +
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
    const message = payload && payload.message ? payload.message : '{{ __('Failed to initiate payment.') }}';
    throw new Error(message);
  }
  return payload;
}

async function promptPaymentPhoneNumber(defaultPhone = '') {
  if (window.Swal) {
    const result = await Swal.fire({
      title: '{{ __('Choose payment number') }}',
      input: 'text',
      inputValue: defaultPhone || '',
      inputLabel: '{{ __('Phone number') }}',
      inputPlaceholder: '255712345678',
      confirmButtonText: '{{ __('Continue') }}',
      showCancelButton: true,
      inputValidator: (value) => {
        if (!value || !value.trim()) {
          return '{{ __('Enter the number to receive the USSD prompt.') }}';
        }
        const digits = value.replace(/\D+/g, '');
        if (!(digits.length === 12 && digits.startsWith('255')) && !(digits.length === 10 && digits.startsWith('0')) && !(digits.length === 9 && digits.startsWith('7'))) {
          return '{{ __('Use a valid mobile money number like 255712345678.') }}';
        }
        return null;
      }
    });

    if (!result.isConfirmed) {
      return null;
    }

    return result.value.trim();
  }

  const fallback = window.prompt('{{ __('Enter the number to receive the USSD prompt') }}', defaultPhone || '');
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
        title: success ? '{{ __('Payment successful') }}' : (failed ? '{{ __('Payment failed') }}' : '{{ __('Payment status') }}'),
        html: buildPaymentHtml(trackingIdentifier, finalStatus, trackingUrl, pdfUrl),
        showConfirmButton: true,
        confirmButtonText: success ? '{{ __('Continue') }}' : '{{ __('Close') }}',
        showCancelButton: false
      });
    };

    Swal.fire({
      title: '{{ __('Processing mobile money payment') }}',
      html: buildPaymentHtml(trackingIdentifier, 'PENDING', trackingUrl, pdfUrl, 60),
      allowOutsideClick: false,
      showCancelButton: true,
      cancelButtonText: '{{ __('Close') }}',
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
        const startMs = Date.now();
        timeoutId = setTimeout(() => {
          stop();
          Swal.hideLoading();
          Swal.update({
            icon: 'info',
            title: '{{ __('Payment window ended') }}',
            html: buildPaymentHtml(trackingIdentifier, 'PENDING', trackingUrl, pdfUrl, 0) + '<div style="margin-top:8px;color:#6b7280;">{{ __('Payment status check stopped after 1 minute.') }}</div>',
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
        Swal.fire({ title: '{{ __('Starting payment') }}', text: '{{ __('Sending USSD push to your phone...') }}', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
      }
      await initiatePayment(trackingIdentifier, phoneNumber);
      if (window.Swal) Swal.close();
      await openPaymentProgressModal(trackingIdentifier, trackingUrl, pdfUrl);
    } catch (e) {
      if (window.Swal) Swal.fire({ icon: 'error', title: '{{ __('Payment not started') }}', text: e.message || '{{ __('Failed to initiate payment.') }}' });
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
  .bindPopup('<strong>{{ __('Store') }}</strong>');

L.circleMarker([trackingOrderLat, trackingOrderLng], {
  radius: 8,
  fillColor: '#f97316',
  color: '#fff',
  weight: 2,
  fillOpacity: 0.85
})
  .addTo(trackingMap)
  .bindPopup('<strong>{{ __('Delivery location') }}</strong><br>{{ addslashes($order->customer_name) }}<br>{{ addslashes($order->delivery_address) }}');

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

document.addEventListener('DOMContentLoaded', () => {
  initCart();
  setTimeout(hidePageLoader, 300);
});

setTimeout(hidePageLoader, 350);
window.addEventListener('load', hidePageLoader);
</script>
</body>
</html>
