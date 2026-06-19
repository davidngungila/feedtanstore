<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JournalEntryController extends Controller
{
    public function index()
    {
        $journalEntries = JournalEntry::with(['items.account', 'postedBy'])->latest()->get();
        return view('finance.journal-entries', compact('journalEntries'));
    }

    public function create()
    {
        $accounts = Account::where('is_active', true)->orderBy('account_code')->get();
        return view('finance.journal-entry-create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entry_date' => 'required|date',
            'description' => 'required|string',
            'items' => 'required|array|min:2',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.type' => 'required|in:debit,credit',
            'items.*.amount' => 'required|numeric|min:0.01',
        ]);

        $totalDebits = 0;
        $totalCredits = 0;
        foreach ($request->items as $item) {
            if ($item['type'] === 'debit') {
                $totalDebits += $item['amount'];
            } else {
                $totalCredits += $item['amount'];
            }
        }

        if ($totalDebits !== $totalCredits) {
            return back()->withErrors(['items' => 'Total debits must equal total credits'])->withInput();
        }

        $entryNumber = 'JE-' . date('YmdHis');
        $journalEntry = JournalEntry::create([
            'entry_number' => $entryNumber,
            'entry_date' => $request->entry_date,
            'description' => $request->description,
        ]);

        foreach ($request->items as $item) {
            JournalEntryItem::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $item['account_id'],
                'type' => $item['type'],
                'amount' => $item['amount'],
                'description' => $item['description'] ?? null,
            ]);
        }

        return redirect()->route('finance.journal-entries')->with('success', 'Journal entry created successfully!');
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['items.account', 'postedBy']);
        return view('finance.journal-entry-show', compact('journalEntry'));
    }

    public function post(JournalEntry $journalEntry, Request $request)
    {
        if ($journalEntry->is_posted) {
            return back()->with('error', 'Journal entry is already posted!');
        }

        $journalEntry->update([
            'is_posted' => true,
            'posted_by' => Auth::id(),
        ]);

        // Create accounting entries for each item
        foreach ($journalEntry->items as $item) {
            \App\Models\AccountingEntry::create([
                'reference_number' => $journalEntry->entry_number,
                'reference_type' => 'journal_entry',
                'account' => $item->account->name,
                'account_id' => $item->account_id,
                'type' => $item->type,
                'amount' => $item->amount,
                'description' => $item->description ?? $journalEntry->description,
            ]);
        }

        return back()->with('success', 'Journal entry posted successfully!');
    }
}
