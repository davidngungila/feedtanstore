@extends('layouts.app')

@section('page-title', 'Goods Received Notes')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
  <div class="card rounded-2xl p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
      <h2 class="text-xl font-bold text-primary-900">Goods Received Notes</h2>
      <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
        <form action="{{ route('purchasing.grn') }}" method="GET" id="grnSearchForm" class="w-full md:w-72">
          <div class="relative">
            <input
              type="text"
              name="search"
              id="grnSearch"
              value="{{ $search ?? '' }}"
              placeholder="Search GRN..."
              class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              autocomplete="off"
            >
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
        <a href="{{ route('purchasing.grn.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
          <i class="fa-solid fa-plus mr-2"></i>New GRN
        </a>
      </div>
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
        <tbody id="grn-table-body">
          @forelse($grns as $grn)
          <tr data-search="{{ strtolower($grn->grn_number . ' ' . ($grn->supplier->name ?? '') . ' ' . ($grn->purchaseOrder->po_number ?? '') . ' ' . ($grn->status ?? '') . ' ' . ($grn->received_date ?? '') . ' ' . $grn->total) }}">
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
          @empty
          <tr>
            <td colspan="5" class="text-center text-gray-500 py-8">No goods received notes found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <script>
      const grnSearch = document.getElementById('grnSearch');
      const grnRows = document.querySelectorAll('#grn-table-body tr');
      let grnSearchTimer = null;

      if (grnSearch) {
        grnSearch.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase().trim();

          grnRows.forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            row.style.display = searchData.includes(searchTerm) ? '' : 'none';
          });

          clearTimeout(grnSearchTimer);
          grnSearchTimer = setTimeout(() => {
            document.getElementById('grnSearchForm').submit();
          }, 350);
        });
      }
    </script>
  </div>
</div>
@endsection
