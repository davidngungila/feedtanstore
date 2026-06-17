<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VFDService;

class VFDController extends Controller
{
    protected $vfdService;

    public function __construct(VFDService $vfdService)
    {
        $this->vfdService = $vfdService;
    }

    /**
     * Send welcome message to VFD
     */
    public function welcome()
    {
        $this->vfdService->displayWelcome();
        return response()->json(['success' => true]);
    }

    /**
     * Send product info to VFD
     */
    public function product(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'total' => 'required|numeric'
        ]);

        $this->vfdService->displayProduct(
            $request->name,
            $request->quantity,
            $request->price,
            $request->total
        );

        return response()->json(['success' => true]);
    }

    /**
     * Send payment info to VFD
     */
    public function payment(Request $request)
    {
        $request->validate([
            'total' => 'required|numeric',
            'paid' => 'required|numeric',
            'change' => 'required|numeric',
            'payment_method' => 'required|string'
        ]);

        $this->vfdService->displayPayment(
            $request->total,
            $request->paid,
            $request->change,
            $request->payment_method
        );

        return response()->json(['success' => true]);
    }

    /**
     * Send thank you message to VFD
     */
    public function thankYou()
    {
        $this->vfdService->displayThankYou();
        return response()->json(['success' => true]);
    }
}
