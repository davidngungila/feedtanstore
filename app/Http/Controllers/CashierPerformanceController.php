<?php

namespace App\Http\Controllers;

use App\Models\CashierServiceTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $cashierId = $request->input('cashier_id');
        $branchId = $request->input('branch_id');

        $q = CashierServiceTime::with('cashier')->where('status','completed')->whereBetween(DB::raw('date(service_start_time)'), [$from,$to]);
        if ($cashierId) $q->where('cashier_id',$cashierId);
        if ($branchId) $q->where('branch_id',$branchId);
        $times = $q->get();

        $avgService = $times->avg('duration_seconds');
        $totalCustomers = $times->count();
        $perHour = $avgService ? round(3600 / max(1,$avgService),2) : 0;
        $fastest = $times->min('duration_seconds');
        $longest = $times->max('duration_seconds');
        $byCashier = CashierServiceTime::selectRaw('cashier_id, AVG(duration_seconds) as avg_duration, COUNT(*) as customers, MIN(duration_seconds) as fastest, MAX(duration_seconds) as longest')->where('status','completed')->whereBetween(DB::raw('date(service_start_time)'), [$from,$to])->groupBy('cashier_id')->with('cashier')->get();
        $byStore = CashierServiceTime::selectRaw('branch_id, AVG(duration_seconds) as avg_duration, COUNT(*) as customers')->where('status','completed')->whereBetween(DB::raw('date(service_start_time)'), [$from,$to])->groupBy('branch_id')->with('branch')->get();
        $byHour = CashierServiceTime::selectRaw("strftime('%H', service_start_time) as hr, AVG(duration_seconds) as avg_duration, COUNT(*) as cnt")->where('status','completed')->whereBetween(DB::raw('date(service_start_time)'), [$from,$to])->groupBy('hr')->orderBy('hr')->get();
        $daily = CashierServiceTime::selectRaw("date(service_start_time) as d, AVG(duration_seconds) as avg_duration, COUNT(*) as cnt")->where('status','completed')->whereBetween(DB::raw('date(service_start_time)'), [$from,$to])->groupBy('d')->orderBy('d')->get();

        $cashiers = User::whereIn('role',['cashier','admin'])->orderBy('name')->get();
        $branches = \App\Models\Branch::where('is_active',true)->get();

        if ($request->expectsJson()) return response()->json(compact('avgService','totalCustomers','perHour','fastest','longest','byCashier','byStore','byHour','daily'));
        return view('reports.cashier-performance', compact('avgService','totalCustomers','perHour','fastest','longest','byCashier','byStore','byHour','daily','cashiers','branches','from','to','cashierId','branchId'));
    }

    // API to start timer (new sale or first scan)
    public function start(Request $request)
    {
        $request->validate(['customer_id'=>'nullable|exists:customers,id','branch_id'=>'nullable|exists:branches,id','trigger'=>'nullable|in:new_sale,first_scan']);
        $active = CashierServiceTime::where('cashier_id',$request->user()->id)->where('status','active')->first();
        if ($active) return response()->json($active);
        $time = CashierServiceTime::create([
            'cashier_id'=>$request->user()->id,
            'customer_id'=>$request->customer_id,
            'branch_id'=>$request->branch_id,
            'service_start_time'=>now(),
            'status'=>'active',
            'channel'=>'in_store',
            'start_trigger'=>$request->trigger??'new_sale',
        ]);
        return response()->json($time,201);
    }

    public function end(Request $request)
    {
        $time = CashierServiceTime::where('cashier_id',$request->user()->id)->where('status','active')->latest()->first();
        if (!$time) return response()->json(['message'=>'No active service time'],404);
        $time->complete();
        if ($request->filled('sale_id')) $time->update(['sale_id'=>$request->sale_id]);
        return response()->json($time);
    }
}
