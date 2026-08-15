<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/* =========================================================
   FEEDTAN STORE — DESIGN TOKENS
========================================================= */
:root{
  --green-950:#0B2017;
  --green-900:#013618;
  --green-800:#014a22;
  --green-700:#015425;
  --green-600:#017a36;
  --green-500:#029642;
  --green-100:#e3f3e9;
  --green-50:#f2f9f5;
  --gold:#FF8A00;
  --gold-dark:#e07200;
  --gold-light:#FFF3E0;
  --ink:#0e1a14;
  --ink-soft:#4a5a52;
  --line:#e6ece8;
  --parchment:#f6f9f7;
  --parchment-dim:#e3f3e9;
  --red:#c0392b;
  --red-dim:#fbe7e7;
  --white:#ffffff;
  --success:#2E7D4F;
  --orange:var(--gold);
  --orange-dark:var(--gold-dark);
  --orange-light:var(--gold-light);

  --font-display:'Poppins', sans-serif;
  --font-body:'Inter', sans-serif;
  --font-mono:'JetBrains Mono', monospace;

  --radius-s:8px;
  --radius-m:14px;
  --radius-l:20px;
  --radius-xl:28px;
  --shadow-card:0 1px 3px rgba(1,60,24,.08), 0 1px 2px rgba(1,60,24,.06);
  --shadow-lift:0 8px 24px rgba(1,60,24,.10);
  --shadow-pop:0 20px 50px rgba(1,60,24,.16);
  --maxw:1280px;
  --header-h:72px;
  --ease:cubic-bezier(.2,.8,.25,1);
}

html.dark{
  --ink:#eef4f0;
  --ink-soft:#a9bdb2;
  --line:#1d2b23;
  --parchment:#0b1410;
  --parchment-dim:#132019;
  --white:#111d16;
  --green-50:#0f2018;
  --green-100:#12271c;
  --gold-light:#2a1c0c;
  --red-dim:#2a1310;
  --shadow-card:0 1px 3px rgba(0,0,0,.4);
  --shadow-lift:0 8px 24px rgba(0,0,0,.45);
  --shadow-pop:0 20px 50px rgba(0,0,0,.55);
}

*{box-sizing:border-box;}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%;}
body{
  margin:0;
  font-family:var(--font-body);
  background:var(--parchment);
  color:var(--ink);
  -webkit-font-smoothing:antialiased;
  line-height:1.55;
  min-height:100dvh;
  overscroll-behavior-y:none;
  transition:background .3s ease, color .3s ease;
}
img{max-width:100%;display:block;}
a{color:inherit;text-decoration:none;}
button{font-family:inherit;cursor:pointer;touch-action:manipulation;border:none;background:none;color:inherit;}
input,select,textarea{font-family:inherit;font-size:16px;}
.wrap{max-width:var(--maxw);margin:0 auto;padding:0 20px;}
h1,h2,h3,h4{font-family:var(--font-display);margin:0;letter-spacing:-0.01em;line-height:1.15;}
p{margin:0;}
ul{margin:0;padding:0;list-style:none;}
.mono{font-family:var(--font-mono);}
::selection{background:var(--gold-light);color:var(--green-800);}

:focus-visible{outline:2.5px solid var(--gold);outline-offset:2px;border-radius:6px;}

@media (prefers-reduced-motion: reduce){
  *,*::before,*::after{animation-duration:.001ms !important;animation-iteration-count:1 !important;transition-duration:.001ms !important;scroll-behavior:auto !important;}
}

/* ---------- Buttons ---------- */
.btn{
  display:inline-flex;align-items:center;justify-content:center;gap:8px;
  border:none;border-radius:999px;font-weight:600;font-size:15px;
  padding:13px 22px;min-height:46px;
  transition:transform .15s ease, box-shadow .15s ease, background .15s ease, border-color .15s ease;
  white-space:nowrap;text-align:center;
}
.btn:active{transform:scale(.96);}
.btn-primary{background:var(--green-700);color:var(--white);box-shadow:var(--shadow-card);}
.btn-primary:hover{background:var(--green-600);box-shadow:var(--shadow-lift);transform:translateY(-1px);}
.btn-dark{background:var(--green-900);color:var(--white);}
.btn-dark:hover{background:var(--green-800);}
.btn-gold{background:var(--gold);color:#fff;box-shadow:0 8px 20px rgba(255,138,0,.30);}
.btn-gold:hover{background:var(--gold-dark);transform:translateY(-1px);}
.btn-outline{background:transparent;color:var(--green-700);border:1.5px solid var(--green-700);}
.btn-outline:hover{background:var(--green-100);}
.btn-ghost{background:transparent;color:var(--ink);border:1.5px solid var(--line);}
.btn-ghost:hover{background:var(--white);}
.btn-ghost-white{background:rgba(255,255,255,.14);color:#fff;border:1.5px solid rgba(255,255,255,.35);backdrop-filter:blur(6px);}
.btn-ghost-white:hover{background:rgba(255,255,255,.24);}
.btn-block{width:100%;}
.btn-sm{padding:8px 15px;min-height:38px;font-size:13px;}
.btn-lg{padding:16px 26px;min-height:54px;font-size:16px;}
.btn:disabled{opacity:.45;cursor:not-allowed;transform:none;box-shadow:none;}

.pill{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;padding:4px 11px;border-radius:999px;letter-spacing:.02em;}
.pill-green{background:var(--green-100);color:var(--green-700);}
.pill-orange{background:var(--gold-light);color:var(--gold-dark);}
.pill-red{background:var(--red-dim);color:var(--red);}
.pill-gray{background:#EFEFE9;color:#6B6B60;}
.pill-blue{background:#E3EAFB;color:#2D4E9E;}
html.dark .pill-gray{background:#1d2b23;color:#a9bdb2;}
html.dark .pill-blue{background:#122033;color:#9db8f5;}

.visually-hidden{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;}

/* ---------- Top bar ---------- */
.topbar{background:var(--green-900);color:#CFE3D7;font-size:12.5px;}
.topbar .wrap{display:flex;align-items:center;justify-content:space-between;padding:7px 20px;gap:12px;flex-wrap:wrap;}
.topbar-msg{display:flex;align-items:center;gap:8px;}
.topbar-msg svg{flex-shrink:0;}

/* ---------- Header ---------- */
header.site-header{
  position:sticky;top:0;z-index:200;background:var(--parchment);
  backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
  border-bottom:1px solid var(--line);transition:box-shadow .2s ease, background .3s ease;
}
header.site-header.scrolled{box-shadow:0 4px 20px rgba(1,60,24,.08);}
.header-inner{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:11px 20px;}
.logo{display:flex;align-items:center;gap:10px;font-family:var(--font-display);font-weight:800;font-size:20px;color:var(--green-800);flex-shrink:0;line-height:1;}
.logo img.logo-img{width:40px;height:40px;border-radius:12px;object-fit:cover;}
.logo .logo-mark{
  width:40px;height:40px;border-radius:12px;background:var(--green-700);
  display:flex;align-items:center;justify-content:center;color:var(--gold);
  font-size:18px;font-weight:900;flex-shrink:0;
}
.logo-sub{display:block;font-family:var(--font-body);font-weight:600;font-size:10px;letter-spacing:.12em;color:var(--ink-soft);text-transform:uppercase;margin-top:2px;}
html.dark .logo{color:#eaf4ee;}

.search-bar{
  flex:1;display:flex;align-items:center;background:var(--green-50);border:1.5px solid transparent;
  border-radius:999px;padding:0 6px 0 16px;max-width:560px;transition:border-color .15s, background .15s, box-shadow .15s;
}
.search-bar:focus-within{border-color:var(--green-600);background:var(--white);box-shadow:0 0 0 4px var(--green-100);}
.search-bar input{flex:1;border:none;background:transparent;padding:10px 8px;font-size:14.5px;outline:none;color:var(--ink);min-width:0;}
.search-bar button{background:var(--green-700);color:#fff;border-radius:999px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:none;transition:background .15s;}
.search-bar button:hover{background:var(--green-600);}

.header-actions{display:flex;align-items:center;gap:6px;flex-shrink:0;}
.icon-btn{
  position:relative;width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;
  background:transparent;border:none;color:var(--green-800);transition:background .15s, color .15s;font-size:16px;
}
.icon-btn:hover{background:var(--green-100);}
.icon-btn .badge, .badge-count{
  position:absolute;top:-2px;right:-2px;background:var(--gold);color:#fff;font-size:10px;font-weight:700;
  min-width:18px;height:18px;border-radius:999px;display:flex;align-items:center;justify-content:center;padding:0 4px;
  border:2px solid var(--parchment);
}
.hamburger{display:none;border-radius:12px;align-items:center;justify-content:center;background:var(--green-50);color:var(--green-700);font-size:18px;border:none;width:42px;height:42px;}
.hide-on-desktop{display:none;}

.lang-switch{display:flex;align-items:center;gap:4px;margin-left:6px;background:var(--white);border:1.5px solid var(--line);border-radius:999px;padding:3px;}
.lang-switch a{font-size:12px;font-weight:800;padding:5px 10px;border-radius:999px;color:var(--ink-soft);transition:all .15s;letter-spacing:.03em;}
.lang-switch a:hover{color:var(--green-700);}
.lang-switch a.active{background:var(--green-700);color:#fff;}

.theme-toggle{position:relative;overflow:hidden;}
.theme-toggle i{transition:.3s;}

.mobile-search{display:none;padding:0 20px 12px;}

/* ---------- Nav strip ---------- */
.nav-strip{border-top:1px solid var(--line);}
.nav-strip .wrap{display:flex;gap:24px;padding:10px 20px;overflow-x:auto;scrollbar-width:none;scroll-snap-type:x proximity;}
.nav-strip .wrap::-webkit-scrollbar{display:none;}
.nav-strip a{font-size:13.5px;font-weight:600;color:var(--ink-soft);white-space:nowrap;transition:color .15s;display:flex;align-items:center;gap:6px;padding:4px 0;scroll-snap-align:start;position:relative;}
.nav-strip a::after{content:'';position:absolute;left:0;right:0;bottom:-2px;height:2px;background:var(--gold);border-radius:2px;transform:scaleX(0);transition:transform .2s ease;transform-origin:left;}
.nav-strip a:hover,.nav-strip a.active{color:var(--green-700);}
.nav-strip a.active::after{transform:scaleX(1);}

/* ---------- Mobile off-canvas menu ---------- */
.mobile-menu{
  position:fixed;top:0;left:0;bottom:0;width:min(340px,86vw);background:var(--white);z-index:240;
  transform:translateX(-105%);transition:transform .3s var(--ease);
  display:flex;flex-direction:column;overflow-y:auto;
  box-shadow:var(--shadow-pop);
}
.mobile-menu.open{transform:translateX(0);}
.mm-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--line);}
.mm-nav{padding:12px 12px;flex:1;}
.mm-nav a{
  display:flex;align-items:center;gap:12px;padding:13px 14px;border-radius:12px;
  font-weight:600;font-size:15px;color:var(--ink);transition:background .15s;
}
.mm-nav a .mm-ic{width:34px;height:34px;border-radius:10px;background:var(--green-100);color:var(--green-700);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;}
.mm-nav a:hover{background:var(--parchment);}
.mm-nav a.active{background:var(--green-100);color:var(--green-700);}
.mm-footer{padding:16px 20px calc(18px + env(safe-area-inset-bottom));border-top:1px solid var(--line);background:var(--parchment);}
.mm-footer .lang-switch{margin-left:0;justify-content:center;}
.mm-contact{font-size:13px;color:var(--ink-soft);line-height:1.9;margin-top:12px;text-align:center;}

/* ---------- Mobile cart bottom bar ---------- */
.mobile-cart-bar{
  position:fixed;bottom:0;left:0;right:0;z-index:180;
  background:rgba(255,255,255,.97);backdrop-filter:blur(10px);
  border-top:1px solid var(--line);box-shadow:0 -10px 30px rgba(1,60,24,.12);
  display:flex;align-items:center;gap:12px;padding:10px 16px calc(10px + env(safe-area-inset-bottom));
  transform:translateY(120%);transition:transform .28s var(--ease);
}
html.dark .mobile-cart-bar{background:rgba(17,29,22,.97);}
.mobile-cart-bar.visible{transform:translateY(0);}
.has-bottom-nav .mobile-cart-bar{bottom:66px;border-radius:20px 20px 0 0;}
.mcb-left{display:flex;align-items:center;gap:10px;min-width:0;flex:1;}
.mcb-ic{position:relative;width:42px;height:42px;border-radius:12px;background:var(--green-700);color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:17px;}
.mcb-ic .badge{position:absolute;top:-5px;right:-5px;background:var(--gold);color:#fff;font-size:10px;font-weight:800;min-width:18px;height:18px;border-radius:999px;display:flex;align-items:center;justify-content:center;padding:0 4px;border:2px solid #fff;}
.mcb-info{min-width:0;}
.mcb-total{font-weight:800;font-family:var(--font-display);font-size:16px;color:var(--ink);line-height:1.1;}
.mcb-sub{font-size:11.5px;color:var(--ink-soft);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}

/* ---------- Scrim ---------- */
.scrim{position:fixed;inset:0;background:rgba(6,20,12,.55);z-index:220;opacity:0;visibility:hidden;transition:opacity .25s ease;backdrop-filter:blur(2px);}
.scrim.open{opacity:1;visibility:visible;}

/* ---------- Cart drawer ---------- */
.cart-drawer{
  position:fixed;top:0;right:0;bottom:0;width:min(440px,100vw);background:var(--white);z-index:235;
  box-shadow:var(--shadow-pop);display:flex;flex-direction:column;
  transform:translateX(105%);transition:transform .32s var(--ease);
}
.cart-drawer.open{transform:translateX(0);}
.drawer-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--line);flex-shrink:0;}
.drawer-head h3{font-size:17px;font-weight:800;display:flex;align-items:center;gap:10px;}
.drawer-head h3 .dc-ic{width:30px;height:30px;border-radius:9px;background:var(--green-100);color:var(--green-700);display:flex;align-items:center;justify-content:center;font-size:13px;}
.close-x{width:36px;height:36px;border-radius:50%;border:none;background:var(--green-50);display:flex;align-items:center;justify-content:center;color:var(--ink);flex-shrink:0;transition:background .15s;}
.close-x:hover{background:var(--line);}
.cart-list{flex:1;overflow-y:auto;padding:14px 20px;display:flex;flex-direction:column;gap:0;-webkit-overflow-scrolling:touch;}
.cart-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;height:100%;gap:14px;color:var(--ink-soft);padding:40px 20px;}
.cart-empty i{font-size:52px;color:var(--line);}
.cart-row{display:flex;gap:12px;padding:14px 0;border-bottom:1px solid var(--line);}
.cart-row:last-child{border-bottom:none;}
.cart-row img{width:64px;height:64px;border-radius:10px;object-fit:cover;flex-shrink:0;background:var(--green-50);}
.cart-row-info{flex:1;min-width:0;display:flex;flex-direction:column;gap:4px;}
.cart-row-info b{font-size:13.5px;display:block;line-height:1.3;}
.cart-row-info .cr-meta{font-size:12px;color:var(--ink-soft);}
.cart-row-bottom{display:flex;align-items:center;justify-content:space-between;margin-top:6px;}
.cart-row .qty-stepper{flex:none;}
.cart-row .qty-stepper button{width:26px;height:30px;border-radius:0;}
.cart-row .qty-stepper span{width:26px;font-size:13px;}
.cr-remove{background:none;border:none;color:var(--ink-soft);font-size:12px;text-decoration:underline;padding:2px 0;align-self:flex-start;}
.cr-remove:hover{color:var(--red);}
.cr-price{font-weight:700;font-size:13.5px;font-family:var(--font-display);}

.drawer-foot{padding:16px 20px calc(16px + env(safe-area-inset-bottom));border-top:1px solid var(--line);background:var(--parchment);flex-shrink:0;}
.free-delivery-bar{margin-bottom:14px;}
.fdb-track{height:8px;border-radius:999px;background:var(--line);overflow:hidden;}
.fdb-fill{height:100%;width:0;border-radius:999px;background:linear-gradient(90deg,var(--gold),var(--green-600));transition:width .5s var(--ease);}
.fdb-text{font-size:12px;font-weight:700;color:var(--ink-soft);margin-top:7px;line-height:1.4;}
.fdb-text b{color:var(--green-700);}
.fdb-text.done b{color:var(--success);}
.sum-row{display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:7px;color:var(--ink-soft);}
.sum-row.total{font-size:17px;font-weight:800;color:var(--ink);margin-top:10px;padding-top:10px;border-top:1px dashed var(--line);}
.sum-row.total span:last-child{font-family:var(--font-display);color:var(--green-700);}

/* ---------- Toast ---------- */
.toast{
  position:fixed;bottom:calc(20px + env(safe-area-inset-bottom));left:50%;transform:translateX(-50%) translateY(24px);
  background:var(--green-900);color:#fff;padding:13px 20px;border-radius:999px;font-size:13.5px;font-weight:600;
  z-index:500;box-shadow:var(--shadow-pop);display:flex;align-items:center;gap:10px;
  opacity:0;visibility:hidden;transition:all .28s var(--ease);max-width:calc(100vw - 32px);
}
.toast.show{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0);}
.toast i{color:var(--gold);flex-shrink:0;}

/* ---------- Page loader ---------- */
.page-loader{
  position:fixed;inset:0;z-index:9999;background:var(--parchment);backdrop-filter:blur(8px);
  display:flex;align-items:center;justify-content:center;transition:opacity .3s ease, visibility .3s ease;
}
.page-loader.hidden{opacity:0;visibility:hidden;pointer-events:none;}
.page-loader-card{text-align:center;padding:24px;}
.page-loader-ring{position:relative;width:108px;height:108px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;}
.page-loader-ring::before{
  content:'';position:absolute;inset:0;border-radius:50%;border:4px solid rgba(1,84,37,.14);border-top-color:var(--green-700);
  animation:spinLoader 1s linear infinite;
}
.page-loader-logo{width:72px;height:72px;border-radius:50%;object-fit:cover;background:#fff;box-shadow:var(--shadow-card);padding:4px;}
@keyframes spinLoader{to{transform:rotate(360deg);}}

/* ---------- Sections & cards ---------- */
.section{padding:44px 0 72px;}
.section-head{display:flex;align-items:baseline;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap;}
.section-head h1,.section-head h2{font-size:clamp(24px,3vw,30px);font-weight:800;}
.section-head .eyebrow,.section-eyebrow{display:block;font-family:var(--font-body);font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--gold-dark);margin-bottom:6px;}
.section-head p{color:var(--ink-soft);font-size:14.5px;margin:6px 0 0;}
.section-head-centered{justify-content:center;text-align:center;}
.section-head-centered > div{max-width:640px;}
.see-all{font-size:13.5px;font-weight:700;color:var(--green-700);display:flex;align-items:center;gap:5px;flex-shrink:0;}
.see-all:hover{color:var(--gold-dark);}
.back-link{display:inline-flex;align-items:center;gap:6px;font-size:13.5px;font-weight:700;color:var(--green-700);}
.back-link:hover{color:var(--gold-dark);}

.card{background:var(--white);border-radius:var(--radius-l);box-shadow:var(--shadow-card);padding:24px;margin-bottom:20px;border:1px solid var(--line);}

/* ---------- Forms ---------- */
.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;}
.field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px;}
.field label{font-size:12.5px;font-weight:700;color:var(--ink-soft);}
.field input,.field select,.field textarea{
  border:1.5px solid var(--line);border-radius:12px;padding:12px 14px;font-size:15px;color:var(--ink);
  background:var(--parchment);outline:none;transition:border-color .15s, box-shadow .15s;width:100%;min-height:48px;
}
.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--green-600);background:var(--white);box-shadow:0 0 0 3px var(--green-100);}
.field-error{font-size:12px;color:var(--red);min-height:14px;line-height:1.3;}
.field.has-error input,.field.has-error select,.field.has-error textarea{border-color:var(--red);}

.option-card{
  display:flex;align-items:flex-start;gap:14px;border:1.5px solid var(--line);border-radius:16px;
  padding:16px;cursor:pointer;margin-bottom:12px;transition:border-color .18s, background .18s, transform .15s;
  background:var(--white);
}
.option-card:hover{border-color:var(--green-600);transform:translateY(-1px);}
.option-card.selected{border-color:var(--green-700);background:var(--green-50);box-shadow:0 0 0 3px var(--green-100);}
.option-card input{margin-top:3px;accent-color:var(--green-700);width:18px;height:18px;flex-shrink:0;}
.option-card .icon{width:42px;height:42px;border-radius:12px;background:var(--green-50);display:flex;align-items:center;justify-content:center;color:var(--green-700);flex-shrink:0;font-size:18px;}
.option-card.selected .icon{background:var(--green-100);}
.option-card b{display:block;font-size:14.5px;margin-bottom:2px;}
.option-card span{font-size:12.5px;color:var(--ink-soft);line-height:1.4;}
.option-grid-two{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}

/* ---------- Qty stepper ---------- */
.qty-stepper{display:flex;align-items:center;border:1.5px solid var(--line);border-radius:999px;overflow:hidden;flex:1;background:var(--white);}
.qty-stepper button{width:38px;height:44px;background:transparent;border:none;font-size:17px;font-weight:700;color:var(--green-700);transition:background .15s;}
.qty-stepper button:hover{background:var(--green-100);}
.qty-stepper span{flex:1;text-align:center;font-weight:700;font-size:15px;}

/* ---------- Maps ---------- */
.mini-map{width:100%;height:280px;border-radius:14px;margin-top:10px;overflow:hidden;border:1px solid var(--line);position:relative;z-index:10;}
.mini-map .leaflet-control-container .leaflet-control{border-radius:10px;overflow:hidden;}
.mini-map .leaflet-control-container{z-index:20;}
.mini-map .leaflet-pane{z-index:15;}
.map-container{width:100%;height:360px;border-radius:16px;overflow:hidden;border:1px solid var(--line);position:relative;z-index:10;}
.map-container .leaflet-control-container{z-index:20;}
.map-container .leaflet-pane{z-index:15;}

/* ---------- Modals ---------- */
.modal-backdrop{
  position:fixed;inset:0;background:rgba(6,20,12,.55);z-index:500;display:flex;align-items:center;justify-content:center;
  opacity:0;visibility:hidden;transition:.25s ease;backdrop-filter:blur(3px);padding:16px;
}
.modal-backdrop.open{opacity:1;visibility:visible;}
.modal-box{
  background:var(--white);border-radius:var(--radius-l);max-width:900px;width:100%;max-height:88vh;overflow-y:auto;
  transform:scale(.94) translateY(14px);transition:.28s cubic-bezier(.2,.8,.2,1);box-shadow:var(--shadow-pop);position:relative;
}
.modal-backdrop.open .modal-box{transform:scale(1) translateY(0);}
.modal-box.narrow{max-width:480px;}
.modal-box.medium{max-width:620px;}
.modal-close{
  position:absolute;top:16px;right:16px;width:38px;height:38px;border-radius:50%;background:var(--green-50);color:var(--ink);
  display:flex;align-items:center;justify-content:center;z-index:5;transition:.2s;border:none;font-size:16px;
}
.modal-close:hover{background:var(--green-700);color:#fff;transform:rotate(90deg);}
.modal-head{padding:22px 26px;border-bottom:1px solid var(--line);}
.modal-head h3{font-size:19px;font-weight:800;margin:0;}
.modal-body{padding:24px 26px;}
.modal-foot{padding:18px 26px;border-top:1px solid var(--line);display:flex;gap:10px;}

/* ---------- Bottom navigation ---------- */
.bottom-nav{
  display:none;position:fixed;bottom:0;left:0;right:0;background:var(--white);border-top:1px solid var(--line);z-index:250;
  padding:8px 6px calc(8px + env(safe-area-inset-bottom));box-shadow:0 -4px 16px rgba(0,0,0,.06);
}
.bn-row{display:flex;justify-content:space-around;}
.bn-item{display:flex;flex-direction:column;align-items:center;gap:3px;font-size:10px;font-weight:700;color:var(--ink-soft);padding:4px 8px;position:relative;background:none;border:none;}
.bn-item.active{color:var(--green-700);}
.bn-item i{font-size:18px;}
.bn-badge{position:absolute;top:0;right:4px;background:var(--gold);color:#fff;font-size:9px;min-width:15px;height:15px;border-radius:50%;display:flex;align-items:center;justify-content:center;}

/* ---------- Footer ---------- */
footer{background:var(--green-900);color:rgba(255,255,255,.85);padding:50px 0 0;margin-top:40px;}
.footer-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1.2fr;gap:32px;padding-bottom:36px;}
.footer-grid h4{color:#fff;font-family:var(--font-body);font-size:12.5px;letter-spacing:.08em;text-transform:uppercase;margin-bottom:14px;}
.footer-grid ul{display:flex;flex-direction:column;gap:9px;font-size:13.5px;}
.footer-grid ul a:hover{color:var(--gold);}
.footer-logo{display:flex;align-items:center;gap:10px;color:#fff;font-family:var(--font-display);font-weight:800;font-size:20px;margin-bottom:12px;}
.footer-social{display:flex;gap:10px;margin-top:16px;}
.footer-social .icon-btn{background:rgba(255,255,255,.1);color:#fff;font-size:15px;}
.footer-social .icon-btn:hover{background:var(--gold);color:#fff;}
.footer-bottom{border-top:1px solid rgba(255,255,255,.12);padding:18px 20px;display:flex;justify-content:space-between;font-size:12.5px;flex-wrap:wrap;gap:8px;}
.footer-bottom span{opacity:.8;}

/* ---------- Reveal on scroll ---------- */
.reveal{opacity:0;transform:translateY(20px);transition:opacity .55s ease, transform .55s var(--ease);}
.reveal.in{opacity:1;transform:none;}

/* ---------- Responsive ---------- */
@media(max-width:900px){
  .search-bar{display:none;}
  .mobile-search{display:block;}
  .logo-sub{display:none;}
  .hamburger{display:flex;}
  .hide-on-mobile{display:none !important;}
  .hide-on-desktop{display:flex;}
  .header-inner{padding:10px 16px;gap:8px;}
}
@media(max-width:768px){
  :root{--header-h:64px;}
  .bottom-nav{display:block;}
  body{padding-bottom:64px;}
}
@media(max-width:600px){
  .wrap{padding:0 14px;}
  .topbar .wrap{justify-content:center;padding:7px 14px;text-align:center;}
  .topbar-msg{justify-content:center;width:100%;}
  #topbarPhone{display:none;}
  .icon-btn{width:38px;height:38px;}
  .logo{font-size:18px;}
  .logo-mark,.logo img.logo-img{width:36px;height:36px;font-size:16px;}
  .nav-strip .wrap{padding:9px 14px;gap:20px;}
  .mobile-search{padding:0 14px 12px;}
  .section{padding:34px 0 96px;}
  .card{padding:18px;border-radius:16px;}
  .form-grid{grid-template-columns:1fr;}
  .option-grid-two{grid-template-columns:1fr;}
  .footer-grid{grid-template-columns:1fr 1fr;gap:26px;}
  .footer-bottom{padding:18px 14px;flex-direction:column;gap:4px;}
  .map-container{height:300px;}
  .mini-map{height:220px;}
}
@media(max-width:480px){
  .footer-grid{grid-template-columns:1fr;}
  .drawer-head,.cart-list,.drawer-foot{padding-left:16px;padding-right:16px;}
  .toast{left:16px;right:16px;transform:translateY(24px);width:auto;max-width:none;border-radius:16px;padding:12px 16px;justify-content:center;}
  .toast.show{transform:translateY(0);}
  .modal-body{padding:18px;}
}
</style>
