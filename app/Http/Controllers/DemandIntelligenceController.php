<?php

namespace App\Http\Controllers;

use App\Models\CustomerDemand;
use App\Models\AbandonedCart;
use App\Models\Wishlist;
use App\Models\OnlineSearchLog;
use App\Models\CompetitorIntelligence;
use Illuminate\Http\Request;

class DemandIntelligenceController extends Controller
{
    public function dashboard(Request $request)
    {
        $mostRequested = CustomerDemand::selectRaw('product_requested, COUNT(*) as cnt, SUM(requested_quantity) as qty, SUM(CASE WHEN was_out_of_stock=1 THEN 1 ELSE 0 END) as oos_cnt')
            ->groupBy('product_requested')->orderByDesc('cnt')->limit(20)->get();
        $outOfStockDemand = CustomerDemand::where('was_out_of_stock',true)->selectRaw('product_requested, COUNT(*) as cnt')->groupBy('product_requested')->orderByDesc('cnt')->limit(20)->get();
        $byLocation = CustomerDemand::selectRaw('branch_id, COUNT(*) as cnt')->groupBy('branch_id')->with('branch')->get();
        $byMonth = CustomerDemand::selectRaw("strftime('%Y-%m', request_date) as month, COUNT(*) as cnt")->groupBy('month')->orderBy('month')->get();
        $trends = CustomerDemand::selectRaw("date(request_date) as d, COUNT(*) as cnt")->groupBy('d')->orderBy('d')->limit(30)->get();
        $wishlistTop = Wishlist::selectRaw('product_id, COUNT(*) as cnt')->groupBy('product_id')->with('product')->orderByDesc('cnt')->limit(10)->get();
        $abandonedTop = AbandonedCart::latest()->limit(10)->get();
        $searchTop = OnlineSearchLog::selectRaw('search_term, COUNT(*) as cnt, AVG(results_count) as avg_results')->groupBy('search_term')->orderByDesc('cnt')->limit(10)->get();
        $competitorTop = CompetitorIntelligence::selectRaw('product_name, COUNT(*) as cnt, AVG(price_difference) as avg_diff')->groupBy('product_name')->orderByDesc('cnt')->limit(10)->get();

        if ($request->expectsJson()) {
            return response()->json(compact('mostRequested','outOfStockDemand','byLocation','byMonth','trends','wishlistTop','abandonedTop','searchTop','competitorTop'));
        }
        return view('demand-intelligence.dashboard', compact('mostRequested','outOfStockDemand','byLocation','byMonth','trends','wishlistTop','abandonedTop','searchTop','competitorTop'));
    }
}
