<?php

namespace App\Http\Controllers;

use App\Models\PriceChangeRequest;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriceRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['admin', 'manager']);

        $query = PriceChangeRequest::with(['product', 'requester', 'reviewer'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at');

        if ($user->role === 'marketing_officer') {
            $query->where('requested_by', $user->id);
        }

        $requests = $query->paginate(20);

        $stats = [
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];

        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('price-requests.index', compact('requests', 'stats', 'products', 'isAdmin'));
    }

    /**
     * Full price management for ALL products used in sales.
     * Multiple prices per product are allowed but only ONE is active at a time;
     * the active price is what POS/sales charge customers.
     */
    public function prices(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized. Only admins and managers can manage product prices.');
        }

        $search = $request->input('search');

        $products = Product::with(['prices.creator', 'unit'])
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%")))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('price-requests.prices', compact('products', 'search'));
    }

    public function storePrice(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized. Only admins and managers can manage product prices.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'label' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'activate_now' => 'nullable|boolean',
        ]);

        $product = Product::findOrFail($request->product_id);

        DB::beginTransaction();
        try {
            $productPrice = ProductPrice::create([
                'product_id' => $product->id,
                'price' => $request->price,
                'label' => $request->label,
                'is_active' => false,
                'created_by' => Auth::id(),
                'notes' => $request->notes,
            ]);

            // Activate immediately when requested OR when the product has no active price yet
            if ($request->boolean('activate_now') || !$product->prices()->active()->exists()) {
                $productPrice->activate();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add price: ' . $e->getMessage());
        }

        return back()->with('success', "New price added for {$product->name}"
            . ($productPrice->fresh()->is_active ? ' and activated.' : '.'));
    }

    public function activatePrice(ProductPrice $productPrice)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized. Only admins and managers can manage product prices.');
        }

        $productPrice->activate();

        return back()->with('success', "{$productPrice->product->name} now sells at "
            . number_format((float) $productPrice->price, 2) . ".");
    }

    public function deactivatePrice(ProductPrice $productPrice)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized. Only admins and managers can manage product prices.');
        }

        $productPrice->update(['is_active' => false]);

        return back()->with('success', 'Price deactivated. The previous selling price still applies until you activate another price.');
    }

    public function destroyPrice(ProductPrice $productPrice)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized. Only admins and managers can manage product prices.');
        }

        if ($productPrice->is_active) {
            return back()->with('error', 'This price is currently active. Activate another price before deleting it.');
        }

        $productName = $productPrice->product->name;
        $productPrice->delete();

        return back()->with('success', "Price entry removed from {$productName}.");
    }

    public function create()
    {
        if (Auth::user()->role !== 'marketing_officer') {
            abort(403, 'Only marketing officers can submit price change requests.');
        }

        $products = Product::with(['prices' => fn ($q) => $q->orderByDesc('is_active')->orderByDesc('created_at'), 'unit', 'category'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('price-requests.create', compact('products'));
    }

    public function store(Request $request)
    {
        if (!in_array(Auth::user()->role, ['marketing_officer'])) {
            abort(403, 'Only marketing officers can submit price change requests.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'proposed_price' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
        ]);

        $product = Product::findOrFail($request->product_id);

        PriceChangeRequest::create([
            'product_id' => $product->id,
            'requested_by' => Auth::id(),
            'current_price' => $product->selling_price ?? 0,
            'proposed_price' => $request->proposed_price,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('price-requests.index')
            ->with('success', "Price change request for {$product->name} submitted for approval.");
    }

    public function approve(PriceChangeRequest $priceChangeRequest)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized. Only admins and managers can approve price requests.');
        }

        if ($priceChangeRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        DB::beginTransaction();
        try {
            $priceChangeRequest->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Create a new price entry from the approved request and make it the active one
            $productPrice = ProductPrice::create([
                'product_id' => $priceChangeRequest->product_id,
                'price' => $priceChangeRequest->proposed_price,
                'label' => 'Approved request',
                'is_active' => false,
                'created_by' => Auth::id(),
                'notes' => 'From approved price request',
            ]);
            $productPrice->activate();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to apply new price: ' . $e->getMessage());
        }

        return back()->with('success', "Price request approved — {$priceChangeRequest->product->name} now uses the new price.");
    }

    public function reject(Request $request, PriceChangeRequest $priceChangeRequest)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized. Only admins and managers can reject price requests.');
        }

        if ($priceChangeRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $priceChangeRequest->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Price request rejected.');
    }

    public function setPrice(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized. Only admins and managers can set product prices directly.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'new_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($request->product_id);
        $oldPrice = $product->selling_price;

        // Recorded in the product's price list and activated immediately
        $productPrice = ProductPrice::create([
            'product_id' => $product->id,
            'price' => $request->new_price,
            'label' => 'Direct set',
            'is_active' => false,
            'created_by' => Auth::id(),
            'notes' => $request->notes,
        ]);
        $productPrice->activate();

        return back()->with(
            'success',
            "{$product->name} price updated from " . number_format((float) $oldPrice, 2) . " to " . number_format((float) $request->new_price, 2) . "."
        );
    }
}
