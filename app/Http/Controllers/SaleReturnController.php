<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;

class SaleReturnController extends Controller {
    public function index() {
        $returns = SaleReturn::with(['sale', 'user'])->orderBy('created_at', 'desc')->get();
        return view('sales.returns', compact('returns'));
    }

    public function show(SaleReturn $return) {
        $return->load(['sale', 'user', 'items.saleItem.product']);
        return view('sales.returns-show', compact('return'));
    }

    public function downloadPDF(SaleReturn $return) {
        $return->load(['sale', 'user', 'items.saleItem.product']);
        
        // Prepare data for the template
        $paymentData = [
            'orderReference' => $return->return_number,
            'createdAt' => $return->created_at,
            'customer_name' => $return->sale->customer->name ?? 'Anonymous',
            'id' => $return->id,
            'description' => 'Product Return',
            'channel' => 'Cash',
            'status' => 'SUCCESS',
            'collectedCurrency' => 'TZS',
            'collectedAmount' => $return->total,
            'amount' => $return->total,
            'currency' => 'TZS',
        ];
        
        $pdf = new Dompdf();
        $pdf->loadHtml(view('sales.returns-pdf', compact('return', 'paymentData'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        return $pdf->stream('return-' . $return->return_number . '.pdf');
    }

    public function create($saleId) {
        $sale = Sale::with('items.product')->findOrFail($saleId);
        return view('sales.returns', compact('sale'));
    }

    public function store(Request $request) {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $returnNumber = 'RET-' . date('YmdHis');
        $total = 0;

        $return = SaleReturn::create([
            'return_number' => $returnNumber,
            'sale_id' => $request->sale_id,
            'user_id' => Auth::id(),
            'total' => 0,
            'reason' => $request->reason
        ]);

        foreach ($request->items as $itemData) {
            $saleItem = \App\Models\SaleItem::find($itemData['sale_item_id']);
            $itemTotal = $itemData['quantity'] * $saleItem->unit_price;
            $total += $itemTotal;

            $return->items()->create([
                'sale_item_id' => $itemData['sale_item_id'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $saleItem->unit_price,
                'total' => $itemTotal
            ]);

            $product = Product::find($saleItem->product_id);
            $product->increment('quantity', $itemData['quantity']);
        }

        $return->update(['total' => $total]);

        $sale = Sale::find($request->sale_id);
        if ($sale->type == 'credit' && $sale->customer_id) {
            $customer = $sale->customer;
            $customer->decrement('balance', $total);
        }

        return redirect()->route('sales.returns')->with('success', 'Return processed successfully!');
    }
}
