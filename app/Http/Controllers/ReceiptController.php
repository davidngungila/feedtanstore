<?php

namespace App\Http\Controllers;

use App\Models\Sale;
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

    public function print($id) {
        $sale = Sale::withTrashed()->findOrFail($id);
        $sale->load(['customer', 'user', 'items.product']);
        
        // Generate SVG QR code (no imagick needed)
        $qrCodeSvg = \QrCode::size(100)->generate(route('sales.receipts.verify', $sale));
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
        
        return view('sales.receipt-print', compact('sale', 'qrCodeBase64'));
    }
}
