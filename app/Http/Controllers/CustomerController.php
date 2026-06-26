<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\CustomerPayment;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $query = Customer::with('group');
        
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhereHas('group', function ($groupQuery) use ($search) {
                        $groupQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }
        
        $customers = $query->get();
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($customers);
        }
        
        return view('customers.list', compact('customers', 'search'));
    }

    public function create()
    {
        $groups = CustomerGroup::all();
        return view('customers.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->is('cashier*') || $request->wantsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $customer = Customer::create($request->all() + ['balance' => 0]);

        if ($request->expectsJson() || $request->is('cashier*') || $request->wantsJson() || $request->header('Content-Type') === 'application/json') {
            return response()->json(['success' => true, 'customer' => $customer]);
        }

        return redirect()->route('customers.list')->with('success', 'Customer created successfully!');
    }

    public function show(Customer $customer)
    {
        $customer->load('sales', 'payments', 'group', 'loyaltyPoints');
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $groups = CustomerGroup::all();
        return view('customers.edit', compact('customer', 'groups'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.list')->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.list')->with('success', 'Customer deleted successfully!');
    }

    public function credit()
    {
        $customers = Customer::where('balance', '>', 0)->with('sales')->get();
        return view('customers.credit', compact('customers'));
    }

    public function history(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $customers = Customer::with('sales', 'payments')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%');
                });
            })
            ->get();

        return view('customers.history', compact('customers', 'search'));
    }

    public function addPayment(Request $request, Customer $customer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $payment = CustomerPayment::create([
            'payment_number' => 'PAY-' . strtoupper(uniqid()),
            'customer_id' => $customer->id,
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        $customer->balance = max(0, $customer->balance - $request->amount);
        $customer->save();

        return back()->with('success', 'Payment recorded successfully!');
    }

    public function addLoyaltyPoints(Request $request, Customer $customer)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'type' => 'required|in:earned,redeemed',
            'notes' => 'nullable|string',
        ]);

        LoyaltyPoint::create([
            'customer_id' => $customer->id,
            'points' => $request->points,
            'type' => $request->type,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Loyalty points updated!');
    }

    public function loyalty()
    {
        $customers = Customer::with('loyaltyPoints')->get();
        return view('customers.loyalty', compact('customers'));
    }
}
