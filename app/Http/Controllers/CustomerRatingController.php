<?php

namespace App\Http\Controllers;

use App\Models\CustomerRating;
use App\Models\Sale;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerRatingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id'=>'nullable|exists:sales,id',
            'online_order_id'=>'nullable|exists:online_orders,id',
            'customer_id'=>'nullable|exists:customers,id',
            'customer_name'=>'nullable|string',
            'customer_phone'=>'nullable|string',
            'branch_id'=>'nullable|exists:branches,id',
            'staff_id'=>'nullable|exists:users,id',
            'rating'=>'required|integer|min:1|max:5',
            'staff_service'=>'nullable|integer|min:1|max:5',
            'waiting_time'=>'nullable|integer|min:1|max:5',
            'product_availability'=>'nullable|integer|min:1|max:5',
            'price_rating'=>'nullable|integer|min:1|max:5',
            'cleanliness'=>'nullable|integer|min:1|max:5',
            'overall_experience'=>'nullable|integer|min:1|max:5',
            'comment'=>'nullable|string|max:1000',
        ]);
        $data['transaction_reference']=$request->sale_id ? Sale::find($request->sale_id)->invoice_number ?? null : null;
        $rating = CustomerRating::create($data);
        AuditService::log('create_customer_rating','customer_rating', CustomerRating::class, $rating->id, null, $rating->toArray(), 'Rating '.$rating->rating.' stars');
        if ($request->expectsJson()) return response()->json($rating,201);
        return back()->with('success','Thank you for your rating!');
    }

    public function index(Request $request)
    {
        $query=CustomerRating::with(['sale','customer','staff','branch'])->latest();
        if ($request->filled('branch_id')) $query->where('branch_id',$request->branch_id);
        if ($request->filled('rating')) $query->where('rating',$request->rating);
        if ($request->filled('staff_id')) $query->where('staff_id',$request->staff_id);
        $ratings=$query->paginate(20)->withQueryString();
        $avg=CustomerRating::avg('rating');
        $byStore=CustomerRating::selectRaw('branch_id, AVG(rating) as avg_rating, COUNT(*) as cnt')->groupBy('branch_id')->with('branch')->get();
        $byStaff=CustomerRating::selectRaw('staff_id, AVG(rating) as avg_rating, COUNT(*) as cnt')->whereNotNull('staff_id')->groupBy('staff_id')->with('staff')->get();
        return view('customer-ratings.index', compact('ratings','avg','byStore','byStaff'));
    }

    public function apiStore(Request $request)
    {
        return $this->store($request);
    }
}
