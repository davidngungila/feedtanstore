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
        $purchaseOrder->load(['supplier', 'items.product', 'items.product.unit']);
        return view('storekeeper.stock-receiving-create', compact('purchaseOrder'));
    }

    public function storeStockReceiving(Request $request, PurchaseOrder $purchaseOrder)
    {
        \Log::info('storeStockReceiving START', [
            'route_po_id' => $purchaseOrder->id,
            'request_po_id' => $request->input('purchase_order_id'),
            'received_items' => $request->input('received_items'),
            'user' => auth()->id()
        ]);
        
        $request->validate([
            'received_items' => 'required|array',
            'received_items.*.quantity' => 'required|integer|min:0',
            'received_items.*.notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder->load('items.product');

            $allFullyReceived = true;
            $anyReceived = false;

            foreach ($request->received_items as $itemId => $receivedData) {
                \Log::info('Processing item', ['itemId' => $itemId, 'data' => $receivedData]);
                $poItem = $purchaseOrder->items()->where('id', $itemId)->first();
                \Log::info('Found poItem', ['poItem' => $poItem ? $poItem->id : null, 'poItemsCount' => $purchaseOrder->items->count()]);
                if (!$poItem) continue;

                $receivedQty = $receivedData['quantity'];
                if ($receivedQty <= 0) continue;

                $anyReceived = true;

                \Log::info('Creating GRN', ['poItem' => $poItem->id, 'receivedQty' => $receivedQty, 'supplier_id' => $purchaseOrder->supplier_id, 'product_id' => $poItem->product_id]);

                // Create or update GRN for this item
                $grn = \App\Models\GoodsReceivedNote::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'supplier_id' => $purchaseOrder->supplier_id,
                    'product_id' => $poItem->product_id,
                    'quantity_ordered' => $poItem->quantity,
                    'quantity_received' => $receivedQty,
                    'quantity_accepted' => $receivedQty,
                    'quantity_rejected' => 0,
                    'unit_cost' => $poItem->unit_price,
                    'total_cost' => $receivedQty * $poItem->unit_price,
                    'status' => 'accepted',
                    'received_by' => Auth::id(),
                    'notes' => $receivedData['notes'] ?? null,
                ]);

                \Log::info('GRN created', ['grn_id' => $grn->id]);

                // Update product stock
                $product = $poItem->product;
                $product->increment('quantity', $receivedQty);

                // Update GRN item
                \App\Models\GrnItem::create([
                    'goods_received_note_id' => $grn->id,
                    'product_id' => $poItem->product_id,
                    'quantity_ordered' => $poItem->quantity,
                    'quantity_received' => $receivedQty,
                    'quantity_accepted' => $receivedQty,
                    'quantity_rejected' => 0,
                    'unit_cost' => $poItem->unit_price,
                    'total_cost' => $receivedQty * $poItem->unit_price,
                ]);

                // Check if fully received
                $totalReceived = \App\Models\GrnItem::whereHas('goodsReceivedNote', function ($q) use ($purchaseOrder, $poItem) {
                    $q->where('purchase_order_id', $purchaseOrder->id)
                        ->where('product_id', $poItem->product_id);
                })->sum('quantity');

                if ($totalReceived < $poItem->quantity) {
                    $allFullyReceived = false;
                }
            }

            if (!$anyReceived) {
                DB::rollBack();
                return back()->with('error', 'No items were received. Please enter quantities greater than 0.');
            }

            // Update purchase order status
            if ($allFullyReceived) {
                $purchaseOrder->update(['status' => 'received']);
            } else {
                $purchaseOrder->update(['status' => 'partial']);
            }

            DB::commit();

            return redirect()->route('storekeeper.stock-receiving')
                ->with('success', 'Stock received successfully! Purchase order status updated to ' . ($allFullyReceived ? 'Received' : 'Partial'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to receive stock: ' . $e->getMessage());
        }
    }
}
