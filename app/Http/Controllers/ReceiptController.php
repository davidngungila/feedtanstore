<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\StoreSetting;
use App\Services\TraVfdService;
use Dompdf\Dompdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller {
    public function index(Request $request) {
        $search = trim((string) $request->input('search'));

        $sales = Sale::with(['customer', 'user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->only('search'));

        return view('sales.receipts', compact('sales', 'search'));
    }

    public function show($key) {
        $sale = Sale::findByAnyKeyOrFail($key, true);
        $sale->load(['customer', 'user', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    public function verify($key) {
        $sale = Sale::findByAnyKeyOrFail($key, true);
        $sale->load(['customer', 'user', 'items.product']);
        $isVerified = $sale->status === 'completed' && !$sale->trashed();
        return view('sales.verify', compact('sale', 'isVerified'));
    }

    public function download($key) {
        $sale = Sale::findByAnyKeyOrFail($key, true);
        $sale->load(['customer', 'user', 'items.product']);
        
        // Generate SVG QR code (no imagick needed)
        $qrCodeSvg = \QrCode::size(100)->generate(route('sales.receipts.verify', $sale));
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

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

    public function print($key) {
        $sale = Sale::findByAnyKeyOrFail($key, true);
        $sale->load(['customer', 'user', 'items.product']);
        
        // Generate SVG QR code (no imagick needed)
        $qrCodeSvg = \QrCode::size(100)->generate(route('sales.receipts.verify', $sale));
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
        
        return view('sales.receipt-print', compact('sale', 'qrCodeBase64'));
    }

    /**
     * Print EFD receipt for a sale (EFD is the default receipt).
     * Only NEW sales are auto-submitted to TRA at creation time;
     * previously saved sales are never auto-posted here - use Post to TRA manually.
     */
    public function efdPrint($key) {
        $sale = Sale::findByAnyKeyOrFail($key, true);
        $sale->load(['customer', 'user', 'items.product']);

        $settings = StoreSetting::first();

        // Store is NOT VAT registered - VAT is always 0
        $vatAmount = 0;

        $verificationLink = $sale->tra_verification_link ?? '';
        $qrCode = $sale->tra_qr_code ?? '';

        if (!empty($verificationLink) && empty($qrCode)) {
            $qrCodeSvg = \QrCode::size(120)->generate($verificationLink);
            $qrCode = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
        }

        return view('sales.efd-receipt-print', compact('sale', 'settings', 'vatAmount', 'verificationLink', 'qrCode'));
    }

    /**
     * Post receipt to TRA and return result.
     * Idempotent: a sale is only ever posted to TRA once.
     */
    public function postToTra(Request $request) {
        $saleId = $request->input('sale_id');
        $sale = Sale::findByAnyKeyOrFail($saleId, true);
        $sale->load(['customer', 'user', 'items.product']);

        $traService = new TraVfdService();

        return response()->json($traService->postReceipt($sale));
    }

    /**
     * Generate XML preview for a sale (for debugging)
     */
    public function traXmlPreview($key) {
        $sale = Sale::findByAnyKeyOrFail($key, true);
        $sale->load(['customer', 'user', 'items.product']);

        $traService = new TraVfdService();
        $xml = $traService->buildXml($sale);

        return response($xml)->header('Content-Type', 'text/xml');
    }
}
