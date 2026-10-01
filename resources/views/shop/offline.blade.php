<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<title>Store Closed - {{ $settings->store_name ?? 'Feedtan Store' }}</title>
<link rel="icon" type="image/png" href="{{ asset('logo-image-feedtan-store.png') }}">
@include('shop.partials.styles')
<style>
.offline-wrap{display:flex;align-items:center;justify-content:center;padding:64px 0;flex:1;}
.offline-card{
  background:var(--paper);border:1px solid var(--line);border-radius:var(--radius-xl);
  padding:40px 34px;text-align:center;max-width:520px;width:100%;box-shadow:var(--shadow-lift);
}
.offline-ic{
  width:82px;height:82px;border-radius:50%;background:var(--orange-100);color:var(--orange-600);
  display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;
}
.offline-card h1{font-size:clamp(21px,3vw,26px);margin-bottom:10px;}
.offline-card > p{color:var(--ink-soft);font-size:15px;}
.offline-contact{
  margin-top:24px;padding-top:22px;border-top:1px solid var(--line);
  display:flex;flex-direction:column;gap:11px;font-size:14px;color:var(--ink-soft);text-align:left;
}
.offline-contact div{display:flex;align-items:center;gap:10px;}
.offline-contact i{color:var(--orange-600);width:16px;text-align:center;}
.offline-actions{margin-top:26px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}
.offline-header{
  background:var(--paper);border-bottom:1px solid var(--line);
}
.offline-header .wrap{display:flex;align-items:center;justify-content:space-between;gap:14px;padding-top:16px;padding-bottom:16px;flex-wrap:wrap;}
.offline-footer{background:var(--paper);border-top:1px solid var(--line);padding:22px 0;}
.offline-footer .wrap{text-align:center;font-size:12.5px;color:var(--ink-faint);}
body{display:flex;flex-direction:column;min-height:100vh;}
@media (max-width:480px){
  .offline-card{padding:30px 20px;}
  .offline-actions .btn{width:100%;}
}
</style>
</head>
<body>

<header class="offline-header">
  <div class="wrap">
    <a href="{{ route('shop.index') }}" class="logo">
      <img class="logo-img" src="{{ asset('logo-image-feedtan-store.png') }}" alt="{{ $settings->store_name ?? 'Feedtan Store' }}">
      <span>{{ $settings->store_name ?? 'Feedtan Store' }}</span>
    </a>
    <div style="display:flex;align-items:center;gap:10px;">
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-dark btn-sm">
          <i class="fa-solid fa-gauge-high"></i> {{ __('Dashboard') }}
        </a>
      @else
        <a href="{{ route('login') }}" class="btn btn-dark btn-sm">
          <i class="fa-solid fa-right-to-bracket"></i> {{ __('Login') }}
        </a>
      @endauth
    </div>
  </div>
</header>

<main class="offline-wrap">
  <div class="wrap">
    <div class="offline-card">
      <div class="offline-ic"><i class="fa-solid fa-store"></i></div>
      <h1>{{ __('Our online store is currently closed') }}</h1>
      <p>{{ __('Online ordering is temporarily unavailable. Please check back soon or contact us below.') }}</p>

      @if(($settings->store_phone ?? null) || ($settings->store_email ?? null) || ($settings->store_address ?? null))
        <div class="offline-contact">
          @if($settings->store_phone)
            <div><i class="fa-solid fa-phone"></i> {{ $settings->store_phone }}</div>
          @endif
          @if($settings->store_email)
            <div><i class="fa-regular fa-envelope"></i> {{ $settings->store_email }}</div>
          @endif
          @if($settings->store_address)
            <div><i class="fa-solid fa-location-dot"></i> {{ $settings->store_address }}</div>
          @endif
        </div>
      @endif

      <div class="offline-actions">
        <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Login') }}</a>
      </div>
    </div>
  </div>
</main>

<footer class="offline-footer">
  <div class="wrap">
    &copy; {{ date('Y') }} {{ $settings->store_name ?? 'Feedtan Store' }}. {{ __('All rights reserved.') }}
  </div>
</footer>

</body>
</html>
