<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\FieldSalesOrder;
use App\Models\OnlineOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $branchId = $request->input('branch_id');
        $channel = $request->input('channel'); // in_store, field_sales, online, all
        $groupBy = $request->input('group_by','day'); // day, week, month, year, store, cashier, product, category, channel

        $salesQuery = Sale::whereBetween(DB::raw('date(created_at)'), [$from,$to])->where('status','completed');
        if ($branchId) $salesQuery->where('branch_id',$branchId);
        if ($channel && $channel!=='all') $salesQuery->where('sales_channel',$channel);
        $sales = $salesQuery->get();

        $gross = $sales->sum('total');
        $discounts = $sales->sum('discount');
        $tax = $sales->sum('tax');
        $net = $gross - $discounts;
        $cogs = $sales->sum('cost_of_goods_sold');
        // Fallback calc cogs if not stored
        if ($cogs==0) {
            $cogs = $sales->sum(function($s){ return $s->items->sum(fn($i)=> ($i->product->cost_price??0)*$i->quantity); });
        }
        $grossProfit = $gross - $cogs;
        $paid = $sales->sum('paid');
        $outstanding = $sales->sum(fn($s)=> max(0,$s->total - $s->paid));
        $refunds = SaleReturn::whereBetween(DB::raw('date(created_at)'), [$from,$to])->sum('total');
        $final = $gross - $refunds;

        // Field sales contribution
        $fieldOrders = FieldSalesOrder::whereBetween(DB::raw('date(created_at)'), [$from,$to]);
        if ($branchId) $fieldOrders->where('branch_id',$branchId);
        $fieldOrdersGet=$fieldOrders->get();
        $fieldGross=$fieldOrdersGet->sum('total');

        // Online paid
        $onlinePaid = OnlineOrder::whereBetween(DB::raw('date(created_at)'), [$from,$to])->where('payment_status','paid')->sum('total');

        // Aggregations
        $byChannel = Sale::selectRaw('sales_channel, SUM(total) as total, COUNT(*) as cnt')->whereBetween(DB::raw('date(created_at)'), [$from,$to])->groupBy('sales_channel')->get();
        $byStore = Sale::selectRaw('branch_id, SUM(total) as total, COUNT(*) as cnt')->whereBetween(DB::raw('date(created_at)'), [$from,$to])->groupBy('branch_id')->with('branch')->get();
        $byCashier = Sale::selectRaw('user_id, SUM(total) as total, COUNT(*) as cnt, AVG(total) as avg')->whereBetween(DB::raw('date(created_at)'), [$from,$to])->groupBy('user_id')->with('user')->get();
        $byProduct = DB::table('sale_items')->join('sales','sales.id','=','sale_items.sale_id')->whereBetween(DB::raw('date(sales.created_at)'), [$from,$to])->selectRaw('product_id, SUM(sale_items.total) as total, SUM(sale_items.quantity) as qty')->groupBy('product_id')->orderByDesc('total')->limit(20)->get();
        $byDay = Sale::selectRaw("date(created_at) as d, SUM(total) as total, COUNT(*) as cnt")->whereBetween(DB::raw('date(created_at)'), [$from,$to])->groupBy('d')->orderBy('d')->get();

        if ($request->expectsJson()) {
            return response()->json(compact('gross','discounts','tax','net','cogs','grossProfit','paid','outstanding','refunds','final','fieldGross','onlinePaid','byChannel','byStore','byCashier','byProduct','byDay'));
        }
        return view('reports.revenue', compact('gross','discounts','tax','net','cogs','grossProfit','paid','outstanding','refunds','final','fieldGross','onlinePaid','byChannel','byStore','byCashier','byProduct','byDay','from','to','branchId','channel'));
    }
}
