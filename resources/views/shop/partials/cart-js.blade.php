<script>
let cart = [];
const DELIVERY_FEE = 3000;
const FREE_DELIVERY_THRESHOLD = 50000;

function normalizeCart(raw) {
  if (Array.isArray(raw)) {
    return raw.filter(i => i && Number(i.quantity) > 0);
  }
  if (raw && typeof raw === 'object') {
    return Object.entries(raw).map(([id, quantity]) => {
      return { id: String(id), name: 'Item', price: 0, quantity: Number(quantity) || 0 };
    }).filter(i => i.quantity > 0);
  }
  return [];
}

function findProductCard(id) {
  return document.querySelector('.p-card[data-id="'+id+'"], .pd-card[data-id="'+id+'"]');
}

function productMetaFromDOM(id) {
  const card = findProductCard(id);
  if (!card) return null;
  const name = card.querySelector('.p-name')?.textContent?.trim() || 'Item';
  const priceEl = card.querySelector('.p-price');
  const price = priceEl ? parseFloat(priceEl.getAttribute('data-price') ?? priceEl.textContent.replace(/[^0-9]/g, '')) || 0 : 0;
  const img = card.querySelector('.p-media img')?.src || null;
  return { name, price, img };
}

function initCart() {
  const saved = localStorage.getItem('shopCart');
  if (saved) {
    try {
      cart = normalizeCart(JSON.parse(saved));
    } catch(e) { cart = []; }
  }
  updateCartUI();
}

function addToCart(id, name, price) {
  const meta = productMetaFromDOM(id);
  if (name === 'Item' && meta) name = meta.name;
  if (!price && meta) price = meta.price;
  const existing = cart.find(i => String(i.id) === String(id));
  if (existing) {
    existing.quantity += 1;
    existing.name = name;
    existing.price = Number(price) || existing.price || 0;
  } else {
    cart.push({ id: String(id), name, price: Number(price) || 0, quantity: 1 });
  }
  saveCart();
  updateCartUI();
  animateCartButton(id);
  showToast(name + ' ' + '{{ __('added to cart') }}', 'cart');
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

function saveCart() { localStorage.setItem('shopCart', JSON.stringify(cart)); }
function cartCount() { return cart.reduce((sum, item) => sum + (Number(item.quantity) || 0), 0); }
function cartSubtotal() { return cart.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 0), 0); }
function formatTZS(n) { return 'TZS ' + (Math.round(n) || 0).toLocaleString(); }

function updateCartUI() {
  const count = cartCount();
  const badge = document.getElementById('cartBadge');
  if (badge) {
    badge.style.display = count > 0 ? 'flex' : 'none';
    badge.textContent = count;
  }
  const bnBadge = document.getElementById('bnCartBadge');
  if (bnBadge) {
    bnBadge.style.display = count > 0 ? 'flex' : 'none';
    bnBadge.textContent = count;
  }
  renderCartList();
  updateBottomBar();
}

function renderCartList() {
  const list = document.getElementById('cartList');
  const foot = document.getElementById('cartFoot');
  if (!list) return;
  if (cart.length === 0) {
    list.innerHTML = '<div class="cart-empty">' +
      '<i class="fa-solid fa-cart-shopping"></i>' +
      '<b>{{ __('Your cart is empty') }}</b>' +
      '<span>{{ __('Explore the menu and add something nice.') }}</span>' +
      '<button class="btn btn-primary btn-sm" onclick="closeCart()">{{ __('Start shopping') }}</button>' +
      '</div>';
    if (foot) foot.style.display = 'none';
    return;
  }
  if (foot) foot.style.display = 'block';
  list.innerHTML = cart.map(item => {
    const meta = productMetaFromDOM(item.id);
    const img = item.img || meta?.img || 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=500&q=80';
    const esc = (s) => String(s).replace(/'/g, "\\'").replace(/"/g, '&quot;');
    return '<div class="cart-row">' +
      '<img src="'+img+'" alt="'+esc(item.name)+'" loading="lazy">' +
      '<div class="cart-row-info">' +
        '<b>'+esc(item.name)+'</b>' +
        '<span class="cr-meta">{{ __('per item') }} · '+formatTZS(item.price)+' {{ __('each') }}</span>' +
        '<div class="cart-row-bottom">' +
          '<div class="qty-stepper">' +
            '<button onclick="changeQty(\''+item.id+'\', -1, \''+esc(item.name)+'\', '+item.price+')" aria-label="{{ __('Decrease quantity') }}">−</button>' +
            '<span>'+item.quantity+'</span>' +
            '<button onclick="changeQty(\''+item.id+'\', 1, \''+esc(item.name)+'\', '+item.price+')" aria-label="{{ __('Increase quantity') }}">+</button>' +
          '</div>' +
          '<span class="cr-price">'+formatTZS(item.price*item.quantity)+'</span>' +
        '</div>' +
        '<button class="cr-remove" onclick="removeFromCart(\''+item.id+'\')">{{ __('Remove') }}</button>' +
      '</div>' +
    '</div>';
  }).join('');
  const subtotal = cartSubtotal();
  document.getElementById('cartSubtotal').textContent = formatTZS(subtotal);
  document.getElementById('cartTotal').textContent = formatTZS(subtotal);
  document.getElementById('cartDeliveryEst').textContent = subtotal >= FREE_DELIVERY_THRESHOLD ? '{{ __('Free (order qualifies)') }}' : formatTZS(DELIVERY_FEE) + ' {{ __('if delivered') }}';
  updateFreeDelivery(subtotal);
}

function updateFreeDelivery(subtotal) {
  const fill = document.getElementById('fdbFill');
  const text = document.getElementById('fdbText');
  if (!fill || !text) return;
  if (subtotal >= FREE_DELIVERY_THRESHOLD) {
    fill.style.width = '100%';
    text.className = 'fdb-text done';
    text.innerHTML = '<i class="fa-solid fa-gift" style="margin-right:5px;"></i><b>{{ __('You have unlocked FREE delivery!') }}</b>';
  } else {
    const pct = Math.min(100, (subtotal / FREE_DELIVERY_THRESHOLD) * 100);
    fill.style.width = pct + '%';
    text.className = 'fdb-text';
    text.innerHTML = '{{ __('Add') }} <b>'+formatTZS(FREE_DELIVERY_THRESHOLD - subtotal)+'</b> {{ __('more to get FREE delivery.') }}';
  }
}

function updateBottomBar() {
  const bar = document.getElementById('mobileCartBar');
  if (!bar) return;
  const count = cartCount();
  const subtotal = cartSubtotal();
  const badge = document.getElementById('mcbBadge');
  const total = document.getElementById('mcbTotal');
  const sub = document.getElementById('mcbSub');
  if (badge) badge.textContent = count;
  if (total) total.textContent = formatTZS(subtotal);
  if (sub) sub.textContent = subtotal >= FREE_DELIVERY_THRESHOLD
    ? '{{ __('Free (order qualifies)') }}'
    : count + ' {{ __('items') }} · ' + formatTZS(DELIVERY_FEE) + ' {{ __('if delivered') }}';
  bar.classList.toggle('visible', count > 0);
  bar.setAttribute('aria-hidden', count > 0 ? 'false' : 'true');
}

function animateCartButton(id) {
  const card = findProductCard(id);
  const btn = card?.querySelector('.quick-add, [data-add-btn]');
  if (!btn) return;
  const orig = btn.innerHTML;
  btn.classList.add('added');
  btn.setAttribute('aria-label', 'Added to cart');
  setTimeout(() => { btn.classList.remove('added'); btn.innerHTML = orig; }, 1200);
}

function openCart() {
  document.getElementById('cartDrawer').classList.add('open');
  document.getElementById('scrim').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeCart() {
  document.getElementById('cartDrawer').classList.remove('open');
  document.getElementById('scrim').classList.remove('open');
  document.getElementById('mobileMenu')?.classList.remove('open');
  document.getElementById('menuToggle')?.setAttribute('aria-expanded', 'false');
  document.body.style.overflow = '';
}

function toggleMenu() {
  const menu = document.getElementById('mobileMenu');
  const scrim = document.getElementById('scrim');
  const open = menu.classList.toggle('open');
  document.getElementById('menuToggle').setAttribute('aria-expanded', open ? 'true' : 'false');
  scrim.classList.toggle('open', open);
  document.body.style.overflow = open ? 'hidden' : '';
}

function closeMenu() {
  document.getElementById('mobileMenu')?.classList.remove('open');
  document.getElementById('menuToggle')?.setAttribute('aria-expanded', 'false');
  document.getElementById('scrim')?.classList.remove('open');
  document.body.style.overflow = '';
}

function closeAllOverlays() {
  closeCart();
  closeMenu();
}

function toggleFav(btn) {
  btn.classList.toggle('active');
  showToast(btn.classList.contains('active') ? '{{ __('Added to wishlist') }}' : '{{ __('Removed from wishlist') }}', 'heart');
}

function showToast(msg, icon) {
  const toast = document.getElementById('toast');
  const icons = {
    heart:'<i class="fa-solid fa-heart"></i>',
    info:'<i class="fa-solid fa-circle-info"></i>',
    phone:'<i class="fa-solid fa-phone"></i>',
    cart:'<i class="fa-solid fa-cart-plus"></i>',
    check:'<i class="fa-solid fa-circle-check"></i>',
    warning:'<i class="fa-solid fa-triangle-exclamation"></i>'
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
  if (loader) loader.classList.add('hidden');
}

function applyTheme(theme) {
  document.documentElement.classList.toggle('dark', theme === 'dark');
  localStorage.setItem('ftTheme', theme);
  const icon = document.getElementById('themeIcon');
  if (icon) {
    icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
  }
}

function toggleTheme() {
  const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
  applyTheme(next);
}

(function initShopChrome() {
  const savedTheme = localStorage.getItem('ftTheme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  applyTheme(savedTheme);
  const header = document.getElementById('siteHeader');
  const onScroll = () => {
    if (header) header.classList.toggle('scrolled', window.scrollY > 4);
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  const revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach((el, i) => {
      el.style.transitionDelay = Math.min(i % 8 * 40, 280) + 'ms';
      io.observe(el);
    });
  } else {
    revealEls.forEach(el => el.classList.add('in'));
  }
})();
</script>
