<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class CreditSaleController extends Controller {
    public function index() {
        $sales = Sale::where('type', 'credit')->with(['customer', 'user'])->orderBy('created_at', 'desc')->get();
        return view('sales.credit', compact('sales'));
    }
}
