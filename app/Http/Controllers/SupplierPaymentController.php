<?php

namespace App\Http\Controllers;

use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Dompdf\Dompdf;

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

        $payment = SupplierPayment::create([
            'payment_number' => $paymentNumber,
            'supplier_id' => $request->supplier_id,
            'purchase_order_id' => $request->purchase_order_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'status' => 'completed',
        ]);

        $this->createAccountingEntries($payment);

        return redirect()->route('purchasing.payments')->with('success', 'Supplier Payment created successfully!');
    }
    
    protected function createAccountingEntries(SupplierPayment $payment)
    {
        $accountsPayableAccount = \App\Models\Account::where('name', 'Accounts Payable')->first();
        $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
        $bankAccount = \App\Models\Account::where('name', 'Bank Account')->first();
        $mobileMoneyAccount = \App\Models\Account::where('name', 'Mobile Money')->first();

        $journalNumber = 'JE-SUP-PAY-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Supplier Payment: ' . $payment->payment_number,
            'reference_type' => SupplierPayment::class,
            'reference_id' => $payment->id,
            'is_manual' => false,
        ]);

        // Debit Accounts Payable
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $payment->payment_number,
            'reference_type' => SupplierPayment::class,
            'account' => 'Accounts Payable',
            'account_id' => $accountsPayableAccount?->id,
            'type' => 'debit',
            'amount' => $payment->amount,
            'description' => 'Supplier payment'
        ]);
        
        // Credit the payment source
        $accountName = 'Cash';
        $accountId = $cashAccount?->id;
        if (in_array($payment->payment_method, ['bank_transfer', 'card'])) {
            $accountName = 'Bank Account';
            $accountId = $bankAccount?->id;
        } elseif ($payment->payment_method == 'mobile_money') {
            $accountName = 'Mobile Money';
            $accountId = $mobileMoneyAccount?->id;
        }
        
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $payment->payment_number,
            'reference_type' => SupplierPayment::class,
            'account' => $accountName,
            'account_id' => $accountId,
            'type' => 'credit',
            'amount' => $payment->amount,
            'description' => 'Supplier payment made'
        ]);
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
            'transaction_id' => 'required_if:payment_method,card,bank_transfer,mobile_money|string',
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

    public function downloadPDF(SupplierPayment $payment)
    {
        $payment->load(['supplier', 'purchaseOrder']);
        $pdf = new Dompdf();
        $pdf->loadHtml(view('purchasing.payments-pdf', compact('payment'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        return $pdf->stream($payment->payment_number . '.pdf');
    }
}
