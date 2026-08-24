<?php

namespace App\Http\Controllers;

use App\Models\PriceChangeRequest;
use App\Models\Product;
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

    public function create()
    {
        if (Auth::user()->role !== 'marketing_officer') {
            abort(403, 'Only marketing officers can submit price change requests.');
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();

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

            // Apply the new price to the product
            $priceChangeRequest->product->update([
                'selling_price' => $priceChangeRequest->proposed_price,
            ]);

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
        $product->update(['selling_price' => $request->new_price]);

        return back()->with(
            'success',
            "{$product->name} price updated from " . number_format((float) $oldPrice, 2) . " to " . number_format((float) $request->new_price, 2) . "."
        );
    }
}
