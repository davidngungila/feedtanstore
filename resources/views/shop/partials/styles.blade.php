<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;0,9..144,900;1,9..144,500;1,9..144,600&family=Manrope:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* ==========================================================================
   FEEDTAN STORE — DESIGN SYSTEM
   Tokens -> base -> typography -> buttons -> header -> cart -> sections
   -> product cards -> product page -> checkout -> tracking -> footer
   -> responsive
   ========================================================================== */

/* ---------- TOKENS ---------- */
:root{
  --green-900:#123328;
  --green-800:#173d30;
  --green-700:#1f5c43;
  --green-600:#26714f;
  --green-500:#2e7d5b;
  --green-300:#7fae97;
  --green-100:#e4f0e9;
  --green-050:#f0f6f2;

  --orange-600:#e8720c;
  --orange-500:#f0821f;
  --orange-400:#f2954a;
  --orange-100:#fde9d4;
  --gold:#e8720c;
  --gold-dark:#c9610a;
  --gold-light:#fde9d4;

  --cream:#faf7ef;
  --paper:#fdfbf5;
  --parchment:#faf7ef;
  --parchment-dim:#f0f6f2;

  --ink:#17231d;
  --ink-soft:#516058;
  --ink-faint:#8a978f;
  --line:#dfe6df;
  --white:#ffffff;

  --red:#c0392b;
  --red-dim:#fbe7e7;
  --success:#2e7d5b;
  --success-dim:#e4f0e9;
  --blue:#2563eb;
  --blue-dim:#e8efff;

  --font-display:'Fraunces',Georgia,serif;
  --font-body:'Manrope',system-ui,-apple-system,sans-serif;
  --font-mono:'IBM Plex Mono',ui-monospace,monospace;

  --radius-s:10px;
  --radius:18px;
  --radius-m:14px;
  --radius-l:20px;
  --radius-xl:28px;

  --shadow-card:0 2px 10px rgba(18,51,40,.05);
  --shadow-lift:0 10px 30px -12px rgba(18,51,40,.25);
  --shadow-pop:0 18px 40px -18px rgba(18,51,40,.35);

  --maxw:1200px;
  --header-h:72px;
  --ease:cubic-bezier(.2,.8,.25,1);
}

/* ---------- BASE ---------- */
*,*::before,*::after{box-sizing:border-box;}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%;overflow-x:clip;}
body{
  margin:0;
  font-family:var(--font-body);
  font-size:15px;
  line-height:1.6;
  background:var(--cream);
  color:var(--ink);
  -webkit-font-smoothing:antialiased;
  overflow-x:clip;
}
img,svg{display:block;max-width:100%;}
a{color:inherit;text-decoration:none;}
button{font-family:inherit;cursor:pointer;}
input,select,textarea{font-family:inherit;font-size:inherit;color:inherit;}
h1,h2,h3,h4{font-family:var(--font-display);margin:0;color:var(--green-900);line-height:1.15;}
p{margin:0;}
ul{margin:0;padding:0;list-style:none;}
:focus-visible{outline:3px solid var(--orange-500);outline-offset:2px;border-radius:6px;}

html.dark h1,html.dark h2,html.dark h3,html.dark h4{color:var(--ink);}

.wrap{width:100%;max-width:var(--maxw);margin:0 auto;padding:0 20px;}
.mono{font-family:var(--font-mono);}
.visually-hidden{
  position:absolute!important;width:1px;height:1px;padding:0;margin:-1px;
  overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;border:0;
}
.visually-hidden-focusable:focus{position:static!important;width:auto;height:auto;clip:auto;margin:0;}

/* ---------- TYPOGRAPHY HELPERS ---------- */
.eyebrow,.section-eyebrow,.hero-eyebrow{
  font-family:var(--font-mono);
  font-size:12px;
  letter-spacing:.14em;
  text-transform:uppercase;
  color:var(--green-600);
  font-weight:600;
  display:inline-block;
}
.section{padding:64px 0;}
.section-head{max-width:620px;margin-bottom:32px;}
.sec-head{max-width:620px;margin-bottom:32px;}
.sec-head h2{font-size:clamp(26px,3.4vw,38px);margin-top:8px;}
.sec-head p{color:var(--ink-soft);margin-top:10px;font-size:15.5px;}
.section-head h2{font-size:clamp(26px,3.4vw,38px);margin-top:8px;}
.section-head p{color:var(--ink-soft);margin-top:10px;font-size:15.5px;}
.section-head-centered{margin-left:auto;margin-right:auto;text-align:center;}
.lead{font-size:17px;color:var(--ink-soft);}
.muted{color:var(--ink-soft);}
.small{font-size:12.5px;}

/* ---------- CARDS ---------- */
.card{
  background:var(--paper);
  border:1px solid var(--line);
  border-radius:var(--radius);
  padding:22px;
  box-shadow:var(--shadow-card);
}
.card-center{text-align:center;}
.see-all{font-size:13.5px;font-weight:700;color:var(--orange-600);white-space:nowrap;}
.see-all:hover{color:var(--orange-500);}
.back-link{
  display:inline-flex;align-items:center;gap:8px;font-size:14px;font-weight:600;
  color:var(--green-700);margin-bottom:18px;
}
.back-link:hover{color:var(--orange-600);}

/* ---------- BUTTONS ---------- */
.btn{
  display:inline-flex;align-items:center;justify-content:center;gap:8px;
  padding:13px 24px;border-radius:999px;border:1.5px solid transparent;
  font-family:var(--font-body);font-weight:700;font-size:14.5px;line-height:1.2;
  transition:background .18s var(--ease),color .18s var(--ease),border-color .18s var(--ease),transform .12s var(--ease),box-shadow .18s var(--ease);
  white-space:nowrap;text-align:center;
}
.btn:active{transform:scale(.97);}
.btn-primary{background:var(--orange-600);color:#fff;box-shadow:0 8px 20px -8px rgba(232,114,12,.55);}
.btn-primary:hover{background:var(--orange-500);}
.btn-dark{background:var(--green-800);color:#fff;}
.btn-dark:hover{background:var(--green-900);}
.btn-gold{background:var(--gold);color:#fff;}
.btn-gold:hover{background:var(--gold-dark);}
.btn-outline{background:transparent;color:var(--green-800);border-color:var(--green-700);}
.btn-outline:hover{background:var(--green-050);}
.btn-ghost{background:var(--green-050);color:var(--green-800);}
.btn-ghost:hover{background:var(--green-100);}
.btn-ghost-white{background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.3);}
.btn-ghost-white:hover{background:rgba(255,255,255,.22);}
.btn-danger{background:var(--red);color:#fff;}
.btn-danger:hover{filter:brightness(.94);}
.btn-block{width:100%;}
.btn-sm{padding:9px 16px;font-size:13.5px;}
.btn-lg{padding:15px 30px;font-size:15.5px;}
.btn[disabled],.btn:disabled{opacity:.5;pointer-events:none;}
.btn-icon{width:38px;height:38px;padding:0;border-radius:50%;}

/* ---------- PILLS ---------- */
.pill{
  display:inline-flex;align-items:center;gap:5px;
  font-family:var(--font-mono);font-size:11px;font-weight:700;
  padding:4px 9px;border-radius:999px;letter-spacing:.02em;white-space:nowrap;
}
.pill-green{background:var(--green-100);color:var(--green-700);}
.pill-orange{background:var(--orange-100);color:var(--orange-600);}
.pill-red{background:var(--red-dim);color:var(--red);}
.pill-gray{background:#eef1ef;color:var(--ink-soft);}
.pill-blue{background:var(--blue-dim);color:var(--blue);}

/* ---------- TOP BAR ---------- */
.topbar{background:var(--green-900);color:#cfe0d7;font-size:12.5px;}
.topbar .wrap{display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:38px;flex-wrap:wrap;}
.topbar-msg{display:inline-flex;align-items:center;gap:8px;font-weight:600;padding:6px 0;}
.topbar-msg i{color:var(--orange-400);}

/* ---------- HEADER ---------- */
.site-header{
  position:sticky;top:0;z-index:60;
  background:rgba(250,247,239,.92);
  backdrop-filter:blur(12px);
  -webkit-backdrop-filter:blur(12px);
  border-bottom:1px solid var(--line);
  transition:box-shadow .2s var(--ease);
}
.site-header.scrolled{box-shadow:0 6px 24px -14px rgba(18,51,40,.4);}
.header-inner{display:flex;align-items:center;gap:16px;min-height:var(--header-h);}
.logo{display:inline-flex;align-items:center;gap:10px;font-family:var(--font-display);font-weight:700;font-size:20px;color:var(--green-900);flex-shrink:0;}
.logo-img{height:34px;width:auto;object-fit:contain;}
.logo-mark{
  width:34px;height:34px;border-radius:10px;flex-shrink:0;
  background:var(--green-700);color:#fff;
  display:inline-flex;align-items:center;justify-content:center;font-size:15px;
}
.logo-sub{
  display:block;font-family:var(--font-mono);font-size:9.5px;letter-spacing:.16em;
  text-transform:uppercase;color:var(--orange-600);font-weight:600;line-height:1.2;
}
.search-bar{
  display:flex;align-items:center;gap:8px;flex:1;max-width:340px;
  background:var(--white);border:1.5px solid var(--line);border-radius:999px;padding:8px 8px 8px 16px;
  transition:border-color .18s var(--ease),box-shadow .18s var(--ease);
}
.search-bar:focus-within{border-color:var(--green-500);box-shadow:0 0 0 4px rgba(46,125,91,.12);}
.search-bar input{
  border:none;outline:none;background:none;width:100%;min-width:0;
  font-family:var(--font-body);font-size:13.5px;color:var(--ink);
}
.search-bar input::placeholder{color:var(--ink-faint);}
.search-bar button{
  width:32px;height:32px;border-radius:50%;border:none;flex-shrink:0;
  background:var(--green-050);color:var(--green-700);
  display:inline-flex;align-items:center;justify-content:center;font-size:13px;
}
.search-bar button:hover{background:var(--green-100);}
.header-actions{display:flex;align-items:center;gap:10px;margin-left:auto;}
.icon-btn{
  position:relative;width:42px;height:42px;border-radius:50%;
  border:1.5px solid var(--line);background:var(--white);color:var(--ink);
  display:inline-flex;align-items:center;justify-content:center;
  font-size:16px;flex-shrink:0;
  transition:background .18s var(--ease),border-color .18s var(--ease),transform .12s var(--ease),color .18s var(--ease);
}
.icon-btn:hover{border-color:var(--green-500);color:var(--green-700);transform:translateY(-1px);}
.icon-btn .badge,.badge{
  position:absolute;top:-5px;right:-5px;
  background:var(--orange-600);color:#fff;
  font-family:var(--font-mono);font-size:10px;font-weight:700;
  border-radius:999px;min-width:19px;height:19px;padding:0 5px;
  display:flex;align-items:center;justify-content:center;
  border:2px solid var(--cream);
}
.hamburger{display:none;}
.lang-switch{
  display:inline-flex;align-items:center;gap:2px;padding:3px;
  background:var(--green-050);border-radius:999px;border:1px solid var(--line);
}
.lang-switch a{
  font-family:var(--font-mono);font-size:11.5px;font-weight:600;
  padding:5px 10px;border-radius:999px;color:var(--ink-soft);
}
.lang-switch a.active{background:var(--green-700);color:#fff;}
.mobile-search{display:none;padding:0 20px 14px;}
.mobile-search .search-bar{max-width:100%;}

/* nav strip (category links) */
.nav-strip{border-top:1px solid var(--line);background:var(--paper);}
.nav-strip .wrap{display:flex;align-items:center;gap:22px;overflow-x:auto;scrollbar-width:none;padding:0 20px;}
.nav-strip .wrap::-webkit-scrollbar{display:none;}
.nav-strip a{
  flex-shrink:0;padding:11px 0;font-size:14px;font-weight:600;color:var(--ink-soft);
  border-bottom:2px solid transparent;white-space:nowrap;
  display:inline-flex;align-items:center;gap:7px;
}
.nav-strip a:hover{color:var(--green-800);border-bottom-color:var(--orange-500);}
.nav-strip a.active{color:var(--green-900);border-bottom-color:var(--green-700);}

/* ---------- MOBILE OFF-CANVAS MENU ---------- */
.mobile-menu{
  position:fixed;inset:0 0 0 auto;width:min(360px,90vw);z-index:110;
  background:var(--cream);border-left:1px solid var(--line);
  transform:translateX(100%);transition:transform .3s var(--ease);
  display:flex;flex-direction:column;overflow-y:auto;
  box-shadow:-20px 0 50px rgba(18,51,40,.2);
}
.mobile-menu.open{transform:translateX(0);}
.mm-head{
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  padding:16px 18px;border-bottom:1px solid var(--line);
}
.mm-nav{padding:10px 18px;display:flex;flex-direction:column;}
.mm-nav a{
  display:flex;align-items:center;gap:12px;padding:12px 4px;
  font-weight:600;color:var(--ink);border-bottom:1px solid var(--line);
}
.mm-nav a:hover,.mm-nav a.active{color:var(--green-700);}
.mm-ic{
  width:34px;height:34px;border-radius:10px;flex-shrink:0;
  background:var(--green-050);color:var(--green-700);
  display:inline-flex;align-items:center;justify-content:center;font-size:14px;
}
.mm-footer{margin-top:auto;padding:18px;display:flex;flex-direction:column;gap:14px;}
.mm-contact{font-size:13px;color:var(--ink-soft);line-height:1.7;}

/* ---------- SCRIM ---------- */
.scrim{
  position:fixed;inset:0;z-index:100;background:rgba(18,51,40,.45);
  opacity:0;pointer-events:none;transition:opacity .25s var(--ease);
}
.scrim.open{opacity:1;pointer-events:auto;}

/* ---------- MOBILE CART BAR ---------- */
.mobile-cart-bar{
  position:fixed;left:0;right:0;bottom:0;z-index:80;
  background:var(--paper);border-top:1px solid var(--line);
  padding:10px 16px calc(10px + env(safe-area-inset-bottom));
  display:flex;align-items:center;gap:12px;
  transform:translateY(110%);transition:transform .3s var(--ease);
  box-shadow:0 -10px 30px -18px rgba(18,51,40,.5);
}
.mobile-cart-bar.visible{transform:translateY(0);}
.mcb-left{display:flex;align-items:center;gap:12px;flex:1;min-width:0;text-align:left;}
.mcb-ic{
  position:relative;width:42px;height:42px;border-radius:12px;flex-shrink:0;
  background:var(--green-050);color:var(--green-700);
  display:inline-flex;align-items:center;justify-content:center;font-size:17px;
}
.mcb-info{min-width:0;}
.mcb-total{display:block;font-family:var(--font-mono);font-weight:700;font-size:15px;color:var(--green-800);}
.mcb-sub{display:block;font-size:11.5px;color:var(--ink-faint);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}

/* ---------- CART DRAWER ---------- */
.cart-drawer{
  position:fixed;top:0;right:0;bottom:0;width:min(420px,100vw);z-index:120;
  background:var(--cream);border-left:1px solid var(--line);
  transform:translateX(100%);transition:transform .3s var(--ease);
  display:flex;flex-direction:column;box-shadow:-20px 0 50px rgba(18,51,40,.22);
}
.cart-drawer.open{transform:translateX(0);}
.drawer-head{
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  padding:18px 20px;border-bottom:1px solid var(--line);flex-shrink:0;
}
.drawer-head h3{font-size:19px;display:flex;align-items:center;gap:10px;}
.dc-ic{
  width:34px;height:34px;border-radius:10px;background:var(--orange-100);color:var(--orange-600);
  display:inline-flex;align-items:center;justify-content:center;font-size:14px;
}
.close-x{
  width:36px;height:36px;border-radius:50%;border:1.5px solid var(--line);background:var(--white);
  color:var(--ink);display:inline-flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;
  transition:border-color .18s var(--ease),color .18s var(--ease);
}
.close-x:hover{border-color:var(--red);color:var(--red);}
.cart-list{flex:1;overflow-y:auto;padding:8px 20px;-webkit-overflow-scrolling:touch;}
.cart-empty{text-align:center;padding:70px 10px;color:var(--ink-faint);}
.cart-empty i{font-size:34px;margin-bottom:14px;display:block;opacity:.6;}
.cart-empty b{display:block;font-family:var(--font-display);font-size:17px;color:var(--ink);margin-bottom:6px;}
.cart-empty span{display:block;font-size:13.5px;margin-bottom:18px;}
.cart-row{display:flex;gap:12px;padding:14px 0;border-bottom:1px solid var(--line);align-items:flex-start;}
.cart-row img{
  width:56px;height:56px;border-radius:12px;object-fit:cover;flex-shrink:0;
  background:var(--green-050);
}
.cart-row-info{flex:1;min-width:0;}
.cart-row-info b{display:block;font-size:14px;color:var(--green-900);line-height:1.35;}
.cr-meta{display:block;font-size:11.5px;color:var(--ink-faint);margin-top:2px;}
.cart-row-bottom{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:8px;}
.cr-price{font-family:var(--font-mono);font-weight:700;font-size:14px;color:var(--green-800);}
.cr-remove{
  margin-top:6px;background:none;border:none;padding:0;font-size:12px;font-weight:700;
  color:var(--red);text-decoration:underline;
}
.drawer-foot{padding:18px 20px calc(18px + env(safe-area-inset-bottom));border-top:1px solid var(--line);background:var(--paper);flex-shrink:0;}

/* free delivery progress */
.free-delivery-bar{margin-bottom:14px;}
.fdb-track{height:8px;border-radius:999px;background:var(--green-050);overflow:hidden;}
.fdb-fill{height:100%;width:0;border-radius:999px;background:var(--orange-600);transition:width .3s var(--ease);}
.fdb-text{font-size:12.5px;color:var(--ink-soft);margin-top:8px;}
.fdb-text.done{color:var(--success);}

.sum-row{display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:14px;margin-bottom:8px;color:var(--ink-soft);}
.sum-row.total{font-weight:800;font-size:17px;color:var(--green-900);font-family:var(--font-mono);margin-top:6px;}

/* ---------- QTY STEPPER ---------- */
.qty-stepper{
  display:inline-flex;align-items:center;background:var(--white);
  border:1.5px solid var(--line);border-radius:999px;overflow:hidden;flex-shrink:0;
}
.qty-stepper button{
  width:32px;height:32px;border:none;background:var(--green-050);color:var(--green-800);
  font-size:15px;font-weight:700;line-height:1;
  display:inline-flex;align-items:center;justify-content:center;
}
.qty-stepper button:hover{background:var(--green-100);}
.qty-stepper span{
  min-width:30px;text-align:center;font-family:var(--font-mono);
  font-size:13px;font-weight:600;
}

/* ---------- TOAST ---------- */
.toast{
  position:fixed;left:50%;bottom:26px;transform:translate(-50%,140%);
  z-index:140;display:flex;align-items:center;gap:10px;
  background:var(--green-900);color:#fff;
  padding:12px 20px;border-radius:999px;font-size:13.5px;font-weight:600;
  box-shadow:var(--shadow-pop);max-width:calc(100vw - 32px);
  transition:transform .3s var(--ease);
}
.toast.show{transform:translate(-50%,0);}

/* ---------- PAGE LOADER ---------- */
.page-loader{
  position:fixed;inset:0;z-index:200;background:var(--cream);
  display:flex;align-items:center;justify-content:center;
  transition:opacity .3s var(--ease),visibility .3s var(--ease);
}
.page-loader.hidden{opacity:0;visibility:hidden;}
.page-loader-card{text-align:center;display:flex;flex-direction:column;align-items:center;gap:14px;}
.page-loader-ring{
  width:74px;height:74px;border-radius:50%;padding:8px;
  border:2px solid var(--line);border-top-color:var(--orange-500);
  animation:ftSpin 1s linear infinite;
}
@keyframes ftSpin{to{transform:rotate(360deg);}}
.page-loader-logo{width:100%;height:100%;object-fit:contain;}

/* ---------- MODALS ---------- */
.modal-backdrop{
  position:fixed;inset:0;z-index:130;background:rgba(18,51,40,.5);
  display:flex;align-items:center;justify-content:center;padding:20px;
  opacity:0;pointer-events:none;transition:opacity .2s var(--ease);
}
.modal-backdrop.open{opacity:1;pointer-events:auto;}
.modal-box{
  width:100%;max-width:560px;max-height:88vh;overflow-y:auto;
  background:var(--paper);border-radius:var(--radius-xl);
  box-shadow:var(--shadow-pop);padding:24px;
  transform:translateY(14px);transition:transform .22s var(--ease);
}
.modal-backdrop.open .modal-box{transform:translateY(0);}
.modal-box.narrow{max-width:420px;}
.modal-box.medium{max-width:680px;}
.modal-close{
  position:absolute;top:16px;right:16px;z-index:2;
  width:34px;height:34px;border-radius:50%;border:1.5px solid var(--line);background:var(--white);
  color:var(--ink);display:inline-flex;align-items:center;justify-content:center;font-size:13px;
}
.modal-head{margin-bottom:16px;padding-right:44px;}
.modal-head h3{font-size:20px;}
.modal-body{font-size:14.5px;color:var(--ink-soft);}
.modal-foot{margin-top:20px;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;}

/* ---------- BOTTOM NAV ---------- */
.bottom-nav{
  position:fixed;left:0;right:0;bottom:0;z-index:70;
  background:var(--paper);border-top:1px solid var(--line);
  padding-bottom:env(safe-area-inset-bottom);
}
.bn-row{display:grid;grid-template-columns:repeat(5,1fr);}
.bn-item{
  position:relative;display:flex;flex-direction:column;align-items:center;gap:3px;
  padding:8px 2px;font-size:10.5px;font-weight:600;color:var(--ink-soft);
  background:none;border:none;
}
.bn-item i{font-size:17px;}
.bn-item.active{color:var(--green-700);}
.bn-badge{
  position:absolute;top:2px;right:22%;background:var(--orange-600);color:#fff;
  font-family:var(--font-mono);font-size:9.5px;font-weight:700;
  min-width:16px;height:16px;padding:0 4px;border-radius:999px;
  display:flex;align-items:center;justify-content:center;
}

/* ---------- FORMS ---------- */
.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;}
.field{margin-bottom:16px;}
.field label,.field-label{
  display:block;font-size:12.5px;font-weight:700;color:var(--ink-soft);margin-bottom:7px;
}
.field input,.field select,.field textarea,.input{
  width:100%;padding:12px 14px;border-radius:var(--radius-s);
  border:1.5px solid var(--line);background:var(--cream);
  font-family:var(--font-body);font-size:14.5px;color:var(--ink);
  transition:border-color .18s var(--ease),box-shadow .18s var(--ease);
}
.field input:focus,.field select:focus,.field textarea:focus,.input:focus{
  outline:none;border-color:var(--green-500);box-shadow:0 0 0 4px rgba(46,125,91,.12);
}
.field textarea{resize:vertical;min-height:78px;}
.field .field-error{
  display:none;margin-top:6px;font-size:12px;font-weight:600;color:var(--red);
}
.field.has-error input,.field.has-error textarea{border-color:var(--red);background:var(--red-dim);}
.field.has-error .field-error{display:block;}
.field-hint{font-size:12px;color:var(--ink-faint);margin-top:6px;}

.option-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;}
.option-card{
  display:flex;align-items:center;gap:11px;
  border:1.5px solid var(--line);border-radius:var(--radius-m);
  padding:13px 14px;cursor:pointer;background:var(--white);
  font-size:14px;font-weight:600;transition:.18s var(--ease);
}
.option-card:hover{border-color:var(--green-500);}
.option-card.selected{border-color:var(--green-600);background:var(--green-050);}
.option-card .icon{color:var(--green-700);font-size:16px;flex-shrink:0;}
.option-card input{accent-color:var(--green-600);flex-shrink:0;margin:0;}

/* ---------- MAPS ---------- */
.map-container,.mini-map{
  width:100%;height:220px;border-radius:var(--radius-m);
  border:1px solid var(--line);background:var(--green-050);z-index:1;
}
.map-container{height:280px;}

/* ---------- TRUST STRIP ---------- */
.trust-strip{padding:0 0 64px;}
.trust-grid{
  display:grid;grid-template-columns:repeat(4,1fr);gap:18px;
  background:var(--paper);border:1px solid var(--line);
  border-radius:var(--radius);padding:24px;
}
.trust-item{display:flex;align-items:center;gap:12px;}
.trust-item .ic{
  width:42px;height:42px;border-radius:12px;background:var(--green-050);color:var(--green-700);
  display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:17px;
}
.trust-item b{display:block;font-size:14px;color:var(--green-900);}
.trust-item span{font-size:12.5px;color:var(--ink-soft);}

/* ---------- CATEGORY CHIPS ---------- */
.cat-row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:26px;}
.cat-chip{
  display:inline-flex;align-items:center;gap:8px;
  padding:9px 16px;border-radius:999px;
  background:var(--paper);border:1.5px solid var(--line);
  font-size:13.5px;font-weight:700;color:var(--ink-soft);
  transition:.18s var(--ease);white-space:nowrap;
}
.cat-chip:hover{border-color:var(--green-500);color:var(--green-700);}
.cat-chip.active{background:var(--green-700);border-color:var(--green-700);color:#fff;}
.cat-chip .ic{font-size:14px;}

/* ---------- PRODUCT CARDS ---------- */
.product-grid{
  display:grid;grid-template-columns:repeat(4,1fr);gap:20px;
}
.p-card{
  background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);
  overflow:hidden;display:flex;flex-direction:column;
  transition:transform .2s var(--ease),box-shadow .2s var(--ease),border-color .2s var(--ease);
}
.p-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lift);border-color:transparent;}
.p-media{
  position:relative;height:168px;background:var(--green-050);
  display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;
}
.p-media img{width:100%;height:100%;object-fit:cover;transition:transform .35s var(--ease);}
.p-card:hover .p-media img{transform:scale(1.05);}
.p-badge{position:absolute;top:10px;left:10px;}
.p-fav{
  position:absolute;top:10px;right:10px;z-index:2;
  width:32px;height:32px;border-radius:50%;border:none;background:var(--white);
  color:var(--green-800);display:flex;align-items:center;justify-content:center;font-size:14px;
  box-shadow:0 4px 10px rgba(18,51,40,.15);transition:.15s var(--ease);
}
.p-fav:hover{transform:scale(1.08);}
.p-fav.active{color:var(--orange-600);}
.p-fav.active svg{fill:var(--orange-600);stroke:var(--orange-600);}
.quick-add{
  position:absolute;bottom:10px;right:10px;z-index:2;
  width:40px;height:40px;border-radius:50%;border:none;
  background:var(--orange-600);color:#fff;font-size:15px;
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 8px 18px -8px rgba(232,114,12,.7);
  transition:background .15s var(--ease),transform .15s var(--ease);
}
.quick-add:hover{background:var(--orange-500);transform:scale(1.07);}
.quick-add.added{background:var(--success);}
.p-body{padding:16px;display:flex;flex-direction:column;gap:7px;flex:1;}
.p-cat{
  font-family:var(--font-mono);font-size:10.5px;font-weight:600;letter-spacing:.06em;
  text-transform:uppercase;color:var(--green-600);
}
.p-name{
  font-family:var(--font-display);font-size:15.5px;font-weight:600;color:var(--green-900);
  line-height:1.3;cursor:pointer;display:block;
}
.p-name:hover{color:var(--orange-600);}
.p-rating{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;color:var(--ink-soft);}
.p-rating svg,.p-rating i{color:var(--orange-500);}
.p-rating span{color:var(--ink-faint);}
.p-price-row{display:flex;align-items:baseline;gap:8px;flex-wrap:wrap;margin-top:2px;}
.p-price{font-family:var(--font-mono);font-weight:700;font-size:16.5px;color:var(--green-800);}
.p-price-old{
  font-family:var(--font-mono);font-size:12px;color:var(--ink-faint);
  text-decoration:line-through;
}
.p-unit{font-size:11.5px;color:var(--ink-faint);}
.p-actions{display:flex;gap:8px;margin-top:auto;padding-top:12px;}
.p-actions .btn{flex:1;padding:10px 12px;font-size:13px;}
.p-stock{font-size:11.5px;font-weight:700;display:inline-flex;align-items:center;gap:6px;}
.p-stock .led{width:6px;height:6px;border-radius:50%;background:currentColor;}
.p-stock.in{color:var(--green-600);}
.p-stock.low{color:var(--orange-600);}
.p-stock.out{color:var(--red);}

/* ---------- PAGINATION ---------- */
.pagination{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:36px;flex-wrap:wrap;}
.pg-btn{
  min-width:42px;height:42px;padding:0 12px;border-radius:12px;
  display:inline-flex;align-items:center;justify-content:center;
  background:var(--white);border:1.5px solid var(--line);
  font-family:var(--font-mono);font-weight:600;font-size:14px;color:var(--ink);
  transition:.15s var(--ease);
}
.pg-btn:hover{border-color:var(--green-700);color:var(--green-700);transform:translateY(-1px);}
.pg-current{background:var(--green-700);border-color:var(--green-700);color:#fff;}
.pg-disabled{opacity:.4;pointer-events:none;}
.pg-ellipsis{color:var(--ink-soft);padding:0 4px;font-family:var(--font-mono);}

/* ---------- PRODUCT PAGE ---------- */
.pd-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:38px;align-items:start;}
.pd-gallery{position:sticky;top:calc(var(--header-h) + 18px);}
.pd-main{
  position:relative;background:var(--green-050);border:1px solid var(--line);
  border-radius:var(--radius-xl);overflow:hidden;
  height:420px;display:flex;align-items:center;justify-content:center;cursor:zoom-in;
}
.pd-main img{width:100%;height:100%;object-fit:contain;}
.pd-thumbs{display:flex;gap:10px;margin-top:12px;flex-wrap:wrap;}
.pd-thumb{
  width:70px;height:70px;border-radius:12px;overflow:hidden;
  border:2px solid var(--line);background:var(--paper);padding:0;flex-shrink:0;
}
.pd-thumb img{width:100%;height:100%;object-fit:cover;}
.pd-thumb.active{border-color:var(--green-600);}
.pd-card{
  background:var(--paper);border:1px solid var(--line);
  border-radius:var(--radius-xl);padding:26px;
}
.pd-info{display:flex;flex-direction:column;gap:12px;}
.pd-cat{
  display:inline-flex;align-items:center;gap:8px;
  font-family:var(--font-mono);font-size:11.5px;font-weight:600;
  letter-spacing:.08em;text-transform:uppercase;color:var(--green-600);
}
.pd-cat .dot{width:4px;height:4px;border-radius:50%;background:var(--orange-500);}
.pd-title{font-size:clamp(24px,3vw,32px);margin:0;}
.pd-save{
  font-family:var(--font-mono);font-size:12px;font-weight:700;
  background:var(--orange-100);color:var(--orange-600);
  padding:4px 9px;border-radius:999px;
}
.pd-desc{font-size:14.5px;color:var(--ink-soft);line-height:1.7;}
.pd-meta-list{display:flex;flex-direction:column;gap:10px;padding-top:6px;}
.pd-meta-list li{display:flex;align-items:center;gap:10px;font-size:13.5px;color:var(--ink-soft);}
.pd-meta-list li b{color:var(--green-900);font-weight:700;}
.stock-pill{
  display:inline-flex;align-items:center;gap:7px;
  font-size:12.5px;font-weight:700;padding:4px 11px;border-radius:999px;
}
.stock-pill .led{width:7px;height:7px;border-radius:50%;background:currentColor;}
.stock-pill.in{background:var(--green-100);color:var(--green-700);}
.stock-pill.low{background:var(--orange-100);color:var(--orange-600);}
.stock-pill.out{background:var(--red-dim);color:var(--red);}
.pd-buy{display:flex;flex-direction:column;gap:12px;padding-top:14px;}
.pd-buy .qty-stepper{height:46px;border-radius:14px;}
.pd-buy .qty-stepper button{width:44px;height:44px;font-size:18px;}
.pd-buy .qty-stepper span{min-width:44px;font-size:15px;}
.trust-bullets{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:6px;}
.trust-bullet{
  display:flex;align-items:center;gap:10px;font-size:13px;font-weight:600;color:var(--green-800);
}
.trust-bullet svg,.trust-bullet i{color:var(--orange-500);flex-shrink:0;}

/* lightbox */
.lightbox{
  position:fixed;inset:0;z-index:160;background:rgba(10,20,16,.92);
  display:none;align-items:center;justify-content:center;padding:24px;
}
.lightbox.open{display:flex;}
.lightbox img{max-width:92vw;max-height:82vh;object-fit:contain;border-radius:12px;}
.lb-close{
  position:absolute;top:18px;right:18px;
  width:42px;height:42px;border-radius:50%;border:none;background:rgba(255,255,255,.14);
  color:#fff;font-size:17px;display:flex;align-items:center;justify-content:center;
}
.lb-arrow{
  position:absolute;top:50%;transform:translateY(-50%);
  width:46px;height:46px;border-radius:50%;border:none;background:rgba(255,255,255,.14);
  color:#fff;font-size:17px;display:flex;align-items:center;justify-content:center;
}
.lb-arrow.prev{left:16px;} .lb-arrow.next{right:16px;}
.lb-counter{
  position:absolute;bottom:22px;left:50%;transform:translateX(-50%);
  font-family:var(--font-mono);font-size:12.5px;color:#cfd8d3;
}
.rel-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}

/* ---------- PRODUCT STICKY BAR ---------- */
.pd-sticky{
  position:fixed;left:0;right:0;bottom:0;z-index:80;
  background:var(--paper);border-top:1px solid var(--line);
  padding:10px 16px calc(10px + env(safe-area-inset-bottom));
  display:none;align-items:center;gap:12px;
  box-shadow:0 -10px 30px -18px rgba(18,51,40,.5);
}
body.with-pd-sticky{padding-bottom:84px;}
@supports (selector(:has(*))){
  body:has(.mobile-cart-bar.visible){padding-bottom:88px;}
}

/* ---------- CHECKOUT ---------- */
.checkout-grid{display:grid;grid-template-columns:1.25fr .75fr;gap:28px;align-items:start;}
.checkout-bottom-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.steps{display:flex;align-items:center;gap:8px;margin-bottom:24px;flex-wrap:wrap;}
.step{display:flex;align-items:center;gap:9px;list-style:none;}
.st-ic{
  width:34px;height:34px;border-radius:50%;background:var(--green-100);color:var(--green-700);
  display:inline-flex;align-items:center;justify-content:center;
  font-family:var(--font-mono);font-size:13px;font-weight:700;flex-shrink:0;
}
.st-label{font-size:13.5px;font-weight:700;color:var(--ink-soft);}
.step.active .st-ic{background:var(--orange-600);color:#fff;}
.step.active .st-label{color:var(--green-900);}
.step-sep{width:26px;height:1.5px;background:var(--line);}
.location-box{
  background:var(--green-050);border:1px solid var(--line);
  border-radius:var(--radius-m);padding:16px;
}
.location-status{
  display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;
  color:var(--ink-soft);margin-bottom:10px;
}
.location-status.ok{color:var(--success);}
.location-status.err{color:var(--red);}
.location-coords{
  font-family:var(--font-mono);font-size:11.5px;color:var(--ink-faint);
  margin-top:8px;word-break:break-all;
}
.search-results{
  margin-top:10px;max-height:200px;overflow-y:auto;
  border:1px solid var(--line);border-radius:var(--radius-s);background:var(--white);
}
.search-result{
  display:block;width:100%;text-align:left;padding:10px 12px;
  border:none;border-bottom:1px solid var(--line);background:none;font-size:13px;
}
.search-result:last-child{border-bottom:none;}
.search-result:hover{background:var(--green-050);}
.summary-card{
  background:var(--paper);border:1px solid var(--line);
  border-radius:var(--radius);padding:20px;
  position:sticky;top:calc(var(--header-h) + 18px);
}
.mini-item{
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  padding:10px 0;border-bottom:1px solid var(--line);font-size:13.5px;
}
.mini-item:last-child{border-bottom:none;}
.mini-item .mi-name{color:var(--ink);}
.mini-item .mi-qty{color:var(--ink-faint);font-family:var(--font-mono);font-size:12px;}
.mini-item .mi-price{font-family:var(--font-mono);font-weight:700;color:var(--green-800);white-space:nowrap;}
.pay-note{
  display:flex;gap:9px;align-items:flex-start;
  background:var(--green-050);border-radius:var(--radius-m);
  padding:12px 14px;font-size:12.5px;color:var(--ink-soft);margin-top:16px;
}
.pay-sticky{
  position:sticky;bottom:0;margin-top:16px;
  background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);
  padding:14px;display:flex;align-items:center;gap:12px;justify-content:space-between;
  box-shadow:var(--shadow-card);
}
.pay-sticky .ps-total{font-family:var(--font-mono);font-weight:800;font-size:19px;color:var(--green-900);}
.empty-state{text-align:center;padding:60px 20px;}
.empty-state .es-ic{font-size:44px;margin-bottom:14px;opacity:.7;}
.empty-state h3{font-size:20px;margin-bottom:8px;}
.empty-state p{color:var(--ink-soft);font-size:14px;margin-bottom:20px;}

/* ---------- TRACKING ---------- */
.track-search{
  background:var(--paper);border:1px solid var(--line);
  border-radius:var(--radius);padding:22px;margin-bottom:28px;
}
.order-hero{
  background:var(--green-900);color:#fff;border-radius:var(--radius-xl);
  padding:26px;margin-bottom:22px;
}
.order-hero h2{color:#fff;font-size:24px;}
.order-hero .oh-sub{color:#c7d6cd;font-size:13.5px;margin-top:6px;}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:18px 0 0;}
.stat{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.16);border-radius:var(--radius-m);padding:12px 14px;}
.stat span{
  display:block;font-family:var(--font-mono);font-size:10.5px;letter-spacing:.1em;
  text-transform:uppercase;color:#9fb6ab;
}
.stat b{display:block;font-size:14.5px;margin-top:4px;color:#fff;word-break:break-word;}
.progress-track{height:8px;border-radius:999px;background:rgba(255,255,255,.14);overflow:hidden;margin-top:20px;}
.progress-fill{height:100%;background:var(--orange-500);border-radius:999px;transition:width .5s var(--ease);}
.h3-title{font-family:var(--font-display);font-size:19px;margin-bottom:14px;}
.timeline{position:relative;padding-left:6px;}
.tl-item{position:relative;padding:0 0 22px 34px;}
.tl-item:last-child{padding-bottom:0;}
.tl-item::before{
  content:"";position:absolute;left:11px;top:22px;bottom:-2px;width:2px;background:var(--line);
}
.tl-item:last-child::before{display:none;}
.tl-dot{
  position:absolute;left:0;top:2px;width:24px;height:24px;border-radius:50%;
  background:var(--paper);border:2px solid var(--line);
  display:flex;align-items:center;justify-content:center;
  font-size:10px;color:var(--ink-faint);
}
.tl-item.done .tl-dot{background:var(--green-600);border-color:var(--green-600);color:#fff;}
.tl-item.current .tl-dot{border-color:var(--orange-500);color:var(--orange-600);box-shadow:0 0 0 4px var(--orange-100);}
.tl-item b{display:block;font-size:14px;color:var(--green-900);}
.tl-item span{font-size:12.5px;color:var(--ink-faint);}
.tl-item.todo b{color:var(--ink-faint);}
.order-items{display:flex;flex-direction:column;}
.oi-row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:11px 0;border-bottom:1px solid var(--line);}
.oi-row:last-child{border-bottom:none;}
.oi-name{font-size:14px;color:var(--ink);}
.oi-qty{font-family:var(--font-mono);font-size:12px;color:var(--ink-faint);}
.oi-price{font-family:var(--font-mono);font-weight:700;color:var(--green-800);white-space:nowrap;}
.alert-card{
  display:flex;gap:12px;align-items:flex-start;
  background:var(--red-dim);border:1px solid #f0c9c4;border-radius:var(--radius);
  padding:16px;color:var(--red);font-size:14px;
}
.alert-card.ok{background:var(--green-100);border-color:#c9e0d4;color:var(--green-700);}
.alert-card.warn{background:var(--orange-100);border-color:#f6d7b4;color:var(--orange-600);}

/* ---------- FOOTER ---------- */
footer{background:var(--green-900);color:#cfe0d7;padding:56px 0 24px;}
.footer-grid{display:grid;grid-template-columns:1.5fr 1fr 1fr 1.2fr;gap:32px;margin-bottom:34px;}
.footer-logo{display:flex;align-items:center;gap:10px;font-family:var(--font-display);font-weight:700;font-size:20px;color:#fff;}
.footer-brand p{font-size:13.5px;color:#9fb6ab;margin-top:12px;max-width:290px;}
footer h4{
  font-family:var(--font-body);font-size:12.5px;font-weight:800;
  letter-spacing:.1em;text-transform:uppercase;color:#7fa392;margin-bottom:14px;
}
footer ul{display:flex;flex-direction:column;gap:10px;font-size:14px;}
footer ul a,.footer-contact-item{color:#d7e5dd;display:inline-flex;align-items:center;gap:9px;}
footer ul a:hover{color:#fff;}
.footer-contact-item{margin-bottom:11px;font-size:14px;}
.footer-social{display:flex;gap:10px;margin-top:16px;}
.footer-social a{
  width:36px;height:36px;border-radius:50%;
  background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.18);
  display:inline-flex;align-items:center;justify-content:center;color:#d7e5dd;font-size:14px;
  transition:.18s var(--ease);
}
.footer-social a:hover{background:var(--orange-600);border-color:var(--orange-600);color:#fff;}
.footer-pay{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;}
.footer-pay span{
  background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);
  padding:6px 10px;border-radius:8px;font-size:11px;font-weight:700;
}
.footer-bottom{
  border-top:1px solid rgba(255,255,255,.12);padding-top:20px;
  display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;
  font-size:12.5px;color:#7fa392;
}

/* ---------- REVEAL ---------- */
.reveal{opacity:0;transform:translateY(16px);transition:opacity .5s var(--ease),transform .5s var(--ease);}
.reveal.in{opacity:1;transform:none;}

/* ==========================================================================
   RESPONSIVE — mobile first corrections for every breakpoint
   ========================================================================== */
@media (max-width:1200px){
  .product-grid,.rel-grid{grid-template-columns:repeat(3,1fr);}
  .footer-grid{grid-template-columns:1.4fr 1fr 1fr;gap:28px;}
}
@media (max-width:1024px){
  .search-bar{max-width:230px;}
  .pd-grid{grid-template-columns:1fr;gap:26px;}
  .pd-gallery{position:static;}
  .pd-main{height:340px;}
  .checkout-grid{grid-template-columns:1fr;}
  .summary-card{position:static;}
  .stats{grid-template-columns:repeat(2,1fr);}
  .checkout-bottom-grid{grid-template-columns:1fr;}
}
@media (max-width:900px){
  :root{--header-h:64px;}
  .hide-on-desktop{display:none!important;}
  .hide-on-mobile{display:none!important;}
  .hamburger{display:inline-flex;}
  .nav-strip{display:none;}
  .search-bar{display:none;}
  .mobile-search{display:block;}
  .trust-grid{grid-template-columns:repeat(2,1fr);}
  .product-grid,.rel-grid{grid-template-columns:repeat(2,1fr);}
  .form-grid{grid-template-columns:1fr;}
  .footer-grid{grid-template-columns:1fr 1fr;}
  .topbar .wrap{justify-content:center;}
  #topbarPhone{display:none;}
}
@media (max-width:768px){
  .section{padding:48px 0;}
  .pd-sticky{display:flex;}
  .trust-strip{padding-bottom:48px;}
  .option-grid{grid-template-columns:1fr;}
}
@media (max-width:640px){
  .wrap{padding:0 16px;}
  .header-inner{gap:10px;}
  .logo{font-size:17px;}
  .logo-mark{width:30px;height:30px;font-size:13px;}
  .logo-sub{font-size:8.5px;}
  .icon-btn{width:38px;height:38px;}
  .product-grid,.rel-grid{gap:14px;}
  .p-media{height:140px;}
  .p-body{padding:13px;}
  .p-price{font-size:15px;}
  .p-actions{flex-direction:column;gap:7px;}
  .trust-grid{grid-template-columns:1fr;padding:18px;}
  .footer-grid{grid-template-columns:1fr;gap:26px;}
  .stats{grid-template-columns:repeat(2,1fr);}
  .modal-box{padding:20px;border-radius:var(--radius-l);}
  .cart-drawer{width:100vw;}
  .toast{left:16px;right:16px;bottom:16px;transform:translateY(140%);max-width:none;}
  .toast.show{transform:translateY(0);}
  .pd-main{height:260px;}
  .pd-card{padding:20px;}
  .trust-bullets{grid-template-columns:1fr;}
  .footer-bottom{flex-direction:column;text-align:center;}
}
@media (max-width:480px){
  .product-grid,.rel-grid{grid-template-columns:1fr;}
  .p-media{height:190px;}
  .p-actions{flex-direction:row;}
  .cat-row{flex-wrap:nowrap;overflow-x:auto;padding-bottom:6px;margin-left:-16px;margin-right:-16px;padding-left:16px;padding-right:16px;}
  .cat-chip{flex-shrink:0;}
  .stats{grid-template-columns:1fr;}
  .search-bar{padding:7px 7px 7px 14px;}
  .btn{padding:12px 18px;}
  .pg-btn{min-width:38px;height:38px;}
  .order-hero{padding:20px;}
  .trust-row{flex-direction:column;align-items:flex-start;gap:10px;}
}
@media (max-width:380px){
  .product-grid{grid-template-columns:1fr;}
  .icon-btn{width:36px;height:36px;}
  .cart-row img{width:48px;height:48px;}
}
@media (max-width:360px){
  .wrap{padding:0 12px;}
  .logo-sub{display:none;}
  .p-actions{flex-direction:column;}
}

/* pointer-friendly touch targets on coarse pointers */
@media (pointer:coarse){
  .qty-stepper button{width:38px;height:38px;}
  .p-fav{width:36px;height:36px;}
  .quick-add{width:44px;height:44px;}
  .icon-btn{width:44px;height:44px;}
}

@media (prefers-reduced-motion:reduce){
  *,*::before,*::after{
    animation-duration:.001ms!important;animation-iteration-count:1!important;
    transition-duration:.001ms!important;scroll-behavior:auto!important;
  }
  .reveal{opacity:1;transform:none;}
}

@media print{
  .site-header,.topbar,.nav-strip,.mobile-cart-bar,.bottom-nav,.pd-sticky,
  .page-loader,.toast,.scrim,.cart-drawer,.modal-backdrop,.footer-social{display:none!important;}
  body{background:#fff;}
}
</style>
