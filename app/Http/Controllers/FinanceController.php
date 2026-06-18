<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountingEntry;

class FinanceController extends Controller
{
    public function transactions()
    {
        $entries = AccountingEntry::orderBy('created_at', 'desc')->paginate(50);
        return view('finance.transactions', compact('entries'));
    }
}
