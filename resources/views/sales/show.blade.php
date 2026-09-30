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
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-800 rounded-xl">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
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
                @if($sale->status == 'completed' && ($sale->tra_status ?? null) !== 'posted')
                <a href="{{ route('sales.edit', $sale) }}" class="px-3 py-1.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 flex items-center whitespace-nowrap text-xs">
                    <i class="fas fa-edit mr-1.5"></i>Edit Sale
                </a>
                @endif
                <a href="{{ route('sales.receipts.download', $sale) }}" class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 flex items-center whitespace-nowrap text-xs" title="Legacy PDF (non-fiscal)">
                    <i class="fas fa-download mr-1.5"></i>PDF
                </a>
                <button onclick="printEfdReceipt('{{ $sale->encrypted_key }}')" class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center whitespace-nowrap text-xs">
                    <i class="fas fa-receipt mr-1.5"></i>EFD Receipt (Default)
                </button>
                <a href="{{ route('sales.receipts') }}" class="px-3 py-1.5 border border-gray-300 rounded-lg flex items-center whitespace-nowrap text-xs">
                    <i class="fas fa-arrow-left mr-1.5"></i>Back
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
            <div>
                <p class="text-sm text-gray-500 mb-1">Payment Method</p>
                <p class="font-medium">{{ ucwords(str_replace('_', ' ', $sale->payment_method ?? '')) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Paid</p>
                <p class="font-medium">TZS {{ number_format($sale->paid, 2) }}</p>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// EFD is the default receipt: every sale is submitted to TRA automatically (backend + efd-print fallback).
// Manual "Post to TRA" button removed; EFD print ensures submission then prints without extra confirmation.
function postSaleToTra(saleId) {
    return postSaleToTraAndPrint(saleId, false);
}

function printEfdReceipt(saleId) {
    postSaleToTraAndPrint(saleId, true);
}

async function postSaleToTraAndPrint(saleId, shouldPrint = true) {
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
            console.warn('TRA post did not succeed, opening EFD anyway (efd-print will retry):', result.error);
            if (window.Swal) {
                Swal.fire({
                    title: 'TRA pending',
                    text: (result.error || 'Could not confirm TRA posting.') + ' Opening EFD receipt anyway.',
                    icon: 'warning',
                    confirmButtonColor: '#16a34a'
                });
            }
        }

        if (!shouldPrint) {
            location.reload();
            return result;
        }

        // Even if duplicate (already posted) or pending, open the EFD receipt (default)
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        iframe.src = '/sales/receipts/' + encodeURIComponent(saleId) + '/efd-print';
        document.body.appendChild(iframe);

        iframe.onload = function() {
            setTimeout(() => {
                iframe.contentWindow.print();
                setTimeout(() => {
                    document.body.removeChild(iframe);
                    location.reload();
                }, 500);
            }, 500);
        };
        return result;
    } catch (e) {
        // Use SweetAlert for error if available
        if (window.Swal) {
            Swal.fire({
                title: 'Error',
                text: e.message,
                icon: 'error',
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'OK'
            });
        } else {
            alert('Error: ' + e.message);
        }
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
