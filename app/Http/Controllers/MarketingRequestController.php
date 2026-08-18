<?php

namespace App\Http\Controllers;

use App\Models\MarketingRequest;
use App\Models\MarketingRequestItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingRequestController extends Controller
{
    public function index()
    {
        $query = MarketingRequest::with(['requester', 'processor', 'items.product'])
            ->orderBy('created_at', 'desc');

        if (Auth::user()->role === 'marketing_officer') {
            $query->where('requested_by', Auth::id());
        }

        $marketingRequests = $query->paginate(20);

        return view('marketing-requests.index', compact('marketingRequests'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('marketing-requests.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_requested' => 'required|integer|min:1',
            'products.*.notes' => 'nullable|string',
        ]);

        $marketingRequest = MarketingRequest::create([
            'requested_by' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        foreach ($request->products as $item) {
            MarketingRequestItem::create([
                'marketing_request_id' => $marketingRequest->id,
                'product_id' => $item['product_id'],
                'quantity_requested' => $item['quantity_requested'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return redirect()->route('marketing-requests.show', $marketingRequest)
            ->with('success', 'Marketing request submitted successfully.');
    }

    public function show(MarketingRequest $marketingRequest)
    {
        $marketingRequest->load(['requester', 'processor', 'items.product']);
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('marketing-requests.show', compact('marketingRequest', 'products'));
    }

    public function accept(MarketingRequest $marketingRequest)
    {
        if (Auth::user()->role !== 'storekeeper' && Auth::user()->role !== 'admin') {
            abort(403, 'Only storekeeper or admin can accept marketing requests.');
        }

        $marketingRequest->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Marketing request accepted. You can now process it.');
    }

    public function process(Request $request, MarketingRequest $marketingRequest)
    {
        if (Auth::user()->role !== 'storekeeper' && Auth::user()->role !== 'admin') {
            abort(403, 'Only storekeeper or admin can process marketing requests.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.quantity_provided' => 'required|integer|min:0',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
            'storekeeper_notes' => 'nullable|string',
        ]);

        foreach ($request->items as $itemId => $itemData) {
            MarketingRequestItem::where('id', $itemId)
                ->where('marketing_request_id', $marketingRequest->id)
                ->update([
                    'quantity_provided' => $itemData['quantity_provided'],
                    'unit_price' => $itemData['unit_price'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
        }

        $marketingRequest->update([
            'status' => 'processed',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'storekeeper_notes' => $request->storekeeper_notes,
        ]);

        return redirect()->back()->with('success', 'Marketing request processed and sent to marketing officer.');
    }

    public function reject(MarketingRequest $marketingRequest)
    {
        if (Auth::user()->role !== 'storekeeper' && Auth::user()->role !== 'admin') {
            abort(403, 'Only storekeeper or admin can reject marketing requests.');
        }

        $marketingRequest->update([
            'status' => 'rejected',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Marketing request rejected.');
    }
}
