@extends('layouts.app')

@section('page-title', 'Warehouses')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Warehouses</h2>
            <a href="{{ route('store.locations.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Warehouse
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="card rounded-2xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-primary-900 mb-4">Warehouse Map</h3>
            <div id="warehousesMap" class="w-full h-[400px]"></div>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Name</th>
                        <th class="text-left">Address</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $location)
                    <tr>
                        <td class="font-medium text-primary-900">{{ $location->name }}</td>
                        <td class="text-gray-600">{{ $location->address ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $location->is_active ? 'badge-green' : 'badge-gray' }}">
                                {{ $location->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('store.locations.edit', $location) }}" class="text-primary-600 hover:text-primary-800 p-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('store.locations.destroy', $location) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this warehouse?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-500">No warehouses found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Default center: Arusha, Tanzania
    const centerLat = -3.3869;
    const centerLng = 36.6883;

    const map = L.map('warehousesMap').setView([centerLat, centerLng], 10);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    @foreach($locations as $location)
        @if($location->latitude && $location->longitude)
            const marker_{{ $location->id }} = L.marker([{{ $location->latitude }}, {{ $location->longitude }}])
                .addTo(map)
                .bindPopup(`
                    <div class="p-2 min-w-[200px]">
                        <h4 class="font-bold text-base text-gray-900 mb-1">{{ $location->name }}</h4>
                        <p class="text-sm text-gray-700 mb-1"><strong>Address:</strong> {{ $location->address ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700 mb-2"><strong>Status:</strong> {{ $location->is_active ? 'Active' : 'Inactive' }}</p>
                        <a href="{{ route('store.locations.edit', $location) }}" class="text-primary-600 hover:underline text-sm">Edit Warehouse</a>
                    </div>
                `);
        @endif
    @endforeach
</script>
@endsection