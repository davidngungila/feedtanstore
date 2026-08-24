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
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items.product', 'items.product.unit', 'createdBy', 'approvedBy'])
            ->findOrFail($id);

        $purchaseOrder->load(['goodsReceivedNotes' => function ($q) {
            $q->with('items.product')->orderBy('received_date', 'desc');
        }]);

        $receivedByProduct = \App\Models\GrnItem::whereHas('goodsReceivedNote', function ($q) use ($purchaseOrder) {
            $q->where('purchase_order_id', $purchaseOrder->id);
        })
            ->selectRaw('product_id, SUM(quantity) as total_received')
            ->groupBy('product_id')
            ->pluck('total_received', 'product_id');

        return view('storekeeper.purchase-order-show', compact('purchaseOrder', 'receivedByProduct'));
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
        return app(\App\Http\Controllers\PurchaseOrderRequestController::class)->index();
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

        $purchaseOrder->load(['supplier', 'items.product.category', 'items.product.unit']);
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
            'received_items.*.unit_price' => 'nullable|numeric|min:0',
            'received_items.*.batch_number' => 'nullable|string|max:100',
            'received_items.*.expiry_date' => 'nullable|date',
            'received_items.*.notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder->load('items.product.category');

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

                if ($poItem->product->category && $poItem->product->category->requires_expiry_date && empty($receivedData['expiry_date'])) {
                    DB::rollBack();
                    return back()->with('error', "Expiry date is required for {$poItem->product->name} (Category: {$poItem->product->category->name})");
                }

                $unitPrice = (isset($receivedData['unit_price']) && $receivedData['unit_price'] !== '' && $receivedData['unit_price'] !== null)
                    ? $receivedData['unit_price']
                    : $poItem->unit_price;

                // Batch number is generated automatically when not provided
                $batchNumber = !empty($receivedData['batch_number'])
                    ? $receivedData['batch_number']
                    : \App\Models\Product::generateBatchNumber($poItem->product_id);

                $itemTotal = round($receivedQty * $unitPrice, 2);
                $totalCost += $itemTotal;

                $grnItemsToCreate[] = [
                    'po_item' => $poItem,
                    'quantity' => $receivedQty,
                    'unit_price' => $unitPrice,
                    'total' => $itemTotal,
                    'batch_number' => $batchNumber,
                    'expiry_date' => $receivedData['expiry_date'] ?? null,
                ];

                $parts = ['Batch: ' . $batchNumber];
                if (!empty($receivedData['expiry_date'])) {
                    $parts[] = 'Expiry: ' . $receivedData['expiry_date'];
                }
                if (!empty($receivedData['notes'])) {
                    $parts[] = $receivedData['notes'];
                }
                $notesLines[] = "{$poItem->product->name}: " . implode(' | ', $parts);
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
                $product = $poItem->product;

                \App\Models\GrnItem::create([
                    'goods_received_note_id' => $grn->id,
                    'product_id' => $product->id,
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'total' => $row['total'],
                    'expiry_date' => $row['expiry_date'],
                ]);

                // Auto-create a barcode for the product if it does not have one yet
                if (!$product->barcode) {
                    $product->update(['barcode' => \App\Models\Product::generateUniqueBarcode()]);
                }

                $product->increment('quantity', $row['quantity']);
                $product->update(array_filter([
                    'cost_price' => $row['unit_price'],
                    'batch_number' => $row['batch_number'],
                    'expiry_date' => $row['expiry_date'],
                ], fn ($v) => $v !== null && $v !== ''));
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

    public function grnIndex(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $grns = \App\Models\GoodsReceivedNote::with(['supplier', 'purchaseOrder'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('grn_number', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%')
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                            $supplierQuery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('purchaseOrder', function ($purchaseOrderQuery) use ($search) {
                            $purchaseOrderQuery->where('po_number', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->get();

        return view('storekeeper.grn', compact('grns', 'search'));
    }

    public function grnCreate()
    {
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get();
        $products = \App\Models\Product::with('category')->orderBy('name')->get();

        return view('storekeeper.grn-create', compact('suppliers', 'products'));
    }

    public function grnStore(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'received_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.selling_price' => 'nullable|numeric|min:0',
            'products.*.batch_number' => 'nullable|string|max:100',
            'products.*.expiry_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            // Validate expiry dates for products in categories that require them
            foreach ($request->products as $productData) {
                $product = \App\Models\Product::with('category')->find($productData['product_id']);
                if ($product && $product->category && $product->category->requires_expiry_date) {
                    if (empty($productData['expiry_date'])) {
                        DB::rollBack();
                        return back()->with('error', "Expiry date is required for {$product->name} (Category: {$product->category->name})")->withInput();
                    }
                }
            }

            $grnNumber = 'GRN-' . date('YmdHis');
            $total = 0;
            foreach ($request->products as $productData) {
                $total += round($productData['quantity'] * $productData['unit_price'], 2);
            }

            $grn = \App\Models\GoodsReceivedNote::create([
                'grn_number' => $grnNumber,
                'supplier_id' => $request->supplier_id,
                'purchase_order_id' => null,
                'received_date' => $request->received_date,
                'notes' => $request->notes,
                'total' => round($total, 2),
                'status' => 'received',
            ]);

            foreach ($request->products as $productData) {
                $itemTotal = round($productData['quantity'] * $productData['unit_price'], 2);

                // Batch number is generated automatically when not provided
                $batchNumber = !empty($productData['batch_number'])
                    ? $productData['batch_number']
                    : \App\Models\Product::generateBatchNumber($productData['product_id']);

                \App\Models\GrnItem::create([
                    'goods_received_note_id' => $grn->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'total' => $itemTotal,
                    'expiry_date' => $productData['expiry_date'] ?? null,
                ]);

                $product = \App\Models\Product::find($productData['product_id']);

                // Auto-create a barcode for the product if it does not have one yet
                if (!$product->barcode) {
                    $product->update(['barcode' => \App\Models\Product::generateUniqueBarcode()]);
                }

                $product->increment('quantity', $productData['quantity']);
                $product->update(array_filter([
                    'cost_price' => $productData['unit_price'],
                    'selling_price' => $productData['selling_price'] ?? null,
                    'batch_number' => $batchNumber,
                    'expiry_date' => $productData['expiry_date'] ?? null,
                ], fn ($v) => $v !== null && $v !== ''));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create GRN: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('storekeeper.grn.show', $grn)
            ->with('success', "Goods Received Note {$grnNumber} created successfully! Stock updated.");
    }

    public function grnShow(\App\Models\GoodsReceivedNote $grn)
    {
        $grn->load(['supplier', 'purchaseOrder', 'items.product.unit']);

        return view('storekeeper.grn-show', compact('grn'));
    }
}
