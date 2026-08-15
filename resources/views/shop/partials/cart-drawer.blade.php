<aside class="cart-drawer" id="cartDrawer" aria-label="{{ __('Your Cart') }}" aria-modal="true">
  <div class="drawer-head">
    <h3>
      <span class="dc-ic"><i class="fa-solid fa-cart-shopping"></i></span>
      {{ __('Your Cart') }}
    </h3>
    <button class="close-x" onclick="closeCart()" aria-label="{{ __('Close cart') }}">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>
  <div class="cart-list" id="cartList"></div>
  <div class="drawer-foot" id="cartFoot" style="display:none;">
    <div class="free-delivery-bar" id="freeDeliveryBar">
      <div class="fdb-track"><div class="fdb-fill" id="fdbFill"></div></div>
      <div class="fdb-text" id="fdbText"></div>
    </div>
    <div class="sum-row"><span>{{ __('Subtotal') }}</span><span id="cartSubtotal">TZS 0</span></div>
    <div class="sum-row"><span>{{ __('Delivery estimate') }}</span><span id="cartDeliveryEst">{{ __('Calculate at checkout') }}</span></div>
    <div class="sum-row total"><span>{{ __('Total') }}</span><span id="cartTotal">TZS 0</span></div>
    <a href="{{ route('shop.checkout') }}" class="btn btn-primary btn-block btn-lg" style="margin-top:14px;"><i class="fa-solid fa-arrow-right"></i> {{ __('Proceed to Checkout') }}</a>
    <button class="btn btn-ghost btn-block" style="margin-top:10px;" onclick="closeCart()">{{ __('Continue Shopping') }}</button>
  </div>
</aside>

@if($showBottomBar ?? true)
<div class="mobile-cart-bar" id="mobileCartBar" aria-hidden="true">
  <button class="mcb-left" onclick="openCart()" style="background:none;border:none;padding:0;text-align:left;min-height:0;">
    <span class="mcb-ic">
      <i class="fa-solid fa-cart-shopping"></i>
      <span class="badge" id="mcbBadge">0</span>
    </span>
    <span class="mcb-info">
      <span class="mcb-total" id="mcbTotal">TZS 0</span>
      <span class="mcb-sub" id="mcbSub">{{ __('Your cart is empty') }}</span>
    </span>
  </button>
  <button class="btn btn-primary btn-sm" style="flex-shrink:0;" onclick="openCart()">
    {{ __('View cart') }}
  </button>
</div>
@endif

@if($showBottomNav ?? false)
<nav class="bottom-nav" aria-label="Bottom navigation">
  <div class="bn-row">
    <a class="bn-item {{ ($activeNav ?? 'home') === 'home' ? 'active' : '' }}" href="{{ route('shop.index') }}">
      <i class="fa-solid fa-house"></i>
      <span>{{ __('Home') }}</span>
    </a>
    <a class="bn-item" href="{{ route('shop.index') }}#shop">
      <i class="fa-solid fa-store"></i>
      <span>{{ __('Buy All') }}</span>
    </a>
    <a class="bn-item" href="{{ route('shop.tracking') }}">
      <i class="fa-solid fa-location-dot"></i>
      <span>{{ __('Track') }}</span>
    </a>
    <button class="bn-item" onclick="toggleTheme()">
      <i class="fa-solid fa-circle-half-stroke"></i>
      <span>{{ __('Theme') }}</span>
    </button>
    <button class="bn-item" onclick="openCart()">
      <i class="fa-solid fa-cart-shopping"></i>
      <span>{{ __('Cart') }}</span>
      <span class="bn-badge" id="bnCartBadge" style="display:none;">0</span>
    </button>
  </div>
</nav>
@endif

<div id="scrim" class="scrim" onclick="closeAllOverlays()"></div>
<div id="toast" class="toast" role="status" aria-live="polite"></div>

<div id="pageLoader" class="page-loader" aria-live="polite" aria-label="{{ __('Loading...') }}">
  <div class="page-loader-card">
    <div class="page-loader-ring">
      <img src="{{ asset('logo-image-feedtan-store.png') }}" alt="Feedtan Store" class="page-loader-logo">
    </div>
    <div style="font-weight:700;color:var(--green-700);font-size:18px;">{{ __('Loading...') }}</div>
  </div>
</div>
