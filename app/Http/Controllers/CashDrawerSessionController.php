<?php

namespace App\Http\Controllers;

use App\Models\CashDrawerSession;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashDrawerSessionController extends Controller
{
    public function index()
    {
        $sessions = CashDrawerSession::with('user', 'reconciler')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('cash-drawer-sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('cash-drawer-sessions.open');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cash_balance' => 'required|numeric|min:0',
            'mobile_balance' => 'required|numeric|min:0',
            'bank_balance' => 'required|numeric|min:0',
            'online_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Check if user already has an open session
        $existingSession = CashDrawerSession::where('user_id', Auth::id())
            ->where('status', 'opened')
            ->first();

        if ($existingSession) {
            return back()->with('error', 'You already have an open cash drawer session');
        }

        $totalOpeningBalance = $request->cash_balance + $request->mobile_balance + $request->bank_balance + $request->online_balance;

        CashDrawerSession::create([
            'session_number' => CashDrawerSession::generateSessionNumber(),
            'user_id' => Auth::id(),
            'opening_balance' => $totalOpeningBalance,
            'cash_balance' => $request->cash_balance,
            'mobile_balance' => $request->mobile_balance,
            'bank_balance' => $request->bank_balance,
            'online_balance' => $request->online_balance,
            'opened_at' => now(),
            'status' => 'opened',
            'notes' => $request->notes,
        ]);

        return redirect()->route('cashier.dashboard')
            ->with('success', 'Cash drawer opened successfully');
    }

    public function show(CashDrawerSession $cashDrawerSession)
    {
        $cashDrawerSession->load(['user', 'reconciler', 'sales']);
        $totalCashSales = Sale::where('cash_drawer_session_id', $cashDrawerSession->id)
            ->where('payment_method', 'cash')
            ->sum('total');
        $session = $cashDrawerSession;
        return view('cash-drawer-sessions.show', compact('session', 'totalCashSales'));
    }

    public function editClose(CashDrawerSession $cashDrawerSession)
    {
        if ($cashDrawerSession->status !== 'opened') {
            return back()->with('error', 'This session is already closed');
        }

        if ($cashDrawerSession->user_id !== Auth::id()) {
            return back()->with('error', 'You can only close your own sessions');
        }

        $session = $cashDrawerSession;

        return view('cash-drawer-sessions.close', compact('session'));
    }

    public function close(Request $request, CashDrawerSession $cashDrawerSession)
    {
        if ($cashDrawerSession->status !== 'opened') {
            return back()->with('error', 'This session is already closed');
        }

        if ($cashDrawerSession->user_id !== Auth::id()) {
            return back()->with('error', 'You can only close your own sessions');
        }

        $request->validate([
            'closing_cash_balance' => 'required|numeric|min:0',
            'closing_mobile_balance' => 'required|numeric|min:0',
            'closing_bank_balance' => 'required|numeric|min:0',
            'closing_online_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate expected balances by payment method
        $totalCashSales = Sale::where('cash_drawer_session_id', $cashDrawerSession->id)
            ->where('payment_method', 'cash')
            ->sum('total');
        
        $totalMobileSales = Sale::where('cash_drawer_session_id', $cashDrawerSession->id)
            ->where('payment_method', 'mobile')
            ->sum('total');
        
        $totalCardSales = Sale::where('cash_drawer_session_id', $cashDrawerSession->id)
            ->where('payment_method', 'card')
            ->sum('total');
        
        $totalOnlineSales = Sale::where('cash_drawer_session_id', $cashDrawerSession->id)
            ->where('payment_method', 'online')
            ->sum('total');

        $expectedCashBalance = $cashDrawerSession->cash_balance + $totalCashSales;
        $expectedMobileBalance = $cashDrawerSession->mobile_balance + $totalMobileSales;
        $expectedBankBalance = $cashDrawerSession->bank_balance + $totalCardSales;
        $expectedOnlineBalance = $cashDrawerSession->online_balance + $totalOnlineSales;
        
        $totalExpectedBalance = $expectedCashBalance + $expectedMobileBalance + $expectedBankBalance + $expectedOnlineBalance;
        $totalClosingBalance = $request->closing_cash_balance + $request->closing_mobile_balance + $request->closing_bank_balance + $request->closing_online_balance;
        $totalDifference = $totalClosingBalance - $totalExpectedBalance;

        $cashDrawerSession->update([
            'closing_balance' => $totalClosingBalance,
            'closed_at' => now(),
            'expected_balance' => $totalExpectedBalance,
            'difference' => $totalDifference,
            'status' => 'closed',
            'notes' => $request->notes,
        ]);

        return redirect()->route('cash-drawer-sessions.show', $cashDrawerSession)
            ->with('success', 'Cash drawer closed. Please wait for manager reconciliation.');
    }

    public function reconcile(Request $request, CashDrawerSession $cashDrawerSession)
    {
        if ($cashDrawerSession->status !== 'closed') {
            return back()->with('error', 'This session must be closed before reconciliation');
        }

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $cashDrawerSession->update([
            'status' => 'reconciled',
            'reconciled_by' => Auth::id(),
            'reconciled_at' => now(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Cash drawer session reconciled successfully. Cashier can now logout.');
    }

    public function generateReport(CashDrawerSession $cashDrawerSession)
    {
        $cashDrawerSession->load(['user', 'reconciler', 'sales']);
        $totalCashSales = Sale::where('cash_drawer_session_id', $cashDrawerSession->id)
            ->where('payment_method', 'cash')
            ->sum('total');
        $totalCardSales = Sale::where('cash_drawer_session_id', $cashDrawerSession->id)
            ->where('payment_method', 'card')
            ->sum('total');
        $totalMobileSales = Sale::where('cash_drawer_session_id', $cashDrawerSession->id)
            ->where('payment_method', 'mobile')
            ->sum('total');
        
        return view('cash-drawer-sessions.report', compact(
            'cashDrawerSession',
            'totalCashSales',
            'totalCardSales',
            'totalMobileSales'
        ));
    }

    public function getActiveSession()
    {
        $session = CashDrawerSession::where('user_id', Auth::id())
            ->where('status', 'opened')
            ->first();

        return response()->json([
            'has_active_session' => $session !== null,
            'session' => $session
        ]);
    }
}
