<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use App\Models\Share;
use App\Models\Capital;
use App\Models\AccountingEntry;
use App\Imports\ShareholderImport;
use App\Exports\ShareholderSampleExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ShareholderController extends Controller
{
    public function index()
    {
        $shareholders = Shareholder::with('shares')->get();
        $totalShares = Share::sum('number_of_shares');
        $totalInvestment = Share::sum('total_amount');
        
        return view('finance.shareholders', compact('shareholders', 'totalShares', 'totalInvestment'));
    }

    public function create()
    {
        return view('finance.shareholder-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        Shareholder::create($request->all());

        return redirect()->route('finance.shareholders')->with('success', 'Shareholder added successfully!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new ShareholderImport();
            Excel::import($import, $request->file('file'));
            $count = $import->getImportedCount();
            
            return redirect()->route('finance.shareholders')->with('success', "Successfully imported {$count} shareholders!");
        } catch (\Exception $e) {
            return redirect()->route('finance.shareholders')->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadSample()
    {
        return Excel::download(new ShareholderSampleExport(), 'shareholders_sample.xlsx');
    }

    public function show(Shareholder $shareholder)
    {
        return view('finance.shareholder-show', compact('shareholder'));
    }

    public function edit(Shareholder $shareholder)
    {
        return view('finance.shareholder-edit', compact('shareholder'));
    }

    public function update(Request $request, Shareholder $shareholder)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $shareholder->update($request->all());

        return redirect()->route('finance.shareholders')->with('success', 'Shareholder updated successfully!');
    }

    public function destroy(Shareholder $shareholder)
    {
        $shareholder->delete();
        return redirect()->route('finance.shareholders')->with('success', 'Shareholder deleted successfully!');
    }

    // Share management
    public function addShare(Shareholder $shareholder)
    {
        $storeSettings = \App\Models\StoreSetting::first();
        return view('finance.share-add', compact('shareholder', 'storeSettings'));
    }

    public function storeShare(Request $request, Shareholder $shareholder)
    {
        $request->validate([
            'number_of_shares' => 'required|integer|min:1',
            'share_price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $totalAmount = $request->number_of_shares * $request->share_price;

        $share = $shareholder->shares()->create([
            'number_of_shares' => $request->number_of_shares,
            'share_price' => $request->share_price,
            'total_amount' => $totalAmount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        // Create capital entry
        $capital = Capital::create([
            'amount' => $totalAmount,
            'description' => $request->description ?? "Shares issued to {$shareholder->name}",
            'transaction_type' => 'add',
            'date' => $request->date,
            'user_id' => Auth::id(),
        ]);

        // Create accounting entries
        $this->createCapitalAccountingEntries($capital);

        return redirect()->route('finance.shareholders.show', $shareholder)->with('success', 'Shares added successfully!');
    }

    protected function createCapitalAccountingEntries(Capital $capital)
    {
        $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
        $capitalAccount = \App\Models\Account::where('name', 'Capital')->first();

        $journalNumber = 'JE-SHARE-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Shares issued',
            'reference_type' => Capital::class,
            'reference_id' => $capital->id,
            'is_manual' => false,
        ]);

        if ($capital->transaction_type === 'add') {
            // Debit cash/bank, credit capital
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => 'CAP-' . $capital->id,
                'reference_type' => Capital::class,
                'account' => 'Cash',
                'account_id' => $cashAccount?->id,
                'type' => 'debit',
                'amount' => $capital->amount,
                'description' => $capital->description ?: 'Capital added',
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => 'CAP-' . $capital->id,
                'reference_type' => Capital::class,
                'account' => 'Capital',
                'account_id' => $capitalAccount?->id,
                'type' => 'credit',
                'amount' => $capital->amount,
                'description' => $capital->description ?: 'Capital added',
            ]);
        }
    }
}
