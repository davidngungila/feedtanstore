<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountingEntry;
use App\Models\MobileMoneyAccount;
use App\Models\BankAccount;
use App\Models\SupplierPayment;
use App\Models\CustomerPayment;
use App\Models\Sale;
use App\Models\PurchaseOrder;
use App\Models\Income;
use App\Models\Expense;

class FinanceController extends Controller
{
    public function dashboard()
    {
        $totalIncome = Income::sum('amount');
        $totalExpenses = Expense::sum('amount');
        $totalSales = Sale::sum('total');
        $totalPurchases = PurchaseOrder::sum('total');
        $cashOnHand = 0;
        
        foreach (AccountingEntry::where('account', 'Cash')->get() as $entry) {
            if ($entry->type === 'debit') {
                $cashOnHand += $entry->amount;
            } else {
                $cashOnHand -= $entry->amount;
            }
        }
        
        $bankBalance = BankAccount::where('is_active', true)->sum('balance');
        $mobileMoneyBalance = MobileMoneyAccount::where('is_active', true)->sum('balance');
        
        $recentEntries = AccountingEntry::orderBy('created_at', 'desc')->take(10)->get();
        
        return view('finance.dashboard', compact(
            'totalIncome', 'totalExpenses', 'totalSales', 'totalPurchases',
            'cashOnHand', 'bankBalance', 'mobileMoneyBalance', 'recentEntries'
        ));
    }
    
    public function transactions()
    {
        $entries = AccountingEntry::orderBy('created_at', 'desc')->paginate(50);
        return view('finance.transactions', compact('entries'));
    }
    
    public function mobileMoneyReconciliation()
    {
        $accounts = MobileMoneyAccount::where('is_active', true)->get();
        $entries = AccountingEntry::where('account', 'Mobile Money')->orderBy('created_at', 'desc')->paginate(50);
        return view('finance.mobile-money-reconciliation', compact('accounts', 'entries'));
    }
    
    public function accountsReceivable()
    {
        $sales = Sale::where('type', 'credit')->where('total', '>', 'paid')->with('customer')->get();
        return view('finance.accounts-receivable', compact('sales'));
    }
    
    public function accountsPayable()
    {
        $purchaseOrders = PurchaseOrder::where('status', 'received')->with('supplier')->get();
        $supplierPayments = SupplierPayment::with('supplier')->get();
        return view('finance.accounts-payable', compact('purchaseOrders', 'supplierPayments'));
    }
    
    public function taxManagement()
    {
        return view('finance.tax-management');
    }
    
    public function budgets()
    {
        return view('finance.budgets');
    }
    
    public function assets()
    {
        return view('finance.assets');
    }
    
    public function reports()
    {
        $today = now()->toDateString();
        $thisMonth = now()->startOfMonth()->toDateString();
        $thisYear = now()->startOfYear()->toDateString();
        
        $dailyIncome = Income::whereDate('date', $today)->sum('amount');
        $dailyExpenses = Expense::whereDate('date', $today)->sum('amount');
        $monthlyIncome = Income::whereDate('date', '>=', $thisMonth)->sum('amount');
        $monthlyExpenses = Expense::whereDate('date', '>=', $thisMonth)->sum('amount');
        $yearlyIncome = Income::whereDate('date', '>=', $thisYear)->sum('amount');
        $yearlyExpenses = Expense::whereDate('date', '>=', $thisYear)->sum('amount');
        
        $recentSales = Sale::orderBy('created_at', 'desc')->take(10)->get();
        $recentPurchases = PurchaseOrder::orderBy('created_at', 'desc')->take(10)->get();
        
        return view('finance.reports', compact(
            'dailyIncome', 'dailyExpenses',
            'monthlyIncome', 'monthlyExpenses',
            'yearlyIncome', 'yearlyExpenses',
            'recentSales', 'recentPurchases'
        ));
    }
    
    public function settings()
    {
        return view('finance.settings');
    }
}
