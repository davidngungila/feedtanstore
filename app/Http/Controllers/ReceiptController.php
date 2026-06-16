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

    public function show($id) {
        $sale = Sale::withTrashed()->findOrFail($id);
        $sale->load(['customer', 'user', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    public function verify($id) {
        $sale = Sale::withTrashed()->findOrFail($id);
        $sale->load(['customer', 'user', 'items.product']);
        $isVerified = $sale->status === 'completed' && !$sale->trashed();
        return view('sales.verify', compact('sale', 'isVerified'));
    }

    public function download($id) {
        $sale = Sale::withTrashed()->findOrFail($id);
        $sale->load(['customer', 'user', 'items.product']);
        
        // Generate PNG QR code linking to verify page
        $qrCodePng = \QrCode::size(100)->format('png')->generate(route('sales.receipts.verify', $sale));
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodePng);

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

    public function print($id) {
        $sale = Sale::withTrashed()->findOrFail($id);
        $sale->load(['customer', 'user', 'items.product']);
        
        // Generate PNG QR code linking to verify page
        $qrCodePng = \QrCode::size(100)->format('png')->generate(route('sales.receipts.verify', $sale));
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodePng);
        
        return view('sales.receipt-print', compact('sale', 'qrCodeBase64'));
    }
}
