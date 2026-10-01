<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo">
          <img class="logo-full" src="{{ asset('feedtanstorelogo.png') }}" alt="Feedtan Store">
        </div>
        <p>{{ __('Quality products, unbeatable prices, delivery to your door — or ready when you step in.') }}</p>
        <div class="footer-pay">
          <span>M-Pesa</span><span>Airtel Money</span><span>Mixx</span><span>HaloPesa</span>
        </div>
        <div class="footer-social">
          <a href="#" class="icon-btn" style="width:36px;height:36px;" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="icon-btn" style="width:36px;height:36px;" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" class="icon-btn" style="width:36px;height:36px;" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>

      <div>
        <h4>{{ __('Buy') }}</h4>
        <ul>
          <li><a href="{{ route('shop.index') }}#shop">{{ __('All products') }}</a></li>
          @foreach($categories->take(5) as $cat)
            <li><a href="{{ route('shop.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
          @endforeach
        </ul>
      </div>

      <div>
        <h4>{{ __('Support') }}</h4>
        <ul>
          <li><a href="{{ route('shop.tracking') }}">{{ __('Track my order link') }}</a></li>
        </ul>
      </div>

      <div>
        <h4>{{ __('Visit our store') }}</h4>
        <div class="footer-contact-item">
          <i class="fa-solid fa-location-dot" style="color:var(--orange-400);"></i>
          {{ $settings->store_address ?? __('Location') }}
        </div>
        <div class="footer-contact-item">
          <i class="fa-regular fa-clock" style="color:var(--orange-400);"></i>
          {{ __('Opening hours') }}
        </div>
        <div class="footer-contact-item">
          <i class="fa-solid fa-phone" style="color:var(--orange-400);"></i>
          +255 717 358 865
        </div>
        <div class="footer-contact-item">
          <i class="fa-regular fa-envelope" style="color:var(--orange-400);"></i>
          {{ $settings->store_email ?? 'info@feedtanstore.com' }}
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <span>© {{ date('Y') }} Feedtan Store. {{ __('All rights reserved.') }}</span>
      <span>Made by Feedtan ICT team</span>
    </div>
  </div>
</footer>
