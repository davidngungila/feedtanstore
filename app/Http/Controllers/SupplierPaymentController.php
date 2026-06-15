<?php

namespace App\Http\Controllers;

use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function index()
    {
        $payments = SupplierPayment::with(['supplier', 'purchaseOrder'])->get();
        return view('purchasing.payments', compact('payments'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $purchaseOrders = PurchaseOrder::all();
        return view('purchasing.payments-create', compact('suppliers', 'purchaseOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $paymentNumber = 'PAY-' . date('YmdHis');

        SupplierPayment::create([
            'payment_number' => $paymentNumber,
            'supplier_id' => $request->supplier_id,
            'purchase_order_id' => $request->purchase_order_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'status' => 'completed',
        ]);

        return redirect()->route('purchasing.payments')->with('success', 'Supplier Payment created successfully!');
    }

    public function show(SupplierPayment $payment)
    {
        $payment->load(['supplier', 'purchaseOrder']);
        return view('purchasing.payments-show', compact('payment'));
    }

    public function edit(SupplierPayment $payment)
    {
        $suppliers = Supplier::all();
        $purchaseOrders = PurchaseOrder::all();
        return view('purchasing.payments-edit', compact('payment', 'suppliers', 'purchaseOrders'));
    }

    public function update(Request $request, SupplierPayment $payment)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment->update($request->all());

        return redirect()->route('purchasing.payments')->with('success', 'Supplier Payment updated successfully!');
    }

    public function destroy(SupplierPayment $payment)
    {
        $payment->delete();
        return redirect()->route('purchasing.payments')->with('success', 'Supplier Payment deleted successfully!');
    }
}
