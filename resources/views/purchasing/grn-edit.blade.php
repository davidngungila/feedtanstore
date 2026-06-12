@extends('layouts.app')

@section('page-title', 'Edit Goods Received Note')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
  <div class="card rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-primary-900">Edit Goods Received Note</h2>
      <a href="{{ route('purchasing.grn') }}" class="text-primary-600 hover:text-primary-800 font-medium">
        <i class="fa-solid fa-arrow-left mr-2"></i>Back to GRNs
      </a>
    </div>

    @if($errors->any())
      <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
        <ul>
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('purchasing.grn.update', $grn) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
          <select name="supplier_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Select Supplier</option>
            @foreach($suppliers as $supplier)
              <option value="{{ $supplier->id }}" {{ old('supplier_id', $grn->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Received Date *</label>
          <input type="date" name="received_date" value="{{ old('received_date', $grn->received_date) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
          <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $grn->notes) }}</textarea>
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <a href="{{ route('purchasing.grn') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
          Cancel
        </a>
        <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
          Update GRN
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
