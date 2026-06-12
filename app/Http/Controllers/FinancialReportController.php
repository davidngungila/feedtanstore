<?php

namespace App\Http\Controllers;

use App\Models\AccountingEntry;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function index()
    {
        $totalSales = Sale::where('status', 'completed')->sum('total');
        $totalExpenses = Expense::sum('amount');
        $totalIncome = Income::sum('amount');
        $profit = $totalSales + $totalIncome - $totalExpenses;
        
        $recentEntries = AccountingEntry::latest()->take(20)->get();
        
        return view('finance.reports', compact('totalSales', 'totalExpenses', 'totalIncome', 'profit', 'recentEntries'));
    }
}
