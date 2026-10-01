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
.track-detail-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:22px;}
.detail-card{background:var(--paper);border:1px solid var(--line);border-radius:var(--radius-m);padding:16px;}
.detail-card h3{font-size:15px;margin-bottom:12px;}
.detail-list{margin:0;display:flex;flex-direction:column;gap:10px;}
.detail-list > div{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;}
.detail-list dt{font-size:12.5px;color:var(--ink-faint);flex-shrink:0;}
.detail-list dd{margin:0;font-size:13.5px;font-weight:600;color:var(--ink);text-align:right;overflow-wrap:anywhere;}
.detail-list dd.mono{font-family:var(--font-mono);font-weight:700;color:var(--green-800);}
.detail-grand{border-top:1px solid var(--line);padding-top:10px;}
.detail-grand dd{font-size:15px;}
.loc-card{background:var(--green-050);border:1px solid var(--line);border-radius:var(--radius-m);padding:16px;}
.loc-card p{margin:0;font-size:14.5px;}

/* ---------- Payment popup ---------- */
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
.pay-modal-field{margin-top:16px;text-align:left;}
.pay-modal-field label{display:block;font-size:12.5px;font-weight:700;color:var(--ink-soft);margin-bottom:6px;}
.pay-modal-field input{
  width:100%;height:48px;border:1px solid var(--line);border-radius:var(--radius-m);
  background:#fff;padding:0 14px;font-family:var(--font-mono);font-size:15px;
}
.pay-modal-field input:focus{outline:3px solid var(--orange-500);outline-offset:1px;border-color:var(--orange-500);}
.pay-modal-field-error{font-size:12.5px;color:var(--red);margin-top:6px;}
.pay-modal-field-error[hidden]{display:none;}
.pay-modal-actions{display:flex;gap:10px;margin-top:20px;}
.pay-modal-actions .btn{flex:1;justify-content:center;}
.pay-modal-actions .btn[hidden]{display:none;}
html.pay-modal-open,html.pay-modal-open body{overflow:hidden;}

/* ---------- Responsive ---------- */
@media (max-width:900px){
  .stats{grid-template-columns:repeat(2,1fr);}
  .track-detail-grid{grid-template-columns:1fr;}
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
              <button type="button" class="btn btn-primary btn-sm" id="payNowBtn" data-order="{{ $order->order_number }}" data-phone="{{ $order->customer_phone }}" data-total="TZS {{ number_format($order->total ?? 0, 0) }}">
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

        <div class="track-detail-grid">
          <section class="detail-card" aria-labelledby="trackingCustomerTitle">
            <h3 id="trackingCustomerTitle">{{ __('Customer details') }}</h3>
            <dl class="detail-list">
              <div><dt>{{ __('Name') }}</dt><dd>{{ $order->customer_name ?: '—' }}</dd></div>
              <div><dt>{{ __('Phone') }}</dt><dd class="mono">{{ $order->customer_phone ?: '—' }}</dd></div>
              <div><dt>{{ __('Email') }}</dt><dd>{{ $order->customer_email ?: '—' }}</dd></div>
              <div><dt>{{ __('Delivery address') }}</dt><dd>{{ $order->delivery_address ?: '—' }}</dd></div>
            </dl>
          </section>

          <section class="detail-card" aria-labelledby="trackingTotalTitle">
            <h3 id="trackingTotalTitle">{{ __('Order total') }}</h3>
            <dl class="detail-list">
              <div><dt>{{ __('Subtotal') }}</dt><dd class="mono">TZS {{ number_format($order->subtotal ?? 0, 0) }}</dd></div>
              <div><dt>{{ __('Discount') }}</dt><dd class="mono">TZS {{ number_format($order->discount ?? 0, 0) }}</dd></div>
              <div><dt>{{ __('Delivery fee') }}</dt><dd class="mono">TZS {{ number_format($order->delivery_fee ?? 0, 0) }}</dd></div>
              <div class="detail-grand"><dt>{{ __('Total') }}</dt><dd class="mono">TZS {{ number_format($order->total ?? 0, 0) }}</dd></div>
            </dl>
          </section>

          <section class="detail-card" aria-labelledby="trackingPaymentTitle">
            <h3 id="trackingPaymentTitle">{{ __('Payment data') }}</h3>
            <dl class="detail-list">
              <div><dt>{{ __('Method') }}</dt><dd>{{ ucfirst($order->payment_method ?? 'Cash') }}</dd></div>
              <div><dt>{{ __('Status') }}</dt><dd>{{ ucfirst($order->payment_status ?? 'Pending') }}</dd></div>
              <div><dt>{{ __('Gateway status') }}</dt><dd class="mono">{{ $order->clickpesa_status ?: '—' }}</dd></div>
              <div><dt>{{ __('Transaction ID') }}</dt><dd class="mono">{{ $order->payment_transaction_id ?: '—' }}</dd></div>
              <div><dt>{{ __('Order reference') }}</dt><dd class="mono">{{ $order->payment_order_reference ?: '—' }}</dd></div>
              @if(isset($paymentUpdate) && $paymentUpdate)
                <div><dt>{{ __('Last update') }}</dt><dd>{{ $paymentUpdate->created_at?->format('M d, Y • h:i A') }}</dd></div>
              @endif
            </dl>
          </section>
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

@if(isset($order))
<!-- Payment popup -->
<div class="pay-modal" id="payModal" hidden>
  <div class="pay-modal-backdrop" data-pay-modal-close></div>
  <div class="pay-modal-box" role="dialog" aria-modal="true" aria-labelledby="payModalTitle">
    <button type="button" class="pay-modal-x" data-pay-modal-close aria-label="{{ __('Close') }}">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="pay-modal-icon" id="payModalIcon"><i class="fa-solid fa-circle-check"></i></div>
    <h2 class="pay-modal-title" id="payModalTitle">{{ __('Processing mobile money payment') }}</h2>
    <div class="pay-modal-rows" id="payModalBody"></div>

    <div class="pay-modal-count" id="payModalCount" hidden>01:00</div>
    <div class="pay-modal-bar" id="payModalBar" hidden><span></span></div>
    <p class="pay-modal-hint" id="payModalHint" hidden></p>

    <div class="pay-modal-field" id="payModalPhoneField" hidden>
      <label for="payModalPhone">{{ __('Phone number') }}</label>
      <input type="tel" id="payModalPhone" inputmode="tel" autocomplete="tel" placeholder="255712345678">
      <p class="pay-modal-field-error" id="payModalPhoneError" hidden></p>
    </div>

    <div class="pay-modal-actions">
      <button type="button" class="btn btn-outline" id="payModalCancel" data-pay-modal-close>{{ __('Close') }}</button>
      <button type="button" class="btn btn-primary" id="payModalConfirm">{{ __('Continue') }}</button>
    </div>
  </div>
</div>
@endif

@include('shop.partials.footer')
@include('shop.partials.cart-drawer', ['showBottomBar' => true])
@include('shop.partials.cart-js')

@if($order && $order->delivery_latitude && $order->delivery_longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
@endif
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

const TRACK_PAY_WAIT_SECONDS = 60;
let trackPayWaitTimer = null;
let trackPayPollTimer = null;
let trackPayPhoneResolver = null;
let trackPayBusy = false;

const TRACK_PAY_MODAL_TONES = {
  phone: { icon: 'fa-solid fa-mobile-screen-button', tone: 'phone' },
  starting: { icon: 'fa-solid fa-spinner fa-spin', tone: 'starting' },
  waiting: { icon: 'fa-solid fa-spinner fa-spin', tone: 'waiting' },
  success: { icon: 'fa-solid fa-circle-check', tone: 'success' },
  failed: { icon: 'fa-solid fa-circle-xmark', tone: 'failed' },
  timeout: { icon: 'fa-solid fa-clock', tone: 'timeout' },
  'start-failed': { icon: 'fa-solid fa-circle-xmark', tone: 'failed' }
};

const TRACK_PAY_MODAL_COPY = {
  phone: { title: '{{ __('Choose payment number') }}', hint: '{{ __('Enter the number that should receive the mobile money prompt.') }}' },
  starting: { title: '{{ __('Starting payment') }}', hint: '{{ __('Sending the mobile money prompt to your phone...') }}' },
  waiting: { title: '{{ __('Processing mobile money payment') }}', hint: '{{ __('Check your phone and approve the mobile money prompt to finish paying.') }}' },
  success: { title: '{{ __('Payment successful') }}', hint: '{{ __('Payment completed successfully.') }}' },
  failed: { title: '{{ __('Payment failed') }}', hint: '{{ __('Payment did not complete. You can try again.') }}' },
  timeout: { title: '{{ __('Payment window ended') }}', hint: '{{ __('Payment status check stopped after 1 minute.') }}' },
  'start-failed': { title: '{{ __('Payment not started') }}', hint: '' }
};

const trackPayModal = { root: null, box: null, icon: null, title: null, body: null, count: null, bar: null, hint: null, phoneField: null, phone: null, phoneError: null, confirm: null, cancel: null };
const trackPayState = { phase: 'phone', remaining: TRACK_PAY_WAIT_SECONDS, status: '', orderRef: '', amount: '', trackingUrl: '', pdfUrl: '', phone: '', error: '', retry: 'phone' };

function formatTrackCountdown(totalSeconds) {
  const s = Math.max(0, Math.floor(totalSeconds));
  return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
}

function cacheTrackPayModal() {
  trackPayModal.root = document.getElementById('payModal');
  if (!trackPayModal.root) return;
  trackPayModal.box = trackPayModal.root.querySelector('.pay-modal-box');
  trackPayModal.icon = document.getElementById('payModalIcon');
  trackPayModal.title = document.getElementById('payModalTitle');
  trackPayModal.body = document.getElementById('payModalBody');
  trackPayModal.count = document.getElementById('payModalCount');
  trackPayModal.bar = document.getElementById('payModalBar');
  trackPayModal.hint = document.getElementById('payModalHint');
  trackPayModal.phoneField = document.getElementById('payModalPhoneField');
  trackPayModal.phone = document.getElementById('payModalPhone');
  trackPayModal.phoneError = document.getElementById('payModalPhoneError');
  trackPayModal.confirm = document.getElementById('payModalConfirm');
  trackPayModal.cancel = document.getElementById('payModalCancel');
}

function stopTrackPayWaitTimers() {
  if (trackPayWaitTimer) { clearInterval(trackPayWaitTimer); trackPayWaitTimer = null; }
  if (trackPayPollTimer) { clearInterval(trackPayPollTimer); trackPayPollTimer = null; }
}

function trackPayModalRow(label, value) {
  return '<div class="pay-modal-row"><span>' + label + '</span><b>' + value + '</b></div>';
}

function renderTrackPayModal() {
  if (!trackPayModal.root) return;
  const tone = TRACK_PAY_MODAL_TONES[trackPayState.phase] || TRACK_PAY_MODAL_TONES.waiting;
  const copy = TRACK_PAY_MODAL_COPY[trackPayState.phase] || TRACK_PAY_MODAL_COPY.waiting;
  const showTimer = trackPayState.phase === 'waiting';

  trackPayModal.box.className = 'pay-modal-box tone-' + tone.tone;
  trackPayModal.icon.innerHTML = '<i class="' + tone.icon + '"></i>';
  trackPayModal.title.textContent = copy.title;
  trackPayModal.hint.textContent = trackPayState.phase === 'start-failed' && trackPayState.error ? trackPayState.error : copy.hint;
  trackPayModal.hint.hidden = false;

  const rows = [trackPayModalRow('{{ __('Order number') }}', window.shortCustomerReference || trackPayState.orderRef)];
  if (trackPayState.amount) {
    rows.push(trackPayModalRow('{{ __('Amount') }}', trackPayState.amount));
  }
  if (trackPayState.phone) {
    rows.push(trackPayModalRow('{{ __('Payment number') }}', trackPayState.phone));
  }
  if (trackPayState.status && trackPayState.phase !== 'phone') {
    rows.push(trackPayModalRow('{{ __('Payment status') }}', trackPayState.status));
  }
  trackPayModal.body.innerHTML = rows.join('');

  if (trackPayState.phase === 'success') {
    const separator = document.createTextNode(' ');
    const trackLink = document.createElement('a');
    trackLink.href = trackPayState.trackingUrl || '#';
    trackLink.textContent = '{{ __('Track your order') }}';
    const pdfLink = document.createElement('a');
    pdfLink.href = trackPayState.pdfUrl || '#';
    pdfLink.textContent = '{{ __('Download order PDF') }}';
    trackPayModal.hint.appendChild(document.createElement('br'));
    trackPayModal.hint.appendChild(trackLink);
    trackPayModal.hint.appendChild(separator);
    trackPayModal.hint.appendChild(document.createTextNode('·'));
    trackPayModal.hint.appendChild(document.createTextNode(' '));
    trackPayModal.hint.appendChild(pdfLink);
  }

  trackPayModal.phoneField.hidden = trackPayState.phase !== 'phone';
  if (trackPayState.phase !== 'phone' && trackPayModal.phoneError) {
    trackPayModal.phoneError.hidden = true;
  }

  trackPayModal.count.hidden = !showTimer;
  trackPayModal.bar.hidden = !showTimer;
  if (showTimer) {
    trackPayModal.count.textContent = formatTrackCountdown(trackPayState.remaining);
    const pct = Math.max(0, Math.min(100, (trackPayState.remaining / TRACK_PAY_WAIT_SECONDS) * 100));
    trackPayModal.bar.firstElementChild.style.width = pct + '%';
  }

  const confirmLabels = {
    phone: '{{ __('Continue') }}',
    success: '{{ __('Refresh status') }}',
    failed: '{{ __('Try again') }}',
    timeout: '{{ __('Try again') }}',
    'start-failed': '{{ __('Try again') }}'
  };
  trackPayModal.confirm.hidden = trackPayState.phase === 'starting' || trackPayState.phase === 'waiting';
  trackPayModal.confirm.textContent = confirmLabels[trackPayState.phase] || confirmLabels.phone;
  trackPayModal.cancel.hidden = trackPayState.phase === 'starting';
}

function openTrackPayModal() {
  if (!trackPayModal.root) return;
  trackPayModal.root.hidden = false;
  document.documentElement.classList.add('pay-modal-open');
  renderTrackPayModal();
}

function closeTrackPayModal() {
  stopTrackPayWaitTimers();
  trackPayBusy = false;
  if (trackPayPhoneResolver) {
    trackPayPhoneResolver(null);
    trackPayPhoneResolver = null;
  }
  if (!trackPayModal.root) return;
  trackPayModal.root.hidden = true;
  document.documentElement.classList.remove('pay-modal-open');
}

function handleTrackPayModalClose() {
  if (trackPayState.phase === 'starting') return;
  closeTrackPayModal();
}

function isValidTrackPayPhone(value) {
  const digits = String(value || '').replace(/\D+/g, '');
  return (digits.length === 12 && digits.startsWith('255')) ||
    (digits.length === 10 && digits.startsWith('0')) ||
    (digits.length === 9 && digits.startsWith('7'));
}

async function handleTrackPayConfirm() {
  if (trackPayState.phase === 'phone') {
    const value = trackPayModal.phone.value.trim();
    if (!value) {
      trackPayModal.phoneError.textContent = '{{ __('Enter the number to receive the USSD prompt.') }}';
      trackPayModal.phoneError.hidden = false;
      return;
    }
    if (!isValidTrackPayPhone(value)) {
      trackPayModal.phoneError.textContent = '{{ __('Use a valid mobile money number like 255712345678.') }}';
      trackPayModal.phoneError.hidden = false;
      return;
    }
    trackPayModal.phoneError.hidden = true;
    const resolver = trackPayPhoneResolver;
    trackPayPhoneResolver = null;
    if (resolver) resolver(value);
    return;
  }

  if (trackPayState.phase === 'success') {
    window.location.reload();
    return;
  }

  if (trackPayState.retry === 'phone') {
    trackPayState.phase = 'phone';
    renderTrackPayModal();
    return;
  }

  runTrackPayment();
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

function promptPaymentPhoneNumber(defaultPhone = '') {
  return new Promise((resolve) => {
    if (!trackPayModal.root) {
      const fallback = window.prompt('{{ __('Enter the number to receive the USSD prompt') }}', defaultPhone || '');
      resolve(fallback ? fallback.trim() : null);
      return;
    }

    if (trackPayPhoneResolver) {
      trackPayPhoneResolver(null);
    }
    trackPayPhoneResolver = resolve;
    trackPayState.phase = 'phone';
    trackPayState.status = '';
    trackPayState.error = '';
    trackPayState.retry = 'phone';
    if (defaultPhone && !trackPayModal.phone.value) {
      trackPayModal.phone.value = defaultPhone;
    }
    trackPayModal.phoneError.hidden = true;
    openTrackPayModal();
    setTimeout(() => trackPayModal.phone.focus(), 50);
  });
}

async function pollTrackPayStatus() {
  if (trackPayState.phase !== 'waiting' || !trackPayState.orderRef) return;
  try {
    const res = await fetch('/api/shop/orders/' + encodeURIComponent(trackPayState.orderRef) + '/payment-status', {
      method: 'GET',
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin'
    });
    const payload = await res.json().catch(() => ({}));
    const raw = extractPaymentStatus(payload);
    if (!raw) {
      trackPayState.status = 'PROCESSING';
      renderTrackPayModal();
      return;
    }

    const normalized = formatPaymentStatus(raw);
    trackPayState.status = normalized;
    if (normalized === 'SUCCESS' || normalized === 'SETTLED') {
      trackPayState.phase = 'success';
      trackPayState.retry = 'payment';
      stopTrackPayWaitTimers();
      renderTrackPayModal();
    } else if (['FAILED', 'DECLINED', 'CANCELLED'].includes(normalized)) {
      trackPayState.phase = 'failed';
      trackPayState.retry = 'payment';
      stopTrackPayWaitTimers();
      renderTrackPayModal();
    } else {
      renderTrackPayModal();
    }
  } catch (e) {}
}

function showTrackPayTimeout() {
  stopTrackPayWaitTimers();
  trackPayState.phase = 'timeout';
  trackPayState.remaining = 0;
  if (!trackPayState.status) {
    trackPayState.status = 'PENDING';
  }
  trackPayState.error = '';
  trackPayState.retry = 'payment';
  renderTrackPayModal();
}

function startTrackPayWait() {
  stopTrackPayWaitTimers();
  const startedAt = Date.now();
  trackPayState.phase = 'waiting';
  trackPayState.remaining = TRACK_PAY_WAIT_SECONDS;
  renderTrackPayModal();

  trackPayWaitTimer = setInterval(() => {
    trackPayState.remaining = Math.max(0, TRACK_PAY_WAIT_SECONDS - Math.floor((Date.now() - startedAt) / 1000));
    if (trackPayState.remaining === 0) {
      showTrackPayTimeout();
      return;
    }
    renderTrackPayModal();
  }, 1000);

  trackPayPollTimer = setInterval(pollTrackPayStatus, 3000);
  pollTrackPayStatus();
}

async function runTrackPayment() {
  if (!trackPayModal.root || !trackPayState.orderRef || !trackPayState.phone) return;
  stopTrackPayWaitTimers();
  trackPayState.phase = 'starting';
  trackPayState.status = 'PROCESSING';
  trackPayState.error = '';
  trackPayState.retry = 'phone';
  openTrackPayModal();

  try {
    const payment = await initiatePayment(trackPayState.orderRef, trackPayState.phone);
    if (payment && payment.success === false) {
      throw new Error(payment.message || '{{ __('Failed to initiate payment.') }}');
    }
    trackPayState.retry = 'payment';
    startTrackPayWait();
  } catch (e) {
    trackPayState.phase = 'start-failed';
    trackPayState.status = '';
    trackPayState.error = (e && e.message) || '{{ __('Failed to initiate payment.') }}';
    renderTrackPayModal();
  }
}

document.getElementById('trackForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const orderNumber = document.getElementById('orderNumber').value.trim();
  if (orderNumber) {
    window.location.href = `{{ route('shop.tracking') }}?order=${encodeURIComponent(orderNumber)}`;
  }
});

@if($order)
function bindTrackPayModal() {
  cacheTrackPayModal();
  if (!trackPayModal.root) return;
  trackPayModal.root.querySelectorAll('[data-pay-modal-close]').forEach((el) => {
    el.addEventListener('click', handleTrackPayModalClose);
  });
  trackPayModal.confirm.addEventListener('click', handleTrackPayConfirm);
  trackPayModal.phone.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && trackPayState.phase === 'phone') {
      e.preventDefault();
      handleTrackPayConfirm();
    }
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !trackPayModal.root.hidden) handleTrackPayModalClose();
  });
}

bindTrackPayModal();

const payNowBtn = document.getElementById('payNowBtn');
if (payNowBtn) {
  payNowBtn.addEventListener('click', async () => {
    if (trackPayBusy || !trackPayModal.root) return;
    trackPayBusy = true;
    const baseUrl = window.location.origin;
    const trackingIdentifier = @json($order->tracking_token ?? $order->order_number);
    trackPayState.orderRef = trackingIdentifier;
    trackPayState.amount = payNowBtn.getAttribute('data-total') || '';
    trackPayState.trackingUrl = `${baseUrl}/shop/tracking/${encodeURIComponent(trackingIdentifier)}`;
    trackPayState.pdfUrl = `${baseUrl}/shop/tracking/${encodeURIComponent(trackingIdentifier)}/pdf`;

    const phoneNumber = await promptPaymentPhoneNumber(payNowBtn.getAttribute('data-phone') || '');
    if (!phoneNumber) {
      trackPayBusy = false;
      return;
    }
    trackPayState.phone = phoneNumber;
    await runTrackPayment();
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
