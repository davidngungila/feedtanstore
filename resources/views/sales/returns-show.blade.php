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
        var doc = new jsPDF('p', 'mm', 'a4');
        
        // Colors
        var primaryColor = [22, 163, 74]; // Green-600
        var darkColor = [31, 41, 55]; // Gray-800
        var lightColor = [156, 163, 175]; // Gray-400
        var bgColor = [248, 250, 252]; // Gray-50
        
        // Header - Full width green banner
        doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
        doc.rect(0, 0, 210, 40, 'F');
        
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(24);
        doc.setFontType('bold');
        doc.text('RETURN RECEIPT', 105, 25, 'center');
        
        // Receipt Body
        var y = 55;
        
        // Business Info
        doc.setFillColor(bgColor[0], bgColor[1], bgColor[2]);
        doc.rect(15, y-10, 180, 40, 'F');
        
        doc.setTextColor(darkColor[0], darkColor[1], darkColor[2]);
        doc.setFontSize(18);
        doc.setFontType('bold');
        doc.text('FEEDTAN STORE', 105, y+5, 'center');
        
        doc.setFontSize(12);
        doc.setFontType('normal');
        doc.setTextColor(lightColor[0], lightColor[1], lightColor[2]);
        doc.text('Your Trusted Store | Quality Products', 105, y+15, 'center');
        doc.text('www.feedtanstore.com', 105, y+22, 'center');
        
        y += 45;
        
        // Return Details Card
        doc.setFillColor(255, 255, 255);
        doc.rect(15, y-5, 180, 75, 'F');
        doc.setDrawColor(229, 231, 235);
        doc.setLineWidth(0.5);
        doc.rect(15, y-5, 180, 75, 'D');
        
        doc.setTextColor(darkColor[0], darkColor[1], darkColor[2]);
        doc.setFontSize(16);
        doc.setFontType('bold');
        doc.text('Return Details', 25, y+10);
        
        doc.setFontSize(12);
        doc.setFontType('bold');
        doc.text('Return Number:', 25, y+25);
        doc.setFontType('normal');
        doc.text('{{ $return->return_number }}', 80, y+25);
        
        doc.setFontType('bold');
        doc.text('Date:', 25, y+35);
        doc.setFontType('normal');
        doc.text('{{ $return->created_at->format("F d, Y H:i:s") }}', 80, y+35);
        
        doc.setFontType('bold');
        doc.text('Invoice:', 25, y+45);
        doc.setFontType('normal');
        doc.text('{{ $return->sale->invoice_number }}', 80, y+45);
        
        doc.setFontType('bold');
        doc.text('Processed By:', 25, y+55);
        doc.setFontType('normal');
        var processedBy = '{{ addslashes($return->user->name ?? "N/A") }}';
        doc.text(processedBy, 80, y+55);
        
        y += 85;
        
        // Reason Section
        doc.setFillColor(255, 255, 255);
        doc.rect(15, y-5, 180, 50, 'F');
        doc.rect(15, y-5, 180, 50, 'D');
        
        doc.setTextColor(darkColor[0], darkColor[1], darkColor[2]);
        doc.setFontSize(16);
        doc.setFontType('bold');
        doc.text('Reason for Return', 25, y+10);
        
        doc.setFontSize(12);
        doc.setFontType('normal');
        var reason = '{{ addslashes($return->reason) }}';
        var splitReason = doc.splitTextToSize(reason, 160);
        doc.text(splitReason, 25, y+23);
        
        y += 60;
        
        // Items Table
        doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
        doc.rect(15, y-5, 180, 12, 'F');
        
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(12);
        doc.setFontType('bold');
        doc.text('Product', 25, y+3);
        doc.text('Qty', 120, y+3);
        doc.text('Unit Price', 140, y+3);
        doc.text('Total', 185, y+3, 'right');
        
        y += 15;
        
        doc.setTextColor(darkColor[0], darkColor[1], darkColor[2]);
        doc.setFontType('normal');
        var total = 0;
        var row = 0;
        
        @if($return->items->count() > 0)
            @foreach($return->items as $item)
                if (row % 2 === 0) {
                    doc.setFillColor(248, 250, 252);
                    doc.rect(15, y-4, 180, 10, 'F');
                }
                
                var productName = '{{ addslashes($item->saleItem->product->name ?? 'Product') }}';
                var price = {{ $item->unit_price }};
                var qty = {{ $item->quantity }};
                var itemTotal = {{ $item->total }};
                
                var splitProduct = doc.splitTextToSize(productName, 90);
                var lineHeight = 6;
                for (var i = 0; i < splitProduct.length; i++) {
                    doc.text(splitProduct[i], 25, y);
                    if (i === 0) {
                        doc.text(qty.toString(), 120, y);
                        doc.text('TZS ' + price.toFixed(2), 140, y);
                        doc.text('TZS ' + itemTotal.toFixed(2), 185, y, 'right');
                    }
                    y += lineHeight;
                }
                if (splitProduct.length === 1) {
                    y += 2;
                } else {
                    y += 1;
                }
                row++;
                total += itemTotal;
            @endforeach
        @else
            doc.text('No items returned', 25, y);
            y += 10;
        @endif
        
        // Total Section
        y += 10;
        doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
        doc.rect(15, y-5, 180, 20, 'F');
        
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(18);
        doc.setFontType('bold');
        doc.text('TOTAL AMOUNT', 25, y+8);
        doc.text('TZS ' + total.toFixed(2), 185, y+8, 'right');
        
        y += 35;
        
        // Thank You Note
        doc.setTextColor(darkColor[0], darkColor[1], darkColor[2]);
        doc.setFontSize(14);
        doc.setFontType('italic');
        doc.text('Thank you for shopping with us!', 105, y, 'center');
        
        // Footer
        y += 20;
        doc.setFontSize(10);
        doc.setFontType('normal');
        doc.setTextColor(lightColor[0], lightColor[1], lightColor[2]);
        doc.text('This is an official receipt from Feedtan Store', 105, y, 'center');
        doc.text('Powered by Feedtan Store', 105, y+6, 'center');
        
        doc.save('return-{{ $return->return_number }}.pdf');
        console.log('PDF saved');
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Error generating PDF: ' + error.message);
    }
}
</script>
@endsection
