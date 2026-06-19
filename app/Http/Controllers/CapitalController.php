<?php

namespace App\Http\Controllers;

use App\Models\Capital;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CapitalController extends Controller
{
    public function index()
    {
        $capitals = Capital::with('user')->latest()->get();
        return view('finance.capital', compact('capitals'));
    }

    public function create()
    {
        return view('finance.capital-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'transaction_type' => 'required|in:add,withdraw',
            'date' => 'required|date',
        ]);

        $capital = Capital::create([
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_type' => $request->transaction_type,
            'date' => $request->date,
            'user_id' => Auth::id(),
        ]);

        $this->createAccountingEntries($capital);

        return redirect()->route('finance.capital')->with('success', 'Capital transaction recorded successfully!');
    }

    public function show(Capital $capital)
    {
        return view('finance.capital-show', compact('capital'));
    }

    public function edit(Capital $capital)
    {
        return view('finance.capital-edit', compact('capital'));
    }

    public function update(Request $request, Capital $capital)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'transaction_type' => 'required|in:add,withdraw',
            'date' => 'required|date',
        ]);

        // Reverse old accounting entries
        $this->reverseAccountingEntries($capital);

        $capital->update([
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_type' => $request->transaction_type,
            'date' => $request->date,
        ]);

        $this->createAccountingEntries($capital);

        return redirect()->route('finance.capital')->with('success', 'Capital transaction updated successfully!');
    }

    public function destroy(Capital $capital)
    {
        $this->reverseAccountingEntries($capital);
        $capital->delete();
        return redirect()->route('finance.capital')->with('success', 'Capital transaction deleted successfully!');
    }

    protected function createAccountingEntries(Capital $capital)
    {
        $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
        $capitalAccount = \App\Models\Account::where('name', 'Capital')->first();

        if ($capital->transaction_type === 'add') {
            // Debit cash/bank, credit capital
            AccountingEntry::create([
                'reference_number' => 'CAP-' . $capital->id,
                'reference_type' => Capital::class,
                'account' => 'Cash',
                'account_id' => $cashAccount?->id,
                'type' => 'debit',
                'amount' => $capital->amount,
                'description' => $capital->description ?: 'Capital added',
            ]);

            AccountingEntry::create([
                'reference_number' => 'CAP-' . $capital->id,
                'reference_type' => Capital::class,
                'account' => 'Capital',
                'account_id' => $capitalAccount?->id,
                'type' => 'credit',
                'amount' => $capital->amount,
                'description' => $capital->description ?: 'Capital added',
            ]);
        } else {
            // Debit capital, credit cash/bank
            AccountingEntry::create([
                'reference_number' => 'CAP-' . $capital->id,
                'reference_type' => Capital::class,
                'account' => 'Capital',
                'account_id' => $capitalAccount?->id,
                'type' => 'debit',
                'amount' => $capital->amount,
                'description' => $capital->description ?: 'Capital withdrawn',
            ]);

            AccountingEntry::create([
                'reference_number' => 'CAP-' . $capital->id,
                'reference_type' => Capital::class,
                'account' => 'Cash',
                'account_id' => $cashAccount?->id,
                'type' => 'credit',
                'amount' => $capital->amount,
                'description' => $capital->description ?: 'Capital withdrawn',
            ]);
        }
    }

    protected function reverseAccountingEntries(Capital $capital)
    {
        $oldEntries = AccountingEntry::where('reference_type', Capital::class)
            ->where('reference_number', 'CAP-' . $capital->id)
            ->get();

        foreach ($oldEntries as $entry) {
            $account = \App\Models\Account::where('name', $entry->account)->first();

            AccountingEntry::create([
                'reference_number' => $entry->reference_number . '-REV',
                'reference_type' => Capital::class,
                'account' => $entry->account,
                'account_id' => $account?->id,
                'type' => $entry->type === 'debit' ? 'credit' : 'debit',
                'amount' => $entry->amount,
                'description' => 'Reversal: ' . $entry->description,
            ]);
        }

        $oldEntries->each->delete();
    }
}
