<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalEntry;
use App\Models\AccountingEntry;
use App\Models\Account;

class JournalEntryController extends Controller
{
    public function index()
    {
        $journalEntries = JournalEntry::with('entries.accountModel')->orderBy('entry_date', 'desc')->orderBy('created_at', 'desc')->paginate(20);
        return view('finance.journal-entries', compact('journalEntries'));
    }

    public function create()
    {
        $accounts = Account::where('is_active', true)->get();
        return view('finance.journal-entries-create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entry_date' => 'required|date',
            'description' => 'required',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.type' => 'required|in:debit,credit',
            'lines.*.amount' => 'required|numeric|min:0.01',
            'lines.*.description' => 'nullable',
        ]);

        $totalDebits = collect($request->lines)->where('type', 'debit')->sum('amount');
        $totalCredits = collect($request->lines)->where('type', 'credit')->sum('amount');

        if ($totalDebits != $totalCredits) {
            return back()->withErrors(['total' => 'Total debits must equal total credits.'])->withInput();
        }

        $journalNumber = 'JE-' . date('Ymd') . '-' . str_pad(JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        $journalEntry = JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_date' => $request->entry_date,
            'description' => $request->description,
            'is_manual' => true,
        ]);

        foreach ($request->lines as $line) {
            $account = Account::find($line['account_id']);
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'account' => $account->name,
                'account_id' => $line['account_id'],
                'type' => $line['type'],
                'amount' => $line['amount'],
                'description' => $line['description'] ?? $request->description,
            ]);
        }

        return redirect()->route('journal-entries.index')->with('success', 'Journal entry created successfully.');
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load('entries.accountModel');
        return view('finance.journal-entries-show', compact('journalEntry'));
    }
}
