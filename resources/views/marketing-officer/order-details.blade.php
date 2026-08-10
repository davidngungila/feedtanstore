@extends('layouts.app')

@section('page-title', 'Order Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('marketing-officer.orders') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-900">{{ $order->order_number }}</h1>
                <p class="text-gray-600">Order #{{ $order->id }}</p>
            </div>
            <div>
                @if($order->status === 'pending')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                @elseif($order->status === 'confirmed')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Confirmed</span>
                @elseif($order->status === 'out_for_delivery')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">Out for Delivery</span>
                @elseif($order->status === 'delivered')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Delivered</span>
                @elseif($order->status === 'cancelled')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600">Customer Name</p>
                <p class="font-semibold">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Customer Phone</p>
                <p class="font-semibold">{{ $order->customer_phone }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Delivery Address</p>
                <p class="font-semibold">{{ $order->delivery_address }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Payment Method</p>
                <p class="font-semibold">{{ ucfirst($order->payment_method) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Payment Status</p>
                <p class="font-semibold">{{ ucfirst($order->payment_status) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Order Date</p>
                <p class="font-semibold">{{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
            @if($order->rider)
            <div>
                <p class="text-sm text-gray-600">Assigned Rider</p>
                <p class="font-semibold">{{ $order->rider->name }}</p>
            </div>
            @endif
        </div>

        @if($order->notes)
        <div class="mt-6">
            <p class="text-sm text-gray-600">Notes</p>
            <p class="text-gray-900">{{ $order->notes }}</p>
        </div>
        @endif
    </div>

    <div class="card rounded-2xl overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-900">Order Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Packaging</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->product ? $item->product->name : ($item->product_name ?? 'N/A') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($item->price, 0) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($item->total, 0) }}</td>
                        <td class="px-6 py-4">
                            @if($item->is_packaged)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    <i class="fas fa-check mr-1"></i>Packaged
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if(!$item->is_packaged)
                                <button onclick="openPackageModal({{ $item->id }}, '{{ $item->product ? $item->product->name : ($item->product_name ?? 'N/A') }}')" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg transition-colors">
                                    <i class="fas fa-box mr-1"></i>Package
                                </button>
                            @else
                                <span class="text-gray-400 text-sm">
                                    <i class="fas fa-check-circle mr-1"></i>Done
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-900">Subtotal</td>
                        <td class="px-6 py-3 text-sm font-semibold text-gray-900">TZS {{ number_format($order->subtotal, 0) }}</td>
                    </tr>
                    @if($order->discount > 0)
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-900">Discount</td>
                        <td class="px-6 py-3 text-sm font-semibold text-green-600">TZS {{ number_format($order->discount, 0) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-900">Delivery Fee</td>
                        <td class="px-6 py-3 text-sm font-semibold text-gray-900">TZS {{ number_format($order->delivery_fee, 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-900">Total</td>
                        <td class="px-6 py-3 text-sm font-bold text-primary-600">TZS {{ number_format($order->total, 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Packaging Status</h2>
        
        <div class="mb-4">
            <div class="flex items-center gap-3 mb-4">
                @if($order->packaging_status === 'pending')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                @elseif($order->packaging_status === 'in_progress')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">In Progress</span>
                @elseif($order->packaging_status === 'completed')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                @endif
            </div>
            
            <div class="text-sm text-gray-600">
                <p>Items packaged: {{ $order->items->where('is_packaged', true)->count() }} / {{ $order->items->count() }}</p>
            </div>
        </div>
        
        @if($order->packaging_status !== 'completed')
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                Scan each product barcode to verify and mark as packaged. Status will automatically update when all items are packaged.
            </p>
        </div>
        @endif
    </div>

    @if($order->packaging_status === 'completed')
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Order Reconciliation</h2>
        
        <div class="mb-4">
            <div class="flex items-center gap-3 mb-4">
                @if($order->reconciliation_status === 'pending')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                @elseif($order->reconciliation_status === 'completed')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                @endif
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <h3 class="font-semibold text-gray-900 mb-3">Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order Number:</span>
                        <span class="font-medium">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-medium">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Items:</span>
                        <span class="font-medium">{{ $order->items->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Packaged Items:</span>
                        <span class="font-medium text-green-600">{{ $order->items->where('is_packaged', true)->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-medium">TZS {{ number_format($order->total, 0) }}</span>
                    </div>
                </div>
            </div>
            
            @if($order->reconciliation_status === 'pending')
            <form action="{{ route('marketing-officer.complete-reconciliation', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white px-4 py-3 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-check-double mr-2"></i>Complete Reconciliation
                </button>
            </form>
            @else
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    Reconciliation completed. You can now assign a rider for delivery.
                </p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Order Processing</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Assign Rider -->
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-3">Assign Rider</h3>
                @if($order->packaging_status !== 'completed')
                    <div class="flex items-center gap-3 p-3 bg-yellow-50 rounded-lg">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-lock text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-yellow-800">Packaging Not Complete</p>
                            <p class="text-sm text-yellow-600">Complete packaging first</p>
                        </div>
                    </div>
                @elseif($order->reconciliation_status !== 'completed')
                    <div class="flex items-center gap-3 p-3 bg-yellow-50 rounded-lg">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-lock text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-yellow-800">Reconciliation Not Complete</p>
                            <p class="text-sm text-yellow-600">Complete reconciliation first</p>
                        </div>
                    </div>
                @elseif($order->rider)
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-motorcycle text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-green-800">{{ $order->rider->name }}</p>
                            <p class="text-sm text-green-600">Assigned</p>
                        </div>
                    </div>
                @else
                    <form action="{{ route('marketing-officer.assign-rider', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="rider_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 mb-3">
                            <option value="">Select Rider</option>
                            @foreach(\App\Models\DeliveryRider::where('is_active', true)->get() as $rider)
                                <option value="{{ $rider->id }}">{{ $rider->name }} - {{ $rider->vehicle_type }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>Assign Rider
                        </button>
                    </form>
                @endif
            </div>

            <!-- Packaging Instructions -->
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-3">Packaging Instructions</h3>
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-600">Process all paid orders</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-600">Package products at the shop</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-600">Mark packaging as complete</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-600">Assign rider for delivery</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Track Delivery -->
        @if($order->rider)
        <div class="mt-6">
            <a href="{{ route('marketing-officer.track-delivery', $order->id) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-sm font-medium transition-colors inline-flex items-center justify-center">
                <i class="fas fa-map-marker-alt mr-2"></i>Track Delivery
            </a>
        </div>
        @endif
    </div>

    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Update Order Status</h2>
        <form action="{{ route('marketing-officer.update-order-status', $order->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex gap-4">
                <select name="status" required class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Barcode Scanner Modal -->
<div id="packageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-primary-900">Scan Product Barcode</h3>
            <button onclick="closePackageModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-2">Product: <span id="modalProductName" class="font-semibold"></span></p>
            <p class="text-xs text-gray-500">Point camera at product barcode to verify and mark as packaged</p>
        </div>
        
        <div class="mb-4">
            <div id="reader" class="w-full" style="width: 100%;"></div>
            <div id="scannerStatus" class="mt-4 hidden">
                <div class="p-3 rounded-lg text-sm"></div>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closePackageModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Html5-Qrcode Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
let currentItemId = null;
let html5QrcodeScanner = null;

function openPackageModal(itemId, productName) {
    currentItemId = itemId;
    document.getElementById('modalProductName').textContent = productName;
    document.getElementById('scannerStatus').classList.add('hidden');
    document.getElementById('packageModal').classList.remove('hidden');
    
    // Initialize scanner
    startScanner();
}

function closePackageModal() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
            html5QrcodeScanner.clear();
            html5QrcodeScanner = null;
        }).catch(error => {
            console.error('Failed to stop scanner', error);
        });
    }
    document.getElementById('packageModal').classList.add('hidden');
    currentItemId = null;
}

function showScannerStatus(message, isSuccess) {
    const statusDiv = document.getElementById('scannerStatus');
    statusDiv.classList.remove('hidden');
    statusDiv.querySelector('div').textContent = message;
    statusDiv.querySelector('div').className = 'p-3 rounded-lg text-sm ' + (isSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
}

function startScanner() {
    html5QrcodeScanner = new Html5Qrcode("reader");
    
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };
    
    html5QrcodeScanner.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanFailure
    ).catch(err => {
        showScannerStatus('Camera access denied or not available', false);
        console.error('Error starting scanner', err);
    });
}

function onScanSuccess(decodedText, decodedResult) {
    // Play success sound
    playSuccessSound();
    
    // Stop scanning temporarily to prevent multiple scans
    if (html5QrcodeScanner) {
        html5QrcodeScanner.pause();
    }
    
    verifyAndPackage(decodedText);
}

function playSuccessSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        // Create two oscillators for a two-tone success sound
        const oscillator1 = audioContext.createOscillator();
        const oscillator2 = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator1.connect(gainNode);
        oscillator2.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        // First tone - higher pitch
        oscillator1.frequency.value = 880;
        oscillator1.type = 'sine';
        
        // Second tone - even higher pitch
        oscillator2.frequency.value = 1100;
        oscillator2.type = 'sine';
        
        // Volume envelope
        gainNode.gain.setValueAtTime(0, audioContext.currentTime);
        gainNode.gain.linearRampToValueAtTime(0.2, audioContext.currentTime + 0.05);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);
        
        // Timing for two-tone effect
        oscillator1.start(audioContext.currentTime);
        oscillator1.stop(audioContext.currentTime + 0.15);
        
        oscillator2.start(audioContext.currentTime + 0.08);
        oscillator2.stop(audioContext.currentTime + 0.15);
    } catch (error) {
        console.log('Audio play failed:', error);
    }
}

function playFailureSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 300;
        oscillator.type = 'sawtooth';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    } catch (error) {
        console.log('Audio play failed:', error);
    }
}

function onScanFailure(error) {
    // Handle scan failure silently
}

async function verifyAndPackage(barcode) {
    if (!barcode) {
        showScannerStatus('No barcode detected', false);
        if (html5QrcodeScanner) {
            html5QrcodeScanner.resume();
        }
        return;
    }
    
    const orderId = {{ $order->id }};
    
    try {
        const response = await fetch(`{{ route('marketing-officer.verify-package-item', [$order->id, 'ITEM_ID']) }}`.replace('ITEM_ID', currentItemId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ barcode: barcode })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showScannerStatus(data.message, true);
            
            if (data.all_packaged) {
                setTimeout(() => {
                    closePackageModal();
                    location.reload();
                }, 1500);
            } else {
                setTimeout(() => {
                    closePackageModal();
                    location.reload();
                }, 1000);
            }
        } else {
            playFailureSound();
            showScannerStatus(data.message, false);
            // Resume scanning after showing error
            setTimeout(() => {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.resume();
                }
            }, 2000);
        }
    } catch (error) {
        showScannerStatus('Error verifying product', false);
        if (html5QrcodeScanner) {
            html5QrcodeScanner.resume();
        }
    }
}
</script>
@endsection
