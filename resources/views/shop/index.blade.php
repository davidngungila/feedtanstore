<!DOCTYPE html>
<html lang="en">
<head>
@php
  $logoUrl = asset('logo-image-feedtan-store.png');
  $selectedCategory = request('category')
      ? $categories->firstWhere('id', (int) request('category'))
      : null;
  $searchTerm = trim((string) request('search', ''));
  $seoTitle = 'Moshi Online Supermarket | Grocery Delivery in Kilimanjaro Tanzania | Feedtan Store';
  $seoDescription = 'Shop groceries online in Moshi, Kilimanjaro. Order fresh food, household items, and get fast home delivery across Moshi town with Feedtan Store.';

  if ($selectedCategory) {
    $seoTitle = $selectedCategory->name . ' - Feedtan Store Online Shop Moshi Kilimanjaro';
    $seoDescription = 'Browse ' . $selectedCategory->name . ' at Feedtan Store with trusted prices, secure checkout, and delivery options across Moshi, Kilimanjaro, Tanzania.';
  }

  if ($searchTerm !== '') {
    $seoTitle = 'Search results for "' . $searchTerm . '" - Feedtan Store Moshi';
    $seoDescription = 'Find products matching "' . $searchTerm . '" at Feedtan Store and order online with quick checkout and delivery tracking in Moshi, Kilimanjaro.';
  }

  $canonicalUrl = request()->fullUrl();
  $pageType = $selectedCategory || $searchTerm !== '' ? 'website' : 'store';
  $seoKeywords = 'online supermarket Moshi, Moshi online grocery store, supermarket delivery Moshi Kilimanjaro, buy groceries online Moshi, online shopping Moshi Tanzania, grocery delivery Moshi, food delivery supermarket Moshi, Kilimanjaro online supermarket, Moshi grocery delivery service, online supermarket in Moshi, grocery store near me Moshi, food delivery near Moshi town, supermarket in Kilimanjaro Tanzania, Moshi town online shopping, delivery supermarket Kilimanjaro region, Moshi fresh food delivery, Moshi household shopping online, buy rice online Moshi, Moshi maize flour delivery, online vegetables supermarket Moshi, fresh fruits delivery Moshi Tanzania, cooking oil delivery Moshi, dairy products online Moshi, beverages delivery Moshi supermarket, same day delivery Moshi supermarket, home delivery grocery Moshi, fast delivery Kilimanjaro supermarket, affordable grocery delivery Moshi, online order and home delivery Moshi, Feedtan supermarket Moshi, Feedtan grocery delivery Tanzania, Feedtan online shopping platform, Feedtan Kilimanjaro delivery service, best online supermarket for home delivery in Moshi Tanzania, how to buy groceries online in Moshi Kilimanjaro, cheap grocery delivery service in Moshi town, trusted online supermarket in Kilimanjaro region Tanzania';
  $structuredData = [
      '@context' => 'https://schema.org',
      '@graph' => [
          [
              '@type' => 'Organization',
              '@id' => url('/#organization'),
              'name' => 'Feedtan Store',
              'url' => url('/'),
              'logo' => [
                  '@type' => 'ImageObject',
                  'url' => $logoUrl,
              ],
              'image' => [$logoUrl],
              'telephone' => '+255717358865',
              'email' => 'info@feedtanstore.com',
              'address' => [
                  '@type' => 'PostalAddress',
                  'streetAddress' => 'Kiboriloni',
                  'addressLocality' => 'Moshi',
                  'addressRegion' => 'Kilimanjaro',
                  'addressCountry' => 'TZ',
              ],
          ],
          [
              '@type' => 'WebSite',
              '@id' => url('/#website'),
              'url' => url('/'),
              'name' => 'Feedtan Store',
              'publisher' => [
                  '@id' => url('/#organization'),
              ],
              'potentialAction' => [
                  '@type' => 'SearchAction',
                  'target' => route('shop.index') . '?search={search_term_string}',
                  'query-input' => 'required name=search_term_string',
              ],
          ],
          [
              '@type' => 'CollectionPage',
              '@id' => $canonicalUrl . '#webpage',
              'url' => $canonicalUrl,
              'name' => $seoTitle,
              'description' => $seoDescription,
              'isPartOf' => [
                  '@id' => url('/#website'),
              ],
              'about' => [
                  '@id' => url('/#organization'),
              ],
              'primaryImageOfPage' => [
                  '@type' => 'ImageObject',
                  'url' => $logoUrl,
              ],
          ],
      ],
  ];
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta name="author" content="Feedtan Store">
<meta name="theme-color" content="#1B4332">
<link rel="canonical" href="{{ $canonicalUrl }}">
<link rel="icon" type="image/png" href="{{ $logoUrl }}">
<link rel="apple-touch-icon" href="{{ $logoUrl }}">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="Feedtan Store">
<meta property="og:type" content="{{ $pageType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $logoUrl }}">
<meta property="og:image:secure_url" content="{{ $logoUrl }}">
<meta property="og:image:type" content="image/png">
<meta property="og:image:alt" content="Feedtan Store logo">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $logoUrl }}">
<meta name="twitter:image:alt" content="Feedtan Store logo">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;0,9..144,700;0,9..144,900;1,9..144,500&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
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
.topbar .wrap{display:flex;align-items:center;justify-content:space-between;padding:7px 24px;gap:12px;flex-wrap:wrap;}
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

.hero{position:relative;overflow:hidden;background:var(--green-700);color:#fff;}
.hero::before{
  content:'';position:absolute;inset:0;
  background:
    radial-gradient(circle at 14% 22%, rgba(232,137,58,0.35), transparent 38%),
    radial-gradient(circle at 88% 78%, rgba(255,255,255,0.10), transparent 45%);
}
.hero-grain{position:absolute;inset:0;opacity:.05;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");}
.hero-inner{position:relative;z-index:2;display:grid;grid-template-columns:1.1fr 0.9fr;gap:40px;align-items:center;padding:64px 24px 56px;}
.hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.25);padding:6px 14px;border-radius:999px;font-size:12.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#FFE3C2;margin-bottom:18px;}
.hero h1{font-size:clamp(34px,5vw,58px);line-height:1.04;font-weight:700;}
.hero h1 em{font-style:italic;color:#FFCB94;font-weight:500;}
.hero p.lead{font-size:17px;color:#DCEAE1;max-width:480px;margin:18px 0 28px;}
.hero-cta{display:flex;gap:12px;flex-wrap:wrap;}
.hero-stats{display:flex;gap:28px;margin-top:36px;flex-wrap:wrap;}
.hero-stats div b{display:block;font-family:var(--font-display);font-size:26px;font-weight:700;}
.hero-stats div span{font-size:12.5px;color:#BFD6C8;}

.hero-visual{position:relative;display:flex;justify-content:center;align-items:center;}
.produce-card{
  position:relative;background:var(--parchment);border-radius:var(--radius-l);padding:22px;width:100%;max-width:380px;
  box-shadow:var(--shadow-pop);transform:rotate(-2deg);
}
.produce-card img{border-radius:var(--radius-m);aspect-ratio:4/3;object-fit:cover;width:100%;}
.produce-card .tag{
  position:absolute;top:-14px;right:18px;background:var(--orange);color:#fff;font-weight:800;font-size:13px;
  padding:8px 16px;border-radius:999px;box-shadow:0 8px 18px rgba(0,0,0,0.2);transform:rotate(4deg);
}
.produce-card .info{display:flex;justify-content:space-between;align-items:center;margin-top:14px;}
.produce-card .info b{font-family:var(--font-display);font-size:18px;color:var(--ink);}
.produce-card .info span{font-size:12.5px;color:var(--ink-soft);}
.float-chip{
  position:absolute;background:#fff;border-radius:var(--radius-m);padding:12px 16px;box-shadow:var(--shadow-card);
  display:flex;align-items:center;gap:10px;font-size:13px;font-weight:700;color:var(--green-900);
}
.chip-1{top:6%;left:-6%;animation:float1 6s ease-in-out infinite;}
.chip-2{bottom:4%;right:-8%;animation:float2 7s ease-in-out infinite;}
@keyframes float1{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
@keyframes float2{0%,100%{transform:translateY(0)}50%{transform:translateY(10px)}}

@media(max-width:900px){
  .hero-inner{grid-template-columns:1fr;padding:44px 20px 40px;}
  .hero-visual{order:-1;margin-bottom:8px;}
  .produce-card{max-width:320px;}
}

.section{padding:54px 0;}
.section-head{display:flex;align-items:baseline;justify-content:space-between;gap:16px;margin-bottom:28px;flex-wrap:wrap;}
.section-head h2{font-size:clamp(24px,3vw,32px);font-weight:700;}
.section-head .eyebrow{display:block;font-family:var(--font-body);font-size:12.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--orange-dark);margin-bottom:6px;}
.section-head p{color:var(--ink-soft);font-size:14.5px;margin:6px 0 0;}
.see-all{font-size:13.5px;font-weight:700;color:var(--green-700);display:flex;align-items:center;gap:5px;flex-shrink:0;}
.see-all:hover{color:var(--orange-dark);}
.section-head-centered{justify-content:center;text-align:center;margin-bottom:20px;}
.section-head-centered > div{max-width:640px;}
.section-head-catalog{justify-content:center;text-align:center;flex-direction:column;align-items:center;margin-bottom:30px;}
.section-head-catalog > div{max-width:640px;}
.section-head-catalog .see-all{margin-top:2px;}

.cat-row{display:flex;gap:12px;overflow-x:auto;padding-bottom:6px;margin-bottom:36px;scrollbar-width:none;justify-content:center;flex-wrap:wrap;}
.cat-row::-webkit-scrollbar{display:none;}
.cat-chip{
  flex-shrink:0;display:flex;align-items:center;gap:9px;background:#fff;border:1.5px solid var(--line);
  border-radius:999px;padding:9px 18px 9px 10px;font-size:13.5px;font-weight:700;color:var(--ink);
  transition:all .15s;
}
.cat-chip .ic{width:30px;height:30px;border-radius:50%;background:var(--green-100);display:flex;align-items:center;justify-content:center;font-size:15px;}
.cat-chip:hover, .cat-chip.active{background:var(--green-700);color:#fff;border-color:var(--green-700);}
.cat-chip.active .ic, .cat-chip:hover .ic{background:rgba(255,255,255,0.2);}

.product-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px;}
@media(max-width:1080px){.product-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:760px){.product-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}}
@media(max-width:420px){.product-grid{grid-template-columns:1fr 1fr;gap:10px;}}
@media(max-width:760px){.cat-row{justify-content:flex-start;flex-wrap:nowrap;}}

.p-card{
  background:#fff;border-radius:var(--radius-m);overflow:hidden;box-shadow:var(--shadow-card);
  display:flex;flex-direction:column;transition:transform .18s ease, box-shadow .18s ease;border:1px solid transparent;
}
.p-card:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(15,42,31,0.15);}
.p-media{position:relative;aspect-ratio:1/0.92;overflow:hidden;background:var(--parchment-dim);cursor:pointer;}
.p-media img{width:100%;height:100%;object-fit:cover;transition:transform .35s ease;}
.p-card:hover .p-media img{transform:scale(1.06);}
.p-badge{position:absolute;top:10px;left:10px;}
.p-fav{
  position:absolute;top:8px;right:8px;width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,0.92);
  display:flex;align-items:center;justify-content:center;border:none;color:var(--ink-soft);transition:color .15s, transform .15s;
}
.p-fav:hover{color:var(--red);transform:scale(1.08);}
.p-fav.active{color:var(--red);}
.p-body{padding:14px 14px 16px;display:flex;flex-direction:column;gap:6px;flex:1;}
.p-cat{font-size:11px;font-weight:700;color:var(--green-700);text-transform:uppercase;letter-spacing:.04em;}
.p-name{font-size:14.5px;font-weight:700;color:var(--ink);cursor:pointer;line-height:1.3;}
.p-name:hover{color:var(--green-700);}
.p-rating{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--ink-soft);}
.p-rating svg{color:var(--orange);}
.p-price-row{display:flex;align-items:baseline;gap:7px;margin-top:2px;}
.p-price{font-family:var(--font-display);font-weight:700;font-size:19px;color:var(--ink);}
.p-price-old{font-size:13px;color:#A39E8C;text-decoration:line-through;}
.p-unit{font-size:11.5px;color:var(--ink-soft);}
.p-actions{display:flex;gap:8px;margin-top:10px;}
.p-actions .btn{flex:1;padding:10px 12px;font-size:13px;}
.stock-low{font-size:11.5px;color:var(--red);font-weight:700;margin-top:-2px;}

.qty-stepper{display:flex;align-items:center;border:1.5px solid var(--line);border-radius:999px;overflow:hidden;flex:1;background:#fff;}
.qty-stepper button{width:32px;height:38px;background:transparent;border:none;font-size:16px;font-weight:700;color:var(--green-700);}
.qty-stepper button:hover{background:var(--green-100);}
.qty-stepper span{flex:1;text-align:center;font-weight:700;font-size:14px;}

.promo-strip{background:var(--orange);border-radius:var(--radius-l);padding:30px 36px;display:flex;align-items:center;justify-content:space-between;gap:24px;color:#fff;flex-wrap:wrap;margin:8px 0 48px;}
.promo-strip h3{font-size:24px;font-weight:700;}
.promo-strip p{margin:6px 0 0;font-size:14px;color:#FFE6CC;}

.trust-strip{background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line);}
.trust-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;padding:30px 24px;}
.trust-item{display:flex;gap:12px;align-items:flex-start;}
.trust-item .ic{width:42px;height:42px;border-radius:12px;background:var(--green-100);color:var(--green-700);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.trust-item b{display:block;font-size:14px;}
.trust-item span{font-size:12.5px;color:var(--ink-soft);}
@media(max-width:760px){.trust-grid{grid-template-columns:repeat(2,1fr);}}

footer{background:var(--green-900);color:#BFD6C8;padding:54px 0 0;margin-top:30px;}
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

.modal{
  position:fixed;top:50%;left:50%;transform:translate(-50%,-46%);width:92vw;max-width:760px;max-height:88vh;
  background:#fff;border-radius:var(--radius-l);z-index:220;box-shadow:var(--shadow-pop);
  display:flex;flex-direction:column;opacity:0;visibility:hidden;transition:opacity .22s ease, transform .22s ease;
}
.modal.open{opacity:1;visibility:visible;transform:translate(-50%,-50%);}
.modal.modal-wide{max-width:980px;}
.modal-head{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--line);flex-shrink:0;}
.modal-head h3{font-size:19px;}
.modal-body{overflow-y:auto;padding:22px;flex:1;}
.modal-foot{padding:16px 22px;border-top:1px solid var(--line);display:flex;gap:10px;justify-content:flex-end;flex-shrink:0;background:#fff;}
@media(max-width:600px){
  .modal{width:100%;max-width:100%;height:100%;max-height:100%;border-radius:0;top:0;left:0;transform:translateY(100%);}
  .modal.open{transform:translateY(0);}
}

.pd-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;}
@media(max-width:600px){.pd-grid{grid-template-columns:1fr;}}
.pd-img{border-radius:var(--radius-m);aspect-ratio:1/1;object-fit:cover;width:100%;background:var(--parchment-dim);}
.pd-cat{font-size:12px;font-weight:700;color:var(--green-700);text-transform:uppercase;letter-spacing:.04em;}
.pd-title{font-size:24px;margin:6px 0 8px;}
.pd-price{font-family:var(--font-display);font-size:28px;font-weight:700;margin:10px 0;}
.pd-desc{font-size:14px;color:var(--ink-soft);line-height:1.7;margin-bottom:16px;}
.pd-meta-list{display:flex;flex-direction:column;gap:8px;font-size:13.5px;margin-bottom:18px;}
.pd-meta-list li{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--parchment-dim);list-style:none;}
.pd-meta-list li span:first-child{color:var(--ink-soft);}
.pd-actions{display:flex;gap:10px;align-items:stretch;}
.pd-actions .qty-stepper{flex:0 0 110px;}

.toast{
  position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--green-900);color:#fff;
  padding:13px 22px;border-radius:999px;font-size:13.5px;font-weight:600;z-index:400;box-shadow:var(--shadow-pop);
  display:flex;align-items:center;gap:10px;opacity:0;visibility:hidden;transition:all .25s ease;
}
.toast.show{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0);}
.toast svg{color:var(--orange);flex-shrink:0;}

.page-loader{
  position:fixed;inset:0;z-index:9999;background:rgba(247,244,237,0.94);backdrop-filter:blur(8px);
  display:flex;align-items:center;justify-content:center;transition:opacity .3s ease, visibility .3s ease;
}
.page-loader.hidden{opacity:0;visibility:hidden;pointer-events:none;}
.page-loader-card{text-align:center;padding:24px;}
.page-loader-ring{
  position:relative;width:110px;height:110px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;
}
.page-loader-ring::before{
  content:'';position:absolute;inset:0;border-radius:50%;border:4px solid rgba(35,90,65,0.12);border-top-color:var(--green-700);
  animation:spinLoader 1s linear infinite;
}
.page-loader-logo{
  width:74px;height:74px;border-radius:50%;object-fit:cover;background:#fff;box-shadow:var(--shadow-card);padding:4px;
}
@keyframes spinLoader{to{transform:rotate(360deg);}}

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
@media(max-width:640px){
  .topbar .wrap{justify-content:center;padding:8px 16px;text-align:center;}
  .topbar-msg{justify-content:center;width:100%;}
  #topbarPhone{display:none;}
  .header-inner{padding:12px 16px;gap:12px;}
  .header-actions{gap:6px;}
  .icon-btn{width:38px;height:38px;}
  .mobile-search{padding:0 16px 12px;}
  .nav-strip .wrap{padding:10px 16px;gap:18px;}
  .section-head{margin-bottom:20px;}
  .section-head h2{font-size:26px;}
  .cat-row{margin-bottom:24px;}
  .cat-chip{padding:8px 14px 8px 10px;font-size:13px;}
  .cat-chip .ic{width:28px;height:28px;font-size:14px;}
  .p-body{padding:12px;}
  .p-name{font-size:14px;}
  .p-price{font-size:18px;}
  .p-actions{flex-direction:column;}
  .p-actions .btn{width:100%;}
  .promo-strip{padding:22px 18px;margin:0 0 36px;border-radius:18px;}
  .promo-strip h3{font-size:20px;}
  .promo-strip .btn{width:100%;}
  .trust-grid{grid-template-columns:1fr;padding:22px 16px;}
  .footer-bottom{padding:18px 16px;}
  .drawer-head,.cart-list,.drawer-foot{padding-left:16px;padding-right:16px;}
  .cart-row{align-items:flex-start;}
  .cart-row-bottom{flex-direction:column;align-items:flex-start;gap:10px;}
  .sum-row.total{font-size:16px;}
  .toast{left:16px;right:16px;bottom:18px;transform:translateY(20px);width:auto;border-radius:16px;padding:12px 16px;}
  .toast.show{transform:translateY(0);}
}
@media(max-width:360px){
  .header-inner{align-items:flex-start;}
  .header-actions{width:100%;justify-content:flex-end;}
  .product-grid{grid-template-columns:1fr;}
  .p-media{aspect-ratio:1/0.85;}
}
@media (prefers-reduced-motion: reduce){
  .page-loader-ring::before{animation:none;}
}
</style>
</head>
<body>

<div id="pageLoader" class="page-loader" aria-live="polite" aria-label="Page loading">
  <div class="page-loader-card">
    <div class="page-loader-ring">
      <img src="{{ asset('logo-image-feedtan-store.png') }}" alt="Feedtan Store" class="page-loader-logo">
    </div>
    <div style="font-weight:700;color:var(--green-700);font-size:18px;">Loading...</div>
  </div>
</div>

<div class="topbar">
  <div class="wrap">
    <div class="topbar-msg">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
      <span>Free delivery on orders over TZS 50,000</span>
    </div>
    <div class="topbar-msg" id="topbarPhone">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
      <span>+255 717 358 865</span>
    </div>
  </div>
</div>

<header class="site-header">
  <div class="header-inner wrap">
    <a href="{{ route('shop.index') }}" class="logo">
      <span class="logo-mark">F</span>
      <span>Feedtan<span class="logo-sub">Online Store</span></span>
    </a>
    <form class="search-bar" id="searchForm" role="search">
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
    <form class="search-bar">
      <input type="search" id="searchInputMobile" name="search" placeholder="Search products…" autocomplete="off" value="{{ request('search', '') }}">
      <button type="submit" aria-label="Search"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg></button>
    </form>
  </div>
  <nav class="nav-strip" aria-label="Primary">
    <div class="wrap">
      <a href="{{ route('shop.index') }}" class="active">Home</a>
      <a href="#shop">Shop All</a>
      @foreach($categories as $cat)
        <a href="{{ route('shop.index', ['category' => $cat->id]) }}">{{ $cat->name }}</a>
      @endforeach
      <a href="{{ route('shop.tracking') }}">Track Order</a>
    </div>
  </nav>
</header>





<main id="mainContent">

  <section class="section" id="shop">
    <div class="wrap">
      <div class="section-head section-head-centered">
        <div>
          
          <h2>Shop by category</h2>
        </div>
      </div>
      <div class="cat-row" id="catRow">
        <a href="{{ route('shop.index') }}" class="cat-chip {{ !request('category') ? 'active' : '' }}">
          <span class="ic">🛒</span> All
        </a>
        @foreach($categories as $cat)
          <a href="{{ route('shop.index', ['category' => $cat->id]) }}" class="cat-chip {{ request('category') == $cat->id ? 'active' : '' }}">
            <span class="ic">📦</span> {{ $cat->name }}
          </a>
        @endforeach
      </div>








      <div class="product-grid" id="productGrid">
        @foreach($products as $product)
          @php
            $primaryImage = $product->images->firstWhere('is_primary', true);
            $baseUrl = $settings->store_url ?? config('app.url');
            $resolveImageUrl = function ($path) use ($baseUrl) {
              if (!$path) {
                return null;
              }

              // If it's already a full URL, clean it to extract the path
              if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                // Parse URL to get path
                $parsed = parse_url($path);
                if (isset($parsed['path'])) {
                  $path = ltrim($parsed['path'], '/');
                  // If path starts with storage/, use it directly
                  if (str_starts_with($path, 'storage/')) {
                    return rtrim($baseUrl, '/') . '/' . $path;
                  }
                  return rtrim($baseUrl, '/') . '/storage/' . $path;
                }
              }

              $cleanPath = ltrim($path, '/');
              if (str_starts_with($cleanPath, 'storage/')) {
                return rtrim($baseUrl, '/') . '/' . $cleanPath;
              }

              return rtrim($baseUrl, '/') . '/storage/' . $cleanPath;
            };

            $imageToShow = $resolveImageUrl($primaryImage?->image_path) ?? $resolveImageUrl($product->image) ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=500&q=80';
            $oldPrice = $product->old_price ?? null;
            $badge = $oldPrice ? '-'.round((($oldPrice - $product->selling_price)/$oldPrice)*100).'%' : null;
          @endphp
          <div class="p-card" data-id="{{ $product->id }}">
            <div class="p-media" onclick="window.location.href='{{ route('shop.product', $product) }}'">
              <img src="{{ $imageToShow }}" alt="{{ $product->name }}" loading="lazy">
              @if($badge)
                <span class="p-badge pill pill-orange">{{ $badge }}</span>
              @endif
              <button class="p-fav" aria-label="Save to wishlist" onclick="event.stopPropagation(); toggleFav(this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
              </button>
            </div>
            <div class="p-body">
              <span class="p-cat">{{ $product->category->name ?? 'Uncategorized' }}</span>
              <span class="p-name" onclick="window.location.href='{{ route('shop.product', $product) }}'">{{ $product->name }}</span>
              <div class="p-rating">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8l-6.2 3.2 1.2-6.8-5-4.9 6.9-1z"/></svg>
                4.5 ({{ rand(10, 500) }})
              </div>
              <div class="p-price-row">
                <span class="p-price">TZS {{ number_format($product->selling_price, 0) }}</span>
                @if($oldPrice)
                  <span class="p-price-old">TZS {{ number_format($oldPrice, 0) }}</span>
                @endif
              </div>
              <span class="p-unit">per item</span>
              @if($product->quantity <= 5)
                <span class="stock-low">Only {{ $product->quantity }} left in stock</span>
              @endif
              <div class="p-actions" id="actions-{{ $product->id }}">
                <button class="btn btn-dark btn-sm" style="flex:1;" onclick="addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }})">Add to cart</button>
                <a href="{{ route('shop.product', $product) }}" class="btn btn-outline btn-sm">Details</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="promo-strip" style="margin-top:40px;">
        <div>
          <h3>Get TZS 5,000 off your first order</h3>
          <p>Use code <strong>FEEDTAN5K</strong> at checkout — valid on orders above TZS 30,000.</p>
        </div>
        <button class="btn btn-dark" onclick="openCart()">Start shopping</button>
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
          @foreach($categories as $cat)
            <li><a href="{{ route('shop.index', ['category' => $cat->id]) }}">{{ $cat->name }}</a></li>
          @endforeach
        </ul>
      </div>
      <div>
        <h4>Support</h4>
        <ul>
          <li><a href="{{ route('shop.tracking') }}">Track my order</a></li>
          <li><a href="#" onclick="showToast('Reach us on +255 717 358 865','phone')">Contact us</a></li>
          <li><a href="#" onclick="showToast('Returns accepted within 48 hours of delivery','info')">Returns policy</a></li>
          <li><a href="#" onclick="showToast('Delivery available across Dar es Salaam and nearby regions','info')">Delivery info</a></li>
        </ul>
      </div>
      <div>
        <h4>Visit our store</h4>
        <ul>
          <li>Kiboriloni, Moshi, Kilimanjaro, Tanzania</li>
          <li>Open daily · 8:00 AM – 9:00 PM</li>
          <li>+255 717 358 865</li>
          <li>info@feedtanstore.com</li>
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
let cart = [];
const DELIVERY_FEE = 3000;
const FREE_DELIVERY_THRESHOLD = 50000;

function normalizeCart(raw) {
  if (Array.isArray(raw)) return raw;
  if (raw && typeof raw === 'object') {
    return Object.entries(raw).map(([id, quantity]) => {
      const product = document.querySelector('.p-card[data-id="'+id+'"]');
      const name = product ? (product.querySelector('.p-name')?.textContent || 'Item') : 'Item';
      let price = 0;
      if (product) {
        price = parseFloat(product.querySelector('.p-price')?.textContent.replace(/[^0-9]/g, '') || '0');
      }
      return { id: String(id), name, price, quantity: Number(quantity) || 0 };
    }).filter(i => i.quantity > 0);
  }
  return [];
}

function initCart() {
  const saved = localStorage.getItem('shopCart');
  if (saved) {
    try {
      cart = normalizeCart(JSON.parse(saved));
    } catch(e) {
      cart = [];
    }
  }
  updateCartUI();
}

function addToCart(id, name, price) {
  const existing = cart.find(i => String(i.id) === String(id));
  if (existing) {
    existing.quantity += 1;
    existing.name = name;
    existing.price = Number(price) || 0;
  } else {
    cart.push({ id: String(id), name, price: Number(price) || 0, quantity: 1 });
  }
  saveCart();
  updateCartUI();
  showToast(name + ' added to cart', 'cart');
}

function changeQty(id, delta, name = null, price = null) {
  const idx = cart.findIndex(i => String(i.id) === String(id));
  if (idx === -1) return;
  cart[idx].quantity += delta;
  if (name !== null) cart[idx].name = name;
  if (price !== null) cart[idx].price = Number(price) || 0;
  if (cart[idx].quantity <= 0) cart.splice(idx, 1);
  saveCart();
  updateCartUI();
}

function removeFromCart(id) {
  cart = cart.filter(i => String(i.id) !== String(id));
  saveCart();
  updateCartUI();
}

function saveCart() {
  localStorage.setItem('shopCart', JSON.stringify(cart));
}

function cartCount() { return cart.reduce((sum, item) => sum + (Number(item.quantity) || 0), 0); }
function cartSubtotal() {
  return cart.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 0), 0);
}

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
  if (cart.length === 0) {
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
  list.innerHTML = cart.map(item => {
    const product = document.querySelector('.p-card[data-id="'+item.id+'"]');
    let img = 'https://images.unsplash.com/photo-1542831329-92c53300491e?w=500&q=80';
    if (product) {
      img = product.querySelector('.p-media img').src;
    }
    return '<div class="cart-row">' +
      '<img src="'+img+'" alt="'+item.name+'">' +
      '<div class="cart-row-info">' +
        '<b>'+item.name+'</b>' +
        '<span class="cr-meta">per item · TZS '+item.price.toLocaleString()+' each</span>' +
        '<div class="cart-row-bottom">' +
          '<div class="qty-stepper">' +
            '<button onclick="changeQty(\''+item.id+'\', -1, \''+item.name+'\', '+item.price+')" aria-label="Decrease quantity">−</button>' +
            '<span>'+item.quantity+'</span>' +
            '<button onclick="changeQty(\''+item.id+'\', 1, \''+item.name+'\', '+item.price+')" aria-label="Increase quantity">+</button>' +
          '</div>' +
          '<span class="cr-price">TZS '+(item.price*item.quantity).toLocaleString()+'</span>' +
        '</div>' +
        '<button class="cr-remove" onclick="removeFromCart(\''+item.id+'\')">Remove</button>' +
      '</div>' +
    '</div>';
  }).join('');
  const subtotal = cartSubtotal();
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

function toggleFav(btn) {
  btn.classList.toggle('active');
  showToast(btn.classList.contains('active') ? 'Added to wishlist' : 'Removed from wishlist', 'heart');
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

function hidePageLoader() {
  const loader = document.getElementById('pageLoader');
  if (!loader) return;
  loader.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
  initCart();
  setTimeout(hidePageLoader, 350);
});

window.addEventListener('load', hidePageLoader);
</script>
</body>
</html>
