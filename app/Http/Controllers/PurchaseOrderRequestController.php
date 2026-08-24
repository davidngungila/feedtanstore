<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrderRequest;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderRequestController extends Controller
{
    public function index()
    {
        return view('storekeeper.purchase-order-requests', $this->indexData());
    }

    public function purchasingIndex()
    {
        return view('purchasing.purchase-requests', $this->indexData());
    }

    private function indexData(): array
    {
        $requests = PurchaseOrderRequest::with(['product.unit', 'supplier'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Multi-product submissions share a base request number (POR-...-1, -2, ...)
        // so they are grouped and displayed as a single order
        $groups = $requests->groupBy(function ($r) {
            return preg_replace('/-\d+$/', '', $r->request_number);
        })->sortByDesc(function ($group) {
            return $group->max('created_at');
        });

        $perPage = 20;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $groupPage = new \Illuminate\Pagination\LengthAwarePaginator(
            $groups->forPage($currentPage, $perPage)->values(),
            $groups->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $stats = [
            'pending' => $requests->where('status', 'pending')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
            'processed' => $requests->whereIn('status', ['processed', 'received'])->count(),
        ];

        return compact('groupPage', 'stats');
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        return view('storekeeper.purchase-order-requests-create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.requested_quantity' => 'required|numeric|min:1',
            'products.*.supplier_id' => 'nullable|exists:suppliers,id',
            'products.*.estimated_unit_price' => 'nullable|numeric|min:0',
            'products.*.reason' => 'nullable|string',
        ]);

        $baseRequestNumber = PurchaseOrderRequest::generateRequestNumber();
        $createdCount = 0;

        foreach ($request->products as $index => $productData) {
            // Add sequence suffix for bulk requests (e.g., POR-20260822-0001-1, POR-20260822-0001-2)
            $requestNumber = count($request->products) > 1
                ? $baseRequestNumber . '-' . ($index + 1)
                : $baseRequestNumber;

            $unitPrice = $productData['estimated_unit_price'] ?? null;

            PurchaseOrderRequest::create([
                'request_number' => $requestNumber,
                'product_id' => $productData['product_id'],
                'requested_quantity' => $productData['requested_quantity'],
                'supplier_id' => $productData['supplier_id'] ?? null,
                'estimated_cost' => ($unitPrice !== null && $unitPrice !== '')
                    ? round($productData['requested_quantity'] * $unitPrice, 2)
                    : null,
                'reason' => $productData['reason'] ?? $request->reason,
                'status' => 'pending',
                'requested_by' => Auth::id(),
            ]);
            $createdCount++;
        }

        return redirect()->route('storekeeper.purchase-order-requests')
            ->with('success', "Purchase order request submitted successfully with {$createdCount} product(s)");
    }

    public function show(PurchaseOrderRequest $purchaseOrderRequest)
    {
        return view('storekeeper.purchase-order-requests-show', $this->showData($purchaseOrderRequest));
    }

    public function purchasingShow(PurchaseOrderRequest $purchaseOrderRequest)
    {
        return view('purchasing.purchase-requests-show', $this->showData($purchaseOrderRequest));
    }

    private function showData(PurchaseOrderRequest $purchaseOrderRequest): array
    {
        $purchaseOrderRequest->load(['product.unit', 'requester', 'approver', 'supplier']);

        // All items belonging to the same multi-product submission
        $baseNumber = preg_replace('/-\d+$/', '', $purchaseOrderRequest->request_number);
        $orderItems = PurchaseOrderRequest::with(['product.unit', 'supplier'])
            ->where('request_number', 'like', $baseNumber . '%')
            ->orderBy('request_number')
            ->get();

        $grn = null;
        if ($purchaseOrderRequest->status === 'received') {
            $grn = \App\Models\GoodsReceivedNote::where('notes', 'like', '%' . $purchaseOrderRequest->request_number . '%')
                ->orderByDesc('id')
                ->first();
        }

        $suppliers = \App\Models\Supplier::where('is_active', true)->get();

        return compact('purchaseOrderRequest', 'orderItems', 'baseNumber', 'grn', 'suppliers');
    }

    public function approve(Request $request, PurchaseOrderRequest $purchaseOrderRequest)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            return back()->with('error', 'Unauthorized. Only admins and managers can approve requests');
        }

        if ($purchaseOrderRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'admin_notes' => 'nullable|string',
        ]);

        $purchaseOrderRequest->update([
            'status' => 'approved',
            'supplier_id' => $request->supplier_id,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Purchase order request approved');
    }

    public function reject(Request $request, PurchaseOrderRequest $purchaseOrderRequest)
    {
        if ($purchaseOrderRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed');
        }

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $purchaseOrderRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Purchase order request rejected');
    }

    public function process(PurchaseOrderRequest $purchaseOrderRequest)
    {
        if ($purchaseOrderRequest->status !== 'approved') {
            return back()->with('error', 'This request must be approved first');
        }

        // Create actual purchase order from request
        // This would integrate with your existing PurchaseOrder system
        $purchaseOrderRequest->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Purchase order processed successfully');
    }

    public function receive(Request $request, PurchaseOrderRequest $purchaseOrderRequest)
    {
        if (!in_array($purchaseOrderRequest->status, ['pending', 'approved'])) {
            return back()->with('error', 'This request cannot be received in its current status');
        }

        $request->validate([
            'received_quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
            'batch_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($request->received_quantity > $purchaseOrderRequest->requested_quantity) {
            return back()->with('error', 'Received quantity cannot exceed requested quantity');
        }

        $product = $purchaseOrderRequest->product;

        if ($product->category && $product->category->requires_expiry_date && empty($request->expiry_date)) {
            return back()->with('error', "Expiry date is required for {$product->name} (Category: {$product->category->name})");
        }

        \DB::beginTransaction();
        try {
            // Create GRN for this request
            $grnNumber = 'GRN-POR-' . date('YmdHis');

            $grn = \App\Models\GoodsReceivedNote::create([
                'grn_number' => $grnNumber,
                'supplier_id' => $purchaseOrderRequest->supplier_id ?? null,
                'purchase_order_id' => null,
                'received_date' => now(),
                'notes' => $request->notes ?? 'Received from Purchase Order Request: ' . $purchaseOrderRequest->request_number,
                'total' => 0,
                'status' => 'received',
            ]);

            $product = $purchaseOrderRequest->product;
            $unitPrice = $request->unit_price;
            $total = round($request->received_quantity * $unitPrice, 2);

            \App\Models\GrnItem::create([
                'goods_received_note_id' => $grn->id,
                'product_id' => $product->id,
                'quantity' => $request->received_quantity,
                'unit_price' => $unitPrice,
                'total' => $total,
                'expiry_date' => $request->expiry_date,
            ]);

            // Update product stock, cost price, batch and expiry for tracking
            $product->increment('quantity', $request->received_quantity);
            $product->update(array_filter([
                'cost_price' => $unitPrice,
                'batch_number' => $request->batch_number,
                'expiry_date' => $request->expiry_date,
            ], fn ($v) => $v !== null && $v !== ''));

            // Update GRN total
            $grn->update(['total' => $total]);

            // Update request status
            $purchaseOrderRequest->update([
                'status' => 'received',
                'processed_at' => now(),
            ]);

            \DB::commit();

            $redirectRoute = request()->routeIs('purchasing.purchase-requests.*')
                ? 'purchasing.purchase-requests.show'
                : 'storekeeper.purchase-order-requests.show';

            return redirect()->route($redirectRoute, $purchaseOrderRequest)
                ->with('success', 'Products received successfully! Stock updated and GRN created.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to receive products: ' . $e->getMessage());
        }
    }
}
