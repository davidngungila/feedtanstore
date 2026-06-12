<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class CancelledSaleController extends Controller {
    public function index() {
        $sales = Sale::where('status', 'cancelled')->with(['customer', 'user'])->orderBy('created_at', 'desc')->get();
        return view('sales.cancelled', compact('sales'));
    }
}
