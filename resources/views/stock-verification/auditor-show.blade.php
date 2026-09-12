@extends('layouts.app')
@section('content')
<div class="p-4 max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold">Stock Verification – Blind Count</h1>
  <p class="text-sm text-gray-500">Session {{ $session->session_number }} | Status: {{ $session->status }} | All {{ isset($allProducts) ? $allProducts->count() : $session->items->count() }} active products listed – enter only physical count. System quantity / variance are hidden.</p>
  <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-800"><i class="fas fa-eye-slash mr-1"></i>You must independently count physical stock. Do not see expected quantities. Leave empty → 0. Showing <strong>ALL existing products</strong> without numbers.</div>
  @if(session('success'))<div class="bg-green-100 text-green-800 p-3 rounded mt-3">{{ session('success') }}</div>@endif
  @if($session->status==='submitted')<div class="bg-blue-100 text-blue-800 p-3 rounded mt-3">Verification Submitted Successfully – Management will review variance secretly.</div>
  @else
  <form method="POST" action="{{ route('stock-verification.auditor-submit',$session) }}" class="card p-4 mt-4 rounded-xl">
    @csrf
    <table class="data-table">
      <thead><tr><th style="width:60%">Product</th><th>Physical Count <span class="text-xs font-normal">(enter actual count)</span></th></tr></thead>
      <tbody>
        @if(isset($allProducts) && isset($itemsMap))
          @foreach($allProducts as $product)
            @php $it = $itemsMap->get($product->id); @endphp
            <tr>
              <td class="font-medium">{{ $product->name }} @if($product->sku)<span class="text-xs text-gray-400">({{ $product->sku }})</span>@endif @if($product->barcode)<span class="text-xs text-gray-300">{{ $product->barcode }}</span>@endif</td>
              <td>
                @if($it)
                  <input type="number" name="items[{{ $loop->index }}][physical_quantity]" value="{{ $it->physical_quantity }}" min="0" required class="form-input input-field text-center font-mono" placeholder="— — —">
                  <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $it->id }}">
                @else
                  <span class="text-xs text-red-500">No verification item – contact manager</span>
                @endif
              </td>
            </tr>
          @endforeach
        @else
          @foreach($session->items as $it)
          <tr>
            <td class="font-medium">{{ $it->product->name ?? 'Unknown product' }} @if(optional($it->product)->sku)<span class="text-xs text-gray-400">({{ $it->product->sku }})</span>@endif</td>
            <td><input type="number" name="items[{{ $loop->index }}][physical_quantity]" value="{{ $it->physical_quantity }}" min="0" required class="form-input input-field text-center font-mono" placeholder="— — —">
                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $it->id }}">
            </td>
          </tr>
          @endforeach
        @endif
      </tbody>
    </table>
    <p class="text-xs text-gray-400 mt-1">All existing products in system are listed above without quantities. Auditor enters only physical count.</p>
    <button class="mt-4 px-6 py-2 bg-primary-600 text-white rounded-lg w-full">Submit Verification</button>
    <p class="text-xs text-gray-500 mt-2">You will see only "Verification Submitted Successfully" after submit. Variance is hidden.</p>
  </form>
  @endif
</div>
@endsection
