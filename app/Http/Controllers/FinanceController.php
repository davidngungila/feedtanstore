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
use App\Models\Capital;
use App\Models\Account;
use Dompdf\Dompdf;

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
        $entries = AccountingEntry::with('reference')->orderBy('created_at', 'desc')->paginate(50);
        return view('finance.transactions', compact('entries'));
    }
    
    public function showTransaction(AccountingEntry $entry)
    {
        $entry->load('reference');
        // Get all related entries with the same reference number
        $relatedEntries = AccountingEntry::with('reference')->where('reference_number', $entry->reference_number)->get();
        return view('finance.transaction-details', compact('entry', 'relatedEntries'));
    }

    public function exportTransactionPDF(AccountingEntry $entry)
    {
        $entry->load('reference');
        $relatedEntries = AccountingEntry::with('reference')->where('reference_number', $entry->reference_number)->get();
        
        $pdf = new Dompdf();
        $pdf->loadHtml(view('finance.transaction-pdf', compact('entry', 'relatedEntries'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        
        return $pdf->stream('transaction-' . $entry->reference_number . '.pdf');
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

    public function balanceSheet()
    {
        // Calculate Assets
        $cashBalance = 0;
        foreach (AccountingEntry::where('account', 'Cash')->get() as $entry) {
            if ($entry->type === 'debit') {
                $cashBalance += $entry->amount;
            } else {
                $cashBalance -= $entry->amount;
            }
        }

        $bankBalance = BankAccount::where('is_active', true)->sum('balance');
        $mobileMoneyBalance = MobileMoneyAccount::where('is_active', true)->sum('balance');
        $inventoryValue = 0;
        foreach (\App\Models\Product::all() as $product) {
            $inventoryValue += $product->quantity * ($product->cost_price ?? 0);
        }

        // Calculate Liabilities
        $accountsReceivable = 0;
        foreach (Sale::where('type', 'credit')->get() as $sale) {
            $accountsReceivable += ($sale->total - $sale->paid);
        }

        $accountsPayable = 0;
        foreach (PurchaseOrder::where('status', 'received')->get() as $po) {
            $paid = SupplierPayment::where('purchase_order_id', $po->id)->sum('amount');
            $accountsPayable += ($po->total - $paid);
        }

        // Calculate Equity
        $totalCapital = 0;
        foreach (Capital::all() as $capital) {
            if ($capital->transaction_type === 'add') {
                $totalCapital += $capital->amount;
            } else {
                $totalCapital -= $capital->amount;
            }
        }

        $totalIncome = Income::sum('amount');
        $totalExpenses = Expense::sum('amount');
        $retainedEarnings = $totalIncome - $totalExpenses;

        return view('finance.balance-sheet', compact(
            'cashBalance',
            'bankBalance',
            'mobileMoneyBalance',
            'inventoryValue',
            'accountsReceivable',
            'accountsPayable',
            'totalCapital',
            'retainedEarnings'
        ));
    }

    public function incomeStatement()
    {
        $totalSales = Sale::where('status', 'completed')->sum('total');
        $totalPurchases = PurchaseOrder::where('status', 'received')->sum('total');
        $grossProfit = $totalSales - $totalPurchases;
        
        $totalOperatingExpenses = Expense::sum('amount');
        $totalOtherIncome = Income::sum('amount');
        
        $operatingProfit = $grossProfit - $totalOperatingExpenses;
        $netProfit = $operatingProfit + $totalOtherIncome;

        return view('finance.income-statement', compact(
            'totalSales',
            'totalPurchases',
            'grossProfit',
            'totalOperatingExpenses',
            'totalOtherIncome',
            'operatingProfit',
            'netProfit'
        ));
    }

    public function generalLedger(Request $request)
    {
        $accounts = Account::where('is_active', true)->orderBy('account_code')->get();
        $selectedAccountId = $request->account_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($selectedAccountId) {
            $account = Account::findOrFail($selectedAccountId);
            $query = AccountingEntry::with('journalEntry')
                ->where(function($q) use ($account) {
                    $q->where('account_id', $account->id)
                      ->orWhere('account', $account->name);
                })
                ->orderBy('created_at', 'asc');

            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }

            $entries = $query->get();
            $balance = 0;
            $ledgerEntries = [];

            // Normal balance: 
            // Asset/Expense: debit = +, credit = -
            // Liability/Equity/Revenue: debit = -, credit = +
            $normalBalance = in_array($account->type, ['Asset', 'Expense']) ? 'debit' : 'credit';

            foreach ($entries as $entry) {
                if ($normalBalance === 'debit') {
                    if ($entry->type === 'debit') {
                        $balance += $entry->amount;
                    } else {
                        $balance -= $entry->amount;
                    }
                } else {
                    if ($entry->type === 'credit') {
                        $balance += $entry->amount;
                    } else {
                        $balance -= $entry->amount;
                    }
                }
                $ledgerEntries[] = [
                    'entry' => $entry,
                    'balance' => $balance,
                ];
            }

            return view('finance.general-ledger', compact('accounts', 'selectedAccountId', 'account', 'ledgerEntries', 'balance', 'startDate', 'endDate'));
        }

        return view('finance.general-ledger', compact('accounts', 'selectedAccountId', 'startDate', 'endDate'));
    }
}
