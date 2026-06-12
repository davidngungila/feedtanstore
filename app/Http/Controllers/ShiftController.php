<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller {
    public function index() {
        $shifts = Shift::with('user')->orderBy('created_at', 'desc')->get();
        $currentShift = Shift::where('user_id', Auth::id())->whereNull('closed_at')->first();
        return view('sales.shifts', compact('shifts', 'currentShift'));
    }

    public function open(Request $request) {
        $request->validate([
            'opening_cash' => 'required|numeric|min:0'
        ]);

        $existingShift = Shift::where('user_id', Auth::id())->whereNull('closed_at')->first();
        if ($existingShift) {
            return back()->with('error', 'You already have an open shift!');
        }

        Shift::create([
            'user_id' => Auth::id(),
            'opened_at' => now(),
            'opening_cash' => $request->opening_cash
        ]);

        return redirect()->route('sales.shifts')->with('success', 'Shift opened successfully!');
    }

    public function close(Request $request, Shift $shift) {
        $request->validate([
            'closing_cash' => 'required|numeric|min:0'
        ]);

        $sales = Sale::where('shift_id', $shift->id)->get();
        $cashSales = $sales->where('payment_method', 'cash')->sum('total');
        $cardSales = $sales->where('payment_method', 'card')->sum('total');
        $mobileSales = $sales->where('payment_method', 'mobile')->sum('total');

        $shift->update([
            'closed_at' => now(),
            'closing_cash' => $request->closing_cash,
            'cash_sales' => $cashSales,
            'card_sales' => $cardSales,
            'mobile_sales' => $mobileSales
        ]);

        return redirect()->route('sales.shifts')->with('success', 'Shift closed successfully!');
    }
}
