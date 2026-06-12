@extends('layouts.app')

@section('page-title', 'Goods Received Notes')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
  <div class="card rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-primary-900">Goods Received Notes</h2>
      <a href="{{ route('purchasing.grn.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
        <i class="fa-solid fa-plus mr-2"></i>New GRN
      </a>
    </div>

    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
        {{ session('success') }}
      </div>
    @endif

    <div class="overflow-x-auto">
      <table class="data-table w-full">
        <thead>
          <tr>
            <th class="text-left">GRN Number</th>
            <th class="text-left">Supplier</th>
            <th class="text-left">Received Date</th>
            <th class="text-left">Total</th>
            <th class="text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($grns as $grn)
          <tr>
            <td class="font-medium text-primary-900">
              <a href="{{ route('purchasing.grn.show', $grn) }}" class="hover:underline">{{ $grn->grn_number }}</a>
            </td>
            <td class="text-gray-600">{{ $grn->supplier->name ?? 'N/A' }}</td>
            <td class="text-gray-600">{{ $grn->received_date ? date('M d, Y', strtotime($grn->received_date)) : '-' }}</td>
            <td class="text-gray-600">TZS {{ number_format($grn->total, 2) }}</td>
            <td class="flex items-center gap-2">
              <a href="{{ route('purchasing.grn.show', $grn) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                <i class="fa-solid fa-eye"></i>
              </a>
              <a href="{{ route('purchasing.grn.edit', $grn) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                <i class="fa-solid fa-edit"></i>
              </a>
              <form action="{{ route('purchasing.grn.destroy', $grn) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this GRN?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
