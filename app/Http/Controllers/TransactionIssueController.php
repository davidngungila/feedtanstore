<?php

namespace App\Http\Controllers;

use App\Models\TransactionIssue;
use App\Models\Sale;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionIssueController extends Controller
{
    public function index(Request $request)
    {
        $q = TransactionIssue::with(['sale','reporter','assignee'])->latest();
        if ($request->filled('status')) $q->where('status',$request->status);
        if ($request->filled('issue_type')) $q->where('issue_type',$request->issue_type);
        if ($request->filled('search')) {
            $s=$request->search;
            $q->where(function($w) use($s){ $w->where('issue_number','like',"%$s%")->orWhere('description','like',"%$s%"); });
        }
        $issues=$q->paginate(20)->withQueryString();
        return view('transaction-issues.index', compact('issues'));
    }

    public function create(Request $request)
    {
        $sale = null;
        if ($request->filled('sale_id')) $sale = Sale::find($request->sale_id);
        return view('transaction-issues.create', compact('sale'));
    }

    public function store(Request $request)
    {
        $data=$request->validate([
            'sale_id'=>'nullable|exists:sales,id',
            'online_order_id'=>'nullable|exists:online_orders,id',
            'transaction_type'=>'nullable|in:sale,online_order',
            'issue_type'=>'required|in:wrong_quantity,wrong_product,wrong_price,payment_problem,duplicate_transaction,customer_complaint,product_damaged,failed_transaction,receipt_problem,other',
            'description'=>'required|string|max:2000',
            'attachment'=>'nullable|image|max:4096',
            'assigned_to'=>'nullable|exists:users,id',
        ]);
        if ($request->hasFile('attachment')) $data['attachment']=$request->file('attachment')->store('issues','public');
        $data['issue_number']=TransactionIssue::generateNumber();
        $data['reporter_id']=Auth::id();
        $data['reported_at']=now();
        $data['status']='open';
        if (!empty($data['sale_id'])) {
            $sale=Sale::find($data['sale_id']);
            $data['transaction_reference']=$sale->invoice_number;
            $data['transaction_type']='sale';
        }
        $issue=TransactionIssue::create($data);
        AuditService::log('create_transaction_issue','transaction_issue', TransactionIssue::class, $issue->id, null, $issue->toArray(), 'Issue '.$issue->issue_number);
        if ($request->expectsJson()) return response()->json($issue,201);
        return redirect()->route('transaction-issues.show',$issue)->with('success','Issue reported');
    }

    public function show(TransactionIssue $issue)
    {
        $issue->load(['sale','onlineOrder','reporter','assignee','resolver']);
        return view('transaction-issues.show', compact('issue'));
    }

    public function update(Request $request, TransactionIssue $issue)
    {
        $data=$request->validate([
            'status'=>'nullable|in:open,investigating,resolved,closed',
            'assigned_to'=>'nullable|exists:users,id',
            'resolution'=>'nullable|string|max:2000',
        ]);
        $old=$issue->toArray();
        // Enforce workflow: Open -> Investigating -> Resolved -> Closed
        if (isset($data['status'])) {
            $allowed=[
                'open'=>['investigating'],
                'investigating'=>['resolved'],
                'resolved'=>['closed'],
                'closed'=>[],
            ];
            if ($data['status']!==$issue->status && !in_array($data['status'], $allowed[$issue->status]??[], true)) {
                return back()->with('error','Invalid status transition from '.$issue->status.' to '.$data['status']);
            }
            if ($data['status']==='resolved') {
                $data['resolved_by']=Auth::id();
                $data['resolved_at']=now();
            }
        }
        $issue->update($data);
        AuditService::log('update_transaction_issue','transaction_issue', TransactionIssue::class, $issue->id, $old, $issue->fresh()->toArray(), 'Updated issue '.$issue->issue_number);
        return back()->with('success','Issue updated');
    }

    public function apiStore(Request $request)
    {
        return $this->store($request);
    }
}
