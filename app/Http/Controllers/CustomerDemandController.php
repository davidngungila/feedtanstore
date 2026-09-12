<?php

namespace App\Http\Controllers;

use App\Models\CustomerDemand;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Location;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDemandController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerDemand::with(['customer','staff','branch','location','product'])->latest();
        if ($request->filled('status')) $query->where('status',$request->status);
        if ($request->filled('branch_id')) $query->where('branch_id',$request->branch_id);
        if ($request->filled('channel')) $query->where('channel',$request->channel);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use($s){
                $q->where('product_requested','like',"%$s%")->orWhere('customer_name','like',"%$s%");
            });
        }
        $demands = $query->paginate(20)->withQueryString();

        // Reports aggregates
        $mostRequested = CustomerDemand::selectRaw('product_requested, COUNT(*) as cnt, SUM(requested_quantity) as total_qty')
            ->groupBy('product_requested')->orderByDesc('cnt')->limit(10)->get();
        $outOfStock = CustomerDemand::where('was_out_of_stock',true)->selectRaw('product_requested, COUNT(*) as cnt')->groupBy('product_requested')->orderByDesc('cnt')->limit(10)->get();

        return view('customer-demands.index', compact('demands','mostRequested','outOfStock'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->limit(200)->get();
        $branches = Branch::where('is_active',true)->get();
        $locations = Location::where('is_active',true)->get();
        return view('customer-demands.create', compact('customers','branches','locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'=>'nullable|exists:customers,id',
            'customer_name'=>'nullable|string|max:255',
            'customer_phone'=>'nullable|string|max:20',
            'product_requested'=>'required|string|max:255',
            'product_id'=>'nullable|exists:products,id',
            'requested_quantity'=>'required|integer|min:1',
            'request_date'=>'required|date',
            'branch_id'=>'nullable|exists:branches,id',
            'location_id'=>'nullable|exists:locations,id',
            'note'=>'nullable|string',
            'was_out_of_stock'=>'nullable|boolean',
            'status'=>'nullable|in:new,reviewing,planned,ordered,available,closed',
            'channel'=>'nullable|in:in_store,field_sales,online',
        ]);
        $data['staff_id']=Auth::id();
        $data['was_out_of_stock']=$request->boolean('was_out_of_stock');
        $data['status']=$data['status']??'new';
        $data['channel']=$data['channel']??'in_store';
        $data['request_date']=$data['request_date']??now()->toDateString();
        $demand = CustomerDemand::create($data);
        AuditService::log('create_customer_demand','customer_demand', CustomerDemand::class, $demand->id, null, $demand->toArray(), 'Demand created: '.$demand->product_requested);
        if ($request->expectsJson()) return response()->json($demand,201);
        return redirect()->route('customer-demands.index')->with('success','Demand recorded');
    }

    public function updateStatus(Request $request, CustomerDemand $demand)
    {
        $request->validate(['status'=>'required|in:new,reviewing,planned,ordered,available,closed']);
        $old=$demand->status;
        $demand->update(['status'=>$request->status]);
        AuditService::log('update_customer_demand','customer_demand', CustomerDemand::class, $demand->id, ['status'=>$old], ['status'=>$request->status], 'Status changed');
        return back()->with('success','Status updated');
    }

    public function reports(Request $request)
    {
        $byStore = CustomerDemand::selectRaw('branch_id, COUNT(*) as cnt')->groupBy('branch_id')->with('branch')->get();
        $byMonth = CustomerDemand::selectRaw("strftime('%Y-%m', request_date) as month, COUNT(*) as cnt")->groupBy('month')->orderBy('month')->get();
        // For MySQL fallback use DATE_FORMAT if needed - sqlite uses strftime
        $byCustomer = CustomerDemand::selectRaw('customer_name, COUNT(*) as cnt')->whereNotNull('customer_name')->groupBy('customer_name')->orderByDesc('cnt')->limit(10)->get();
        $byRep = CustomerDemand::selectRaw('staff_id, COUNT(*) as cnt')->groupBy('staff_id')->with('staff')->orderByDesc('cnt')->get();
        return view('customer-demands.reports', compact('byStore','byMonth','byCustomer','byRep'));
    }

    // API for POS & field sales
    public function apiStore(Request $request)
    {
        $data = $request->validate([
            'customer_id'=>'nullable|exists:customers,id',
            'customer_name'=>'nullable|string',
            'customer_phone'=>'nullable|string',
            'product_requested'=>'required|string|max:255',
            'requested_quantity'=>'required|integer|min:1',
            'was_out_of_stock'=>'nullable|boolean',
            'note'=>'nullable|string',
            'branch_id'=>'nullable|exists:branches,id',
            'location_id'=>'nullable|exists:locations,id',
        ]);
        $data['staff_id']=$request->user()->id;
        $data['request_date']=now()->toDateString();
        $data['channel']=$request->user()->role==='field_sales'?'field_sales':'in_store';
        $demand = CustomerDemand::create($data);
        AuditService::log('create_customer_demand','customer_demand', CustomerDemand::class, $demand->id, null, $demand->toArray(), 'API demand');
        return response()->json($demand,201);
    }

    public function apiIndex(Request $request)
    {
        $q=CustomerDemand::with(['customer','staff'])->latest();
        if ($request->filled('search')) $q->where('product_requested','like','%'.$request->search.'%');
        return response()->json($q->paginate(20));
    }
}
