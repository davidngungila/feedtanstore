<div class="topbar">
  <div class="wrap">
    <div class="topbar-msg">
      <i class="fa-solid fa-truck-fast"></i>
      <span>{{ __('Free delivery for orders over TZS 50,000') }}</span>
    </div>
    <div class="topbar-msg" id="topbarPhone">
      <i class="fa-solid fa-phone"></i>
      <span>+255 717 358 865</span>
    </div>
  </div>
</div>

<header class="site-header" id="siteHeader">
  <div class="header-inner wrap">
    <button class="icon-btn hamburger" id="menuToggle" aria-label="{{ __('Menu') }}" aria-expanded="false" onclick="toggleMenu()">
      <i class="fa-solid fa-bars"></i>
    </button>
    <a href="{{ route('shop.index') }}" class="logo" aria-label="{{ __('Home') }}">
      <span class="logo-mark"><i class="fa-solid fa-leaf"></i></span>
      <span>Feedtan<span class="logo-sub">{{ __('Online Store') }}</span></span>
    </a>
    <form class="search-bar" id="searchForm" role="search" action="{{ route('shop.index') }}">
      <label for="searchInput" class="visually-hidden">{{ __('Search products') }}</label>
      <input type="search" id="searchInput" name="search" placeholder="{{ __('Search products placeholder') }}" autocomplete="off" value="{{ request('search', '') }}">
      <button type="submit" aria-label="{{ __('Search') }}">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
    </form>
    <div class="header-actions">
      <button class="icon-btn hide-on-desktop" id="mobileSearchToggle" aria-label="{{ __('Search') }}" onclick="toggleMobileSearch()">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
      <button class="icon-btn hide-on-mobile theme-toggle" id="themeToggle" aria-label="{{ __('Toggle theme') }}" onclick="toggleTheme()">
        <i class="fa-solid fa-moon" id="themeIcon"></i>
      </button>
      <button class="icon-btn hide-on-mobile" aria-label="{{ __('Saved items live in your wishlist') }}" onclick="showToast('{{ __('Saved items live in your wishlist') }}','heart')">
        <i class="fa-regular fa-heart"></i>
      </button>
      <a href="{{ route('shop.tracking') }}" class="icon-btn hide-on-mobile" aria-label="{{ __('Track my order') }}" title="{{ __('Track my order') }}">
        <i class="fa-solid fa-truck-fast"></i>
      </a>
      <button class="icon-btn" aria-label="{{ __('Open cart') }}" onclick="openCart()">
        <i class="fa-solid fa-cart-shopping"></i>
        <span class="badge" id="cartBadge" style="display:none;">0</span>
      </button>
      <div class="lang-switch">
        <a href="{{ route('lang.switch', 'en') }}" class="{{ App::getLocale() === 'en' ? 'active' : '' }}" aria-label="English">EN</a>
        <a href="{{ route('lang.switch', 'sw') }}" class="{{ App::getLocale() === 'sw' ? 'active' : '' }}" aria-label="Kiswahili">SW</a>
      </div>
    </div>
  </div>
  <div class="mobile-search" id="mobileSearchBox" style="display:none;">
    <form class="search-bar" action="{{ route('shop.index') }}">
      <input type="search" id="searchInputMobile" name="search" placeholder="{{ __('Search products placeholder') }}" autocomplete="off" value="{{ request('search', '') }}">
      <button type="submit" aria-label="{{ __('Search') }}"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
  </div>
  <nav class="nav-strip" aria-label="Primary">
    <div class="wrap">
      <a href="{{ route('shop.index') }}" class="{{ ($activeNav ?? 'home') === 'home' ? 'active' : '' }}"><i class="fa-solid fa-house"></i> {{ __('Home') }}</a>
      <a href="{{ route('shop.index') }}#shop" class="{{ ($activeNav ?? 'home') === 'shop' ? 'active' : '' }}"><i class="fa-solid fa-store"></i> {{ __('Buy All') }}</a>
      @foreach($categories as $cat)
        <a href="{{ route('shop.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
      @endforeach
      <a href="{{ route('shop.tracking') }}" class="{{ ($activeNav ?? 'home') === 'track' ? 'active' : '' }}"><i class="fa-solid fa-location-dot"></i> {{ __('Track my order') }}</a>
    </div>
  </nav>
</header>

<aside class="mobile-menu" id="mobileMenu" aria-label="{{ __('Menu') }}">
  <div class="mm-head">
    <a href="{{ route('shop.index') }}" class="logo" style="font-size:19px;">
      <span class="logo-mark"><i class="fa-solid fa-leaf"></i></span>
      <span>Feedtan<span class="logo-sub">{{ __('Online Store') }}</span></span>
    </a>
    <button class="close-x" onclick="closeMenu()" aria-label="{{ __('Close') }}">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>
  <nav class="mm-nav">
    <a href="{{ route('shop.index') }}" class="{{ ($activeNav ?? 'home') === 'home' ? 'active' : '' }}">
      <span class="mm-ic"><i class="fa-solid fa-house"></i></span>
      {{ __('Home') }}
    </a>
    <a href="{{ route('shop.index') }}#shop" class="{{ ($activeNav ?? 'home') === 'shop' ? 'active' : '' }}">
      <span class="mm-ic"><i class="fa-solid fa-store"></i></span>
      {{ __('Buy All') }}
    </a>
    @foreach($categories as $cat)
      <a href="{{ route('shop.index', ['category' => $cat->slug]) }}">
        <span class="mm-ic"><i class="fa-solid fa-tags"></i></span>
        {{ $cat->name }}
      </a>
    @endforeach
    <a href="{{ route('shop.tracking') }}" class="{{ ($activeNav ?? 'home') === 'track' ? 'active' : '' }}">
      <span class="mm-ic"><i class="fa-solid fa-location-dot"></i></span>
      {{ __('Track my order') }}
    </a>
  </nav>
  <div class="mm-footer">
    <div class="lang-switch">
      <a href="{{ route('lang.switch', 'en') }}" class="{{ App::getLocale() === 'en' ? 'active' : '' }}">EN</a>
      <a href="{{ route('lang.switch', 'sw') }}" class="{{ App::getLocale() === 'sw' ? 'active' : '' }}">SW</a>
    </div>
    <div class="mm-contact">
      <b style="color:var(--ink);">Feedtan Store</b><br>
      {{ __('Location') }}<br>
      {{ __('Opening hours') }}<br>
      +255 717 358 865
    </div>
  </div>
</aside>
