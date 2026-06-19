<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::with(['accountingEntries' => function ($query) {
            $query->orderBy('created_at');
        }])->where('is_active', true)->orderBy('account_code')->get();

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        if ($dateFrom && $dateTo) {
            $accounts = Account::with(['accountingEntries' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo])->orderBy('created_at');
            }])->where('is_active', true)->orderBy('account_code')->get();
        }

        return view('finance.general-ledger', compact('accounts', 'dateFrom', 'dateTo'));
    }
}
