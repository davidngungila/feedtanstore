@extends('layouts.app')

@section('page-title', 'Return ' . $return->return_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Return {{ $return->return_number }}</h2>
            <div class="flex gap-3">
                <button onclick="downloadPDF()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </button>
                <a href="{{ route('sales.returns') }}" class="px-4 py-2 border border-gray-300 rounded-lg">Back to Returns</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Invoice #</p>
                <p class="font-medium">
                    <a href="{{ route('sales.show', $return->sale) }}" class="text-primary-600 hover:text-primary-800">
                        {{ $return->sale->invoice_number }}
                    </a>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Return Date</p>
                <p class="font-medium">{{ $return->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Processed By</p>
                <p class="font-medium">{{ $return->user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Total</p>
                <p class="font-medium text-red-600">TZS {{ number_format($return->total, 2) }}</p>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-1">Reason for Return</p>
            <p class="p-4 bg-gray-50 rounded-lg">{{ $return->reason }}</p>
        </div>

        <h3 class="text-lg font-semibold text-primary-900 mb-3">Returned Items</h3>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Product</th>
                        <th class="text-left">Price</th>
                        <th class="text-left">Quantity</th>
                        <th class="text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($return->items as $item)
                    <tr>
                        <td class="font-medium">
                            {{ $item->saleItem->product->name ?? 'Product' }}
                        </td>
                        <td>TZS {{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>TZS {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- jsPDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>

<script>
function downloadPDF() {
    console.log('Download PDF clicked');
    try {
        var doc = new jsPDF();
        
        // Header
        doc.setFontSize(24);
        doc.setFontType('bold');
        doc.text('RETURN RECEIPT', 105, 25, 'center');
        
        // Receipt Body
        var y = 40;
        
        // Business Info
        doc.setFontSize(16);
        doc.setFontType('bold');
        doc.text('FEEDTAN STORE', 105, y, 'center');
        
        y += 7;
        doc.setFontSize(11);
        doc.setFontType('normal');
        doc.text('Your Trusted Store | Quality Products', 105, y, 'center');
        
        y += 7;
        doc.text('www.feedtanstore.com', 105, y, 'center');
        
        y += 15;
        
        // Return Details
        doc.setFontSize(14);
        doc.setFontType('bold');
        doc.text('Return Details', 15, y);
        
        y += 8;
        doc.setFontSize(12);
        doc.setFontType('bold');
        doc.text('Return Number:', 15, y);
        doc.setFontType('normal');
        doc.text('{{ $return->return_number }}', 65, y);
        
        y += 7;
        doc.setFontType('bold');
        doc.text('Date:', 15, y);
        doc.setFontType('normal');
        doc.text('{{ $return->created_at->format("F d, Y H:i:s") }}', 65, y);
        
        y += 7;
        doc.setFontType('bold');
        doc.text('Invoice:', 15, y);
        doc.setFontType('normal');
        doc.text('{{ $return->sale->invoice_number }}', 65, y);
        
        y += 7;
        doc.setFontType('bold');
        doc.text('Processed By:', 15, y);
        doc.setFontType('normal');
        var processedBy = '{{ addslashes($return->user->name ?? "N/A") }}';
        doc.text(processedBy, 65, y);
        
        y += 15;
        
        // Reason Section
        doc.setFontSize(14);
        doc.setFontType('bold');
        doc.text('Reason for Return', 15, y);
        
        y += 8;
        doc.setFontSize(12);
        doc.setFontType('normal');
        var reason = '{{ addslashes($return->reason) }}';
        var splitReason = doc.splitTextToSize(reason, 180);
        doc.text(splitReason, 15, y);
        y += (splitReason.length * 6) + 5;
        
        // Items Table
        doc.setFontSize(14);
        doc.setFontType('bold');
        doc.text('Returned Items', 15, y);
        
        y += 8;
        doc.setFontSize(12);
        doc.text('Product', 15, y);
        doc.text('Qty', 130, y);
        doc.text('Unit Price', 150, y);
        doc.text('Total', 195, y, 'right');
        
        y += 7;
        
        doc.setFontType('normal');
        var total = 0;
        
        @if($return->items->count() > 0)
            @foreach($return->items as $item)
                var productName = '{{ addslashes($item->saleItem->product->name ?? 'Product') }}';
                var price = parseFloat({{ $item->unit_price }});
                var qty = {{ $item->quantity }};
                var itemTotal = parseFloat({{ $item->total }});
                
                var splitProduct = doc.splitTextToSize(productName, 100);
                var lineHeight = 6;
                for (var i = 0; i < splitProduct.length; i++) {
                    doc.text(splitProduct[i], 15, y);
                    if (i === 0) {
                        doc.text(qty.toString(), 130, y);
                        doc.text('TZS ' + price.toFixed(2), 150, y);
                        doc.text('TZS ' + itemTotal.toFixed(2), 195, y, 'right');
                    }
                    y += lineHeight;
                }
                if (splitProduct.length === 1) {
                    y += 2;
                } else {
                    y += 1;
                }
                total += itemTotal;
            @endforeach
        @else
            doc.text('No items returned', 15, y);
            y += 7;
        @endif
        
        // Total Section
        y += 10;
        doc.setFontSize(16);
        doc.setFontType('bold');
        doc.text('TOTAL AMOUNT:', 15, y);
        doc.text('TZS ' + total.toFixed(2), 195, y, 'right');
        
        y += 20;
        
        // Thank You Note
        doc.setFontSize(14);
        doc.setFontType('italic');
        doc.text('Thank you for shopping with us!', 105, y, 'center');
        
        // Footer
        y += 15;
        doc.setFontSize(10);
        doc.setFontType('normal');
        doc.text('This is an official receipt from Feedtan Store', 105, y, 'center');
        y += 5;
        doc.text('Powered by Feedtan Store', 105, y, 'center');
        
        doc.save('return-{{ $return->return_number }}.pdf');
        console.log('PDF saved');
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Error generating PDF: ' + error.message);
    }
}
</script>
@endsection
