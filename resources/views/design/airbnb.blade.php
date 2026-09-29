<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#ffffff">
<title>airbnb.com — alpha preview</title>
<meta name="description" content="Alpha preview of the airbnb.com public homepage design system: white canvas, red accent, pill controls, image-led cards.">
<link rel="stylesheet" href="{{ asset('design-systems/airbnb-alpha.css') }}">
</head>
<body class="ab-page">

<header class="ab-header">
  <div class="ab-header-row">
    <a class="ab-logo" href="{{ route('design.airbnb') }}" aria-label="airbnb.com home">
      <span class="ab-logo-mark" aria-hidden="true">✦</span>
      <span>airbnb.com</span>
    </a>
    <nav class="ab-nav" aria-label="Primary">
      <a href="#stays" class="is-active">Stays</a>
      <a href="#experiences">Experiences</a>
      <a href="#online">Online</a>
    </nav>
    <div style="display:flex;gap:10px;align-items:center;">
      <a class="ab-btn ab-btn-link" href="#help">Help</a>
      <a class="ab-btn ab-btn-secondary" href="#cta">Sign up</a>
    </div>
  </div>
</header>

<main>
  <div class="ab-search-zone">
    <div class="ab-search" role="search" aria-label="Search stays">
      <button class="ab-search-field" type="button">
        <b>Where</b>
        <span>Search destinations</span>
      </button>
      <button class="ab-search-field" type="button">
        <b>When</b>
        <span>Add dates</span>
      </button>
      <button class="ab-search-field" type="button">
        <b>Who</b>
        <span>Add guests</span>
      </button>
      <button class="ab-search-go" type="button" aria-label="Search">⌕</button>
    </div>
  </div>

  <section class="ab-section" id="stays">
    <div class="ab-wrap">
      <div class="ab-section-head">
        <p class="ab-eyebrow">Alpha · Public homepage</p>
        <h1 class="ab-display">Find your next stay</h1>
        <p class="ab-body-md ab-quiet">Discovery-first browsing. Photography leads, metadata stays small and quiet.</p>
        <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap;">
          <a class="ab-btn ab-btn-primary" href="#popular">Search stays</a>
          <a class="ab-btn ab-btn-secondary" href="#near">Browse all</a>
          <a class="ab-btn ab-btn-link" href="#help">How it works</a>
        </div>
      </div>

      <div class="ab-chip-row" aria-label="Categories">
        <button class="ab-chip is-active" type="button">All</button>
        <button class="ab-chip" type="button">Cabins</button>
        <button class="ab-chip" type="button">Beachfront</button>
        <button class="ab-chip" type="button">City</button>
        <button class="ab-chip" type="button">Countryside</button>
      </div>

      <div class="ab-section-head" id="popular" style="margin-top:24px;">
        <h2 class="ab-hlg">Popular right now</h2>
        <p class="ab-body-md ab-quiet">Horizontally scrollable rail · image-led cards</p>
      </div>
      <div class="ab-rail">
        @php
          $cards = [
            ['img' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80', 'badge' => 'Guest favourite', 'title' => 'Cliffside villa, Malibu', 'meta' => '★ 4.97 · Ocean view', 'price' => '$312', 'old' => '$390'],
            ['img' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&q=80', 'badge' => null, 'title' => 'Loft in the arts district', 'meta' => '★ 4.82 · 2 beds', 'price' => '$148', 'old' => null],
            ['img' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=600&q=80', 'badge' => 'Guest favourite', 'title' => 'Garden flat, Lisbon', 'meta' => '★ 4.91 · Patio', 'price' => '$96', 'old' => null],
            ['img' => 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=600&q=80', 'badge' => null, 'title' => 'Sunny studio, Austin', 'meta' => '★ 4.75 · Pool', 'price' => '$84', 'old' => '$105'],
            ['img' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=600&q=80', 'badge' => null, 'title' => 'Desert house, Marrakech', 'meta' => '★ 4.88 · Courtyard', 'price' => '$122', 'old' => null],
          ];
        @endphp
        @foreach($cards as $c)
        <article class="ab-card">
          <div class="ab-card-media">
            <img src="{{ $c['img'] }}" alt="{{ $c['title'] }}" loading="lazy">
            @if($c['badge'])<span class="ab-card-badge">{{ $c['badge'] }}</span>@endif
            <button class="ab-card-fav" type="button" aria-label="Save" onclick="this.classList.toggle('is-active')">♡</button>
          </div>
          <div class="ab-card-body">
            <h3 class="ab-card-title">{{ $c['title'] }}</h3>
            <p class="ab-card-meta">{{ $c['meta'] }}</p>
            <div class="ab-card-foot">
              <span class="ab-card-price">{{ $c['price'] }}</span>
              <span class="ab-card-meta">night</span>
              @if($c['old'])<span class="ab-card-old">{{ $c['old'] }}</span>@endif
            </div>
          </div>
        </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="ab-section" id="near">
    <div class="ab-wrap">
      <div class="ab-section-head">
        <h2 class="ab-hlg">Near you this weekend</h2>
        <p class="ab-body-md ab-quiet">Compact titles · quiet prices and ratings</p>
      </div>
      <div class="ab-rail">
        @php
          $cards2 = [
            ['img' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=600&q=80', 'badge' => 'Guest favourite', 'title' => 'Cabin among pines', 'meta' => '★ 4.95 · Hot tub', 'price' => '$174'],
            ['img' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&q=80', 'badge' => null, 'title' => 'City apartment, 1 bed', 'meta' => '★ 4.71 · Central', 'price' => '$112'],
            ['img' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600&q=80', 'badge' => null, 'title' => 'Lake house retreat', 'meta' => '★ 4.89 · Dock', 'price' => '$205'],
            ['img' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80', 'badge' => null, 'title' => 'Poolside suite', 'meta' => '★ 4.80 · Breakfast', 'price' => '$139'],
          ];
        @endphp
        @foreach($cards2 as $c)
        <article class="ab-card">
          <div class="ab-card-media">
            <img src="{{ $c['img'] }}" alt="{{ $c['title'] }}" loading="lazy">
            @if($c['badge'])<span class="ab-card-badge">{{ $c['badge'] }}</span>@endif
            <button class="ab-card-fav" type="button" aria-label="Save" onclick="this.classList.toggle('is-active')">♡</button>
          </div>
          <div class="ab-card-body">
            <h3 class="ab-card-title">{{ $c['title'] }}</h3>
            <p class="ab-card-meta">{{ $c['meta'] }}</p>
            <div class="ab-card-foot">
              <span class="ab-card-price">{{ $c['price'] }}</span>
              <span class="ab-card-meta">night</span>
            </div>
          </div>
        </article>
        @endforeach
      </div>

      <div class="ab-section-head" id="cta" style="margin-top:40px;">
        <h2 class="ab-hmd">One primary action per region</h2>
        <p class="ab-body-md ab-quiet">Red is reserved for the main action. Everything else stays quiet.</p>
        <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap;align-items:center;">
          <button class="ab-btn ab-btn-primary" type="button">Continue</button>
          <button class="ab-btn ab-btn-secondary" type="button">Save draft</button>
          <button class="ab-btn ab-btn-link" type="button">Cancel</button>
          <span class="ab-status is-key"><span class="ab-status-dot"></span>Live availability</span>
        </div>
      </div>
    </div>
  </section>
</main>

<footer class="ab-footer">
  <div class="ab-wrap">
    <div class="ab-footer-grid">
      <div>
        <h4>Explore</h4>
        <ul><li><a href="#stays">Stays</a></li><li><a href="#experiences">Experiences</a></li><li><a href="#online">Online</a></li></ul>
      </div>
      <div>
        <h4>Hosting</h4>
        <ul><li><a href="#cta">List your home</a></li><li><a href="#help">Resources</a></li></ul>
      </div>
      <div>
        <h4>Support</h4>
        <ul><li><a href="#help">Help center</a></li><li><a href="#help">Cancellation options</a></li></ul>
      </div>
      <div id="experiences">
        <h4 id="help">About this preview</h4>
        <ul><li>Alpha tokens only</li><li>White canvas · 8px cards · pill controls</li></ul>
      </div>
    </div>
  </div>
</footer>

</body>
</html>
