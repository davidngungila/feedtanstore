<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Dompdf\Dompdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller {
    public function index() {
        $sales = Sale::with(['customer', 'user'])->orderBy('created_at', 'desc')->get();
        return view('sales.receipts', compact('sales'));
    }

    public function show(Sale $sale) {
        $sale->load(['customer', 'user', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    public function download(Sale $sale) {
        $sale->load(['customer', 'user', 'items.product']);
        
        // Generate PNG QR code for better PDF compatibility
        $qrCode = \QrCode::size(100)->format('png')->generate(route('sales.show', $sale));
        // Encode PNG to base64
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCode);

        // Instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $html = view('sales.receipt-pdf', compact('sale', 'qrCodeBase64'))->render();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper([0, 0, 283.46, 800], 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        return $dompdf->stream('receipt-' . $sale->invoice_number . '.pdf');
    }

    public function print(Sale $sale) {
        $sale->load(['customer', 'user', 'items.product']);
        
        // Generate PNG QR code for print view and encode to base64
        $qrCode = \QrCode::size(100)->format('png')->generate(route('sales.show', $sale));
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCode);
        
        return view('sales.receipt-print', compact('sale', 'qrCodeBase64'));
    }
}
