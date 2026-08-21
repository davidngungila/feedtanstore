@extends('layouts.app')

@section('page-title', $sale->invoice_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-800 rounded-xl">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif
    
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-xl font-bold text-primary-900">{{ $sale->invoice_number }}</h2>
                @if($sale->tra_status == 'posted')
                    <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>Posted to TRA
                    </span>
                @else
                    <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-1"></i>Not posted to TRA
                    </span>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('sales.receipts.download', $sale) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center whitespace-nowrap">
                    <i class="fas fa-download mr-2"></i>PDF
                </a>
                <a href="{{ route('sales.receipts.print', $sale) }}" target="_blank" class="px-4 py-2 border border-gray-300 rounded-lg flex items-center whitespace-nowrap">
                    <i class="fas fa-print mr-2"></i>Invoice
                </a>
                <button onclick="printEfdReceipt({{ $sale->id }})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center whitespace-nowrap">
                    <i class="fas fa-receipt mr-2"></i>EFD Receipt
                </button>
                @if($sale->tra_status != 'posted')
                <button onclick="postSaleToTra({{ $sale->id }})" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center whitespace-nowrap" id="postTraBtn">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Post to TRA
                </button>
                @endif
                <a href="{{ route('sales.receipts') }}" class="px-4 py-2 border border-gray-300 rounded-lg flex items-center whitespace-nowrap">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Customer</p>
                <p class="font-medium">{{ $sale->customer->name ?? 'Walk-in Customer' }}</p>
                @if($sale->customer && $sale->customer->tin_number)
                    <p class="text-xs text-gray-400">TIN: {{ $sale->customer->tin_number }}</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Date</p>
                <p class="font-medium">{{ $sale->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Cashier</p>
                <p class="font-medium">{{ $sale->user->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="badge {{ $sale->status == 'completed' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($sale->status) }}</span>
            </div>
            @if($sale->tra_receipt_number)
            <div>
                <p class="text-sm text-gray-500 mb-1">TRA Receipt #</p>
                <p class="font-medium">{{ $sale->tra_receipt_number ?: $sale->invoice_number }}</p>
            </div>
            @endif
            @if($sale->tra_verification_link)
            <div>
                <p class="text-sm text-gray-500 mb-1">TRA Verification</p>
                <a href="{{ $sale->tra_verification_link }}" target="_blank" class="font-medium text-blue-600 hover:text-blue-800 text-sm break-all">
                    {{ $sale->tra_verification_link }}
                </a>
            </div>
            @endif
            @if($sale->discount_id && $sale->discountApplied)
            <div>
                <p class="text-sm text-gray-500 mb-1">Discount Applied</p>
                <p class="font-medium text-primary-600">{{ $sale->discountApplied->name }}</p>
            </div>
            @endif
        </div>

        <div class="border-t pt-4 mb-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Product</th>
                            <th class="text-left py-2">Tax Code</th>
                            <th class="text-right py-2">Qty</th>
                            <th class="text-right py-2">Price</th>
                            <th class="text-right py-2">VAT</th>
                            <th class="text-right py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $taxPercent = 18; @endphp
                        @foreach($sale->items as $item)
                        @php
                            $taxCode = (int) ($item->product->tax_code ?? 1);
                            $itemAmt = round((float) $item->total, 2);
                            $itemVat = $taxCode == 1 ? round($itemAmt * $taxPercent / (100 + $taxPercent), 2) : 0;
                        @endphp
                        <tr class="border-b border-gray-100">
                            <td class="py-3">
                                {{ $item->product->name ?? 'Product Not Found' }}
                            </td>
                            <td class="py-3">
                                <span class="text-xs px-1.5 py-0.5 rounded {{ $taxCode == 1 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $taxCode == 1 ? '18%' : ($taxCode == 3 ? '0% ZR' : ($taxCode == 4 ? 'SR' : 'Exempt')) }}
                                </span>
                            </td>
                            <td class="py-3 text-right">{{ $item->quantity }}</td>
                            <td class="py-3 text-right">TZS {{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3 text-right text-gray-500">{{ $itemVat > 0 ? number_format($itemVat, 2) : '-' }}</td>
                            <td class="py-3 text-right font-medium">TZS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col items-end gap-2 mb-6">
            <div class="flex justify-between w-72">
                <span class="text-gray-600">Subtotal:</span>
                <span>TZS {{ number_format($sale->subtotal, 2) }}</span>
            </div>
            @php
                $totalVat = 0;
                foreach ($sale->items as $item) {
                    $tc = (int) ($item->product->tax_code ?? 1);
                    if ($tc == 1) {
                        $totalVat += round((float) $item->total * 18 / 118, 2);
                    }
                }
            @endphp
            @if($totalVat > 0)
            <div class="flex justify-between w-72">
                <span class="text-gray-600">VAT (18%):</span>
                <span>TZS {{ number_format($totalVat, 2) }}</span>
            </div>
            @endif
            @if($sale->discount > 0)
            <div class="flex justify-between w-72 text-red-600">
                <span>Discount:</span>
                <span>-TZS {{ number_format($sale->discount, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between w-72 text-lg font-bold border-t pt-2">
                <span>Total:</span>
                <span>TZS {{ number_format($sale->total, 2) }}</span>
            </div>
            <div class="flex justify-between w-72">
                <span class="text-gray-600">Paid:</span>
                <span>TZS {{ number_format($sale->paid, 2) }}</span>
            </div>
            <div class="flex justify-between w-72">
                <span class="text-gray-600">Change:</span>
                <span>TZS {{ number_format($sale->change, 2) }}</span>
            </div>
        </div>

        @if($sale->notes)
        <div class="border-t pt-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Notes</h3>
            <p class="text-gray-600 whitespace-pre-wrap">{{ $sale->notes }}</p>
        </div>
        @endif
        
        @if($sale->cancellation_reason)
        <div class="border-t pt-4 mt-4">
            <h3 class="text-sm font-semibold text-red-700 mb-2">Cancellation Reason</h3>
            <p class="text-gray-600 whitespace-pre-wrap">{{ $sale->cancellation_reason }}</p>
        </div>
        @endif
    </div>
</div>

<script>
function postSaleToTra(saleId) {
    // First confirmation dialog
    const firstConfirmed = confirm('Are you sure you want to post this sale to TRA?');
    if (!firstConfirmed) return;

    // Second confirmation dialog
    const secondConfirmed = confirm('Are you REALLY sure you want to post this sale to TRA? This action cannot be undone.');
    if (!secondConfirmed) return;
    
    const btn = document.getElementById('postTraBtn');
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Posting...';
        btn.disabled = true;
    }
    
    fetch('/sales/receipts/post-to-tra', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ sale_id: saleId })
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Sale posted to TRA successfully!');
            location.reload();
        } else {
            alert('TRA posting failed: ' + (result.error || 'Unknown error'));
            if (btn) {
                btn.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i>Post to TRA';
                btn.disabled = false;
            }
        }
    })
    .catch(e => {
        alert('Error: ' + e.message);
        if (btn) {
            btn.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i>Post to TRA';
            btn.disabled = false;
        }
    });
}

function printEfdReceipt(saleId) {
    // Ask for confirmation before posting to TRA
    if (!confirm('Are you sure you want to post this sale to TRA and print the EFD receipt?')) {
        return; // User clicked Cancel - do nothing
    }
    
    // Find the EFD Receipt button and show loading state
    const btn = event?.target?.closest('button') || document.querySelector('button[onclick*="printEfdReceipt"]');
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Posting...';
        btn.disabled = true;
    }
    
    postSaleToTraAndPrint(saleId, btn);
}

async function postSaleToTraAndPrint(saleId) {
    try {
        const response = await fetch('/sales/receipts/post-to-tra', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ sale_id: saleId })
        });
        const result = await response.json();
        if (!result.success) {
            alert('TRA posting failed: ' + (result.error || 'Unknown error') + '\n\nPlease check TRA VFD settings.');
            return;
        }
        
        // Even if duplicate (already posted), print the EFD receipt
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        iframe.src = '/sales/receipts/' + saleId + '/efd-print';
        document.body.appendChild(iframe);
        
        iframe.onload = function() {
            setTimeout(() => {
                iframe.contentWindow.print();
                setTimeout(() => {
                    document.body.removeChild(iframe);
                }, 500);
            }, 500);
        };
    } catch (e) {
        alert('Error: ' + e.message);
    }
}
</script>
@endsection
