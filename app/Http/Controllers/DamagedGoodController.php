<?php

namespace App\Http\Controllers;

use App\Models\DamagedGood;
use Illuminate\Http\Request;

class DamagedGoodController extends Controller
{
    public function index()
    {
        $damagedGoods = DamagedGood::with(['product', 'location'])->get();
        return view('inventory.damaged', compact('damagedGoods'));
    }
}
