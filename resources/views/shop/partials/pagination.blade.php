@php
  $encryptedPageUrl = function ($page) use ($paginator) {
    $parts = parse_url($paginator->url($page));
    parse_str($parts['query'] ?? '', $params);
    $params['page'] = \App\Http\Controllers\OnlineOrderController::encryptShopPage((int) $page);
    return ($parts['path'] ?? '') . '?' . http_build_query($params) . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
  };
@endphp
@if ($paginator->hasPages())
<nav class="pagination" role="navigation" aria-label="Pagination">
  {{-- Previous --}}
  @if ($paginator->onFirstPage())
    <span class="pg-btn pg-disabled" aria-disabled="true">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 18-6-6 6-6"/></svg>
    </span>
  @else
    <a href="{{ $encryptedPageUrl($paginator->currentPage() - 1) }}" class="pg-btn" rel="prev" aria-label="Previous">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m15 18-6-6 6-6"/></svg>
    </a>
  @endif

  {{-- Elements --}}
  @foreach ($elements as $element)
    @if (is_string($element))
      <span class="pg-ellipsis" aria-hidden="true">{{ $element }}</span>
    @elseif (is_array($element))
      @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
          <span class="pg-btn pg-current" aria-current="page">{{ $page }}</span>
        @else
          <a href="{{ $encryptedPageUrl($page) }}" class="pg-btn" aria-label="Page {{ $page }}">{{ $page }}</a>
        @endif
      @endforeach
    @endif
  @endforeach

  {{-- Next --}}
  @if ($paginator->hasMorePages())
    <a href="{{ $encryptedPageUrl($paginator->currentPage() + 1) }}" class="pg-btn" rel="next" aria-label="Next">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 18 6-6-6-6"/></svg>
    </a>
  @else
    <span class="pg-btn pg-disabled" aria-disabled="true">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m9 18 6-6-6-6"/></svg>
    </span>
  @endif
</nav>
@endif
