@extends('layouts.app')
@section('content')
<div class="p-4 max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold">Stock Verification – Blind Count</h1>
  <p class="text-sm text-gray-500">Session {{ $session->session_number }} | Status: {{ $session->status }} | Never shows system quantity or variance.</p>
  @if(session('success'))<div class="bg-green-100 text-green-800 p-3 rounded mt-3">{{ session('success') }}</div>@endif
  @if($session->status==='submitted')<div class="bg-blue-100 text-blue-800 p-3 rounded mt-3">Verification Submitted Successfully – Management will review variance secretly.</div>
  @else
  <form method="POST" action="{{ route('stock-verification.auditor-submit',$session) }}" class="card p-4 mt-4 rounded-xl">
    @csrf
    <table class="data-table">
      <thead><tr><th>Product</th><th>Physical Count</th></tr></thead>
      <tbody>
        @foreach($session->items as $it)
        <tr>
          <td>{{ $it->product->name }}</td>
          <td><input type="number" name="items[{{ $loop->index }}][physical_quantity]" value="{{ $it->physical_quantity }}" min="0" required class="form-input input-field" placeholder="Enter quantity">
              <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $it->id }}">
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <button class="mt-4 px-6 py-2 bg-primary-600 text-white rounded-lg w-full">Submit Verification</button>
    <p class="text-xs text-gray-500 mt-2">You will see only "Verification Submitted Successfully" after submit. Variance is hidden.</p>
  </form>
  @endif
</div>
@endsection
