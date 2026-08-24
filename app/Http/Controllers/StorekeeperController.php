<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorekeeperController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get store statistics
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('quantity', '<=', 10)->count();
        $outOfStockProducts = Product::where('quantity', '<=', 0)->count();
        $totalStockValue = Product::sum(\DB::raw('quantity * cost_price'));
        
        // Recent purchase orders
        $recentPurchaseOrders = PurchaseOrder::with('supplier')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Low stock products
        $lowStockItems = Product::where('quantity', '<=', 10)
            ->orderBy('quantity', 'asc')
            ->take(10)
            ->get();
        
        return view('storekeeper.dashboard', compact(
            'totalProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'totalStockValue',
            'recentPurchaseOrders',
            'lowStockItems'
        ));
    }

    public function products()
    {
        $products = Product::with('category')->orderBy('name')->paginate(20);
        $categories = Category::all();
        return view('storekeeper.products', compact('products', 'categories'));
    }

    public function showProduct($id)
    {
        $product = Product::with(['category', 'brand', 'unit'])->findOrFail($id);
        
        $barcodeValue = $product->barcode ?? $product->sku ?? $product->id;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodePng = $generator->getBarcode($barcodeValue, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128);
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodePng);
        
        return view('storekeeper.product-show', compact('product', 'barcodeBase64', 'barcodeValue'));
    }

    public function stock()
    {
        $products = Product::where('quantity', '<=', 10)
            ->orderBy('quantity', 'asc')
            ->paginate(20);
        return view('storekeeper.stock', compact('products'));
    }

    public function purchaseOrders()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $suppliers = Supplier::all();
        return view('storekeeper.purchase-orders', compact('purchaseOrders', 'suppliers'));
    }

    public function showPurchaseOrder($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items.product', 'createdBy', 'approvedBy'])
            ->findOrFail($id);
        return view('storekeeper.purchase-order-show', compact('purchaseOrder'));
    }

    public function stockTransfers()
    {
        $transfers = \App\Models\StockTransfer::with(['items.product', 'fromLocation', 'toLocation', 'requester', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('storekeeper.stock-transfers', compact('transfers'));
    }

    public function purchaseOrderRequests()
    {
        $requests = \App\Models\PurchaseOrderRequest::with('product', 'requester', 'supplier')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('storekeeper.purchase-order-requests', compact('requests'));
    }

    public function stockAdjustments()
    {
        $adjustments = \App\Models\StockAdjustment::with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('storekeeper.stock-adjustments', compact('adjustments'));
    }

    public function stockReceiving()
    {
        $purchaseOrders = PurchaseOrder::with('supplier', 'items.product')
            ->where('approval_status', 'approved')
            ->where('status', '!=', 'received')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('storekeeper.stock-receiving', compact('purchaseOrders'));
    }

    public function createStockReceiving(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->approval_status !== 'approved') {
            return redirect()->route('storekeeper.stock-receiving')
                ->with('error', 'This purchase order is not approved yet.');
        }

        if ($purchaseOrder->status === 'received') {
            return redirect()->route('storekeeper.stock-receiving')
                ->with('error', 'This purchase order has already been fully received.');
        }

        $purchaseOrder->load(['supplier', 'items.product', 'items.product.unit']);
        return view('storekeeper.stock-receiving-create', compact('purchaseOrder'));
    }

    public function storeStockReceiving(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->approval_status !== 'approved' || $purchaseOrder->status === 'received') {
            return back()->with('error', 'This purchase order cannot receive stock in its current status.');
        }

        $request->validate([
            'received_items' => 'required|array',
            'received_items.*.quantity' => 'required|integer|min:0',
            'received_items.*.notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder->load('items.product');

            $grnItemsToCreate = [];
            $totalCost = 0;
            $notesLines = [];

            foreach ($request->received_items as $itemId => $receivedData) {
                $poItem = $purchaseOrder->items->firstWhere('id', (int) $itemId);
                if (!$poItem) continue;

                $receivedQty = (int) $receivedData['quantity'];
                if ($receivedQty <= 0) continue;

                $previouslyReceived = \App\Models\GrnItem::where('product_id', $poItem->product_id)
                    ->whereHas('goodsReceivedNote', function ($q) use ($purchaseOrder) {
                        $q->where('purchase_order_id', $purchaseOrder->id);
                    })->sum('quantity');

                $remaining = $poItem->quantity - $previouslyReceived;
                if ($remaining <= 0) continue;

                if ($receivedQty > $remaining) {
                    DB::rollBack();
                    return back()->with('error', "Quantity for {$poItem->product->name} exceeds the remaining {$remaining}.");
                }

                $itemTotal = $receivedQty * $poItem->unit_price;
                $totalCost += $itemTotal;

                $grnItemsToCreate[] = [
                    'po_item' => $poItem,
                    'quantity' => $receivedQty,
                    'unit_price' => $poItem->unit_price,
                    'total' => $itemTotal,
                ];

                if (!empty($receivedData['notes'])) {
                    $notesLines[] = "{$poItem->product->name}: {$receivedData['notes']}";
                }
            }

            if (empty($grnItemsToCreate)) {
                DB::rollBack();
                return back()->with('error', 'No items were received. Please enter quantities greater than 0.');
            }

            $grnNumber = 'GRN-' . date('YmdHis');
            while (\App\Models\GoodsReceivedNote::where('grn_number', $grnNumber)->exists()) {
                $grnNumber .= '-' . random_int(10, 99);
            }

            $grn = \App\Models\GoodsReceivedNote::create([
                'grn_number' => $grnNumber,
                'purchase_order_id' => $purchaseOrder->id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'received_date' => now(),
                'notes' => $notesLines ? implode("\n", $notesLines) : null,
                'total' => $totalCost,
                'status' => 'received',
            ]);

            foreach ($grnItemsToCreate as $row) {
                $poItem = $row['po_item'];

                \App\Models\GrnItem::create([
                    'goods_received_note_id' => $grn->id,
                    'product_id' => $poItem->product_id,
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'total' => $row['total'],
                ]);

                $poItem->product->increment('quantity', $row['quantity']);
            }

            // A PO is fully received only when every item's cumulative received
            // quantity covers its ordered quantity
            $allFullyReceived = true;
            foreach ($purchaseOrder->items as $poItem) {
                $totalReceived = \App\Models\GrnItem::where('product_id', $poItem->product_id)
                    ->whereHas('goodsReceivedNote', function ($q) use ($purchaseOrder) {
                        $q->where('purchase_order_id', $purchaseOrder->id);
                    })->sum('quantity');

                if ($totalReceived < $poItem->quantity) {
                    $allFullyReceived = false;
                    break;
                }
            }

            $purchaseOrder->update(['status' => $allFullyReceived ? 'received' : 'partial']);

            DB::commit();

            return redirect()->route('storekeeper.stock-receiving')
                ->with('success', "Stock received successfully ({$grn->grn_number})! Purchase order status updated to " . ($allFullyReceived ? 'Received' : 'Partial'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to receive stock: ' . $e->getMessage());
        }
    }
}
