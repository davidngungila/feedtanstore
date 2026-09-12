<?php

namespace App\Http\Controllers;

use App\Models\CompetitorIntelligence;
use App\Models\Product;
use App\Models\Branch;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompetitorIntelligenceController extends Controller
{
    public function index(Request $request)
    {
        $query = CompetitorIntelligence::with(['product','salesRep','branch'])->latest();
        if ($request->filled('competitor_name')) $query->where('competitor_name','like','%'.$request->competitor_name.'%');
        if ($request->filled('product_name')) $query->where('product_name','like','%'.$request->product_name.'%');
        if ($request->filled('branch_id')) $query->where('branch_id',$request->branch_id);
        if ($request->filled('date_from')) $query->whereDate('date_checked','>=',$request->date_from);
        if ($request->filled('date_to')) $query->whereDate('date_checked','<=',$request->date_to);
        $entries = $query->paginate(20)->withQueryString();
        $products = Product::where('is_active',true)->orderBy('name')->limit(100)->get();
        $branches = Branch::where('is_active',true)->get();
        return view('competitor-intel.index', compact('entries','products','branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'competitor_name'=>'required|string|max:255',
            'competitor_location'=>'nullable|string|max:255',
            'product_id'=>'nullable|exists:products,id',
            'product_name'=>'required|string|max:255',
            'competitor_price'=>'required|numeric|min:0',
            'our_price'=>'required|numeric|min:0',
            'availability'=>'nullable|string|max:50',
            'date_checked'=>'required|date',
            'branch_id'=>'nullable|exists:branches,id',
            'location_id'=>'nullable|exists:locations,id',
            'notes'=>'nullable|string',
            'photo'=>'nullable|image|max:2048',
        ]);
        if ($request->hasFile('photo')) $data['photo']=$request->file('photo')->store('competitor','public');
        $data['sales_rep_id']=Auth::id();
        $entry = CompetitorIntelligence::create($data);
        AuditService::log('create_competitor_intel','competitor_intel', CompetitorIntelligence::class, $entry->id, null, $entry->toArray(), 'Competitor data: '.$entry->competitor_name);
        if ($request->expectsJson()) return response()->json($entry,201);
        return back()->with('success','Competitor intelligence recorded');
    }

    public function reports()
    {
        $avgDiff = CompetitorIntelligence::selectRaw('product_name, AVG(price_difference) as avg_diff, COUNT(*) as cnt, AVG(competitor_price) as avg_comp_price, AVG(our_price) as avg_our_price')->groupBy('product_name')->orderByDesc('cnt')->limit(20)->get();
        $cheaperCompetitors = CompetitorIntelligence::where('price_difference','>',0)->orderByDesc('price_difference')->limit(20)->get();
        $moreExpensive = CompetitorIntelligence::where('price_difference','<',0)->orderBy('price_difference')->limit(20)->get();
        return view('competitor-intel.reports', compact('avgDiff','cheaperCompetitors','moreExpensive'));
    }

    public function apiStore(Request $request)
    {
        $data = $request->validate([
            'competitor_name'=>'required|string|max:255',
            'competitor_location'=>'nullable|string',
            'product_id'=>'nullable|exists:products,id',
            'product_name'=>'required|string',
            'competitor_price'=>'required|numeric|min:0',
            'our_price'=>'required|numeric|min:0',
            'availability'=>'nullable|string',
            'notes'=>'nullable|string',
            'photo'=>'nullable|image|max:2048',
        ]);
        $data['sales_rep_id']=$request->user()->id;
        $data['date_checked']=now()->toDateString();
        if ($request->hasFile('photo')) $data['photo']=$request->file('photo')->store('competitor','public');
        $entry=CompetitorIntelligence::create($data);
        AuditService::log('create_competitor_intel','competitor_intel', CompetitorIntelligence::class, $entry->id, null, $entry->toArray(), 'API competitor');
        return response()->json($entry,201);
    }

    public function apiIndex(Request $request)
    {
        $q=CompetitorIntelligence::with('product')->where('sales_rep_id',$request->user()->id)->latest();
        return response()->json($q->paginate(20));
    }
}
