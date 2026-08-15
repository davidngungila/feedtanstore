<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <div class="footer-logo"><span class="logo-mark" style="background:var(--gold);color:var(--green-900);"><i class="fa-solid fa-leaf"></i></span> Feedtan Store</div>
        <p style="font-size:13.5px;line-height:1.7;max-width:280px;">{{ __('Quality products, unbeatable prices, delivery to your door — or ready when you step in.') }}</p>
        <div class="footer-social">
          <a href="#" class="icon-btn" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="icon-btn" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" class="icon-btn" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>
      <div>
        <h4>{{ __('Buy') }}</h4>
        <ul>
          @foreach($categories as $cat)
            @if ($loop->index < 5)
              <li><a href="{{ route('shop.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
            @endif
          @endforeach
        </ul>
      </div>
      <div>
        <h4>{{ __('Support') }}</h4>
        <ul>
          <li><a href="{{ route('shop.tracking') }}">{{ __('Track my order link') }}</a></li>
          <li><a href="#" onclick="showToast('{{ __('Contact us phone') }}','phone')">{{ __('Contact us') }}</a></li>
          <li><a href="#" onclick="showToast('{{ __('Return policy') }}','info')">{{ __('Return policy') }}</a></li>
          <li><a href="#" onclick="showToast('{{ __('Delivery info') }}','info')">{{ __('Delivery info') }}</a></li>
        </ul>
      </div>
      <div>
        <h4>{{ __('Visit our store') }}</h4>
        <ul>
          <li><i class="fa-solid fa-location-dot" style="margin-right:6px;"></i>{{ __('Location') }}</li>
          <li><i class="fa-regular fa-clock" style="margin-right:6px;"></i>{{ __('Opening hours') }}</li>
          <li><i class="fa-solid fa-phone" style="margin-right:6px;"></i>+255 717 358 865</li>
          <li><i class="fa-regular fa-envelope" style="margin-right:6px;"></i>info@feedtanstore.com</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© {{ date('Y') }} Feedtan Store. Haki zote zimehifadhiwa.</span>
      <span>Imeundwa kwa usikivu kwa wanunuzi wa kila siku.</span>
    </div>
  </div>
</footer>
