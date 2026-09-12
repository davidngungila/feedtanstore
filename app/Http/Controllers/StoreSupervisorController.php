<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\TransactionIssue;
use App\Models\CustomerDemand;
use App\Models\CompetitorIntelligence;
use App\Models\StockVerificationSession;
use App\Models\CustomerRating;
use App\Models\OfflineTransaction;
use App\Models\CashierServiceTime;
use App\Models\Product;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreSupervisorController extends Controller
{
    private function ensureSupervisor(): void
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['admin','manager','store_supervisor'], true)) {
            abort(403, 'Only Store Supervisor, Manager or Admin can access this area.');
        }
    }

    public function dashboard(Request $request)
    {
        $this->ensureSupervisor();
        $branchId = Auth::user()->branch_id ?? null; // if you have branch assignment, optional

        $stats = [
            'open_issues' => TransactionIssue::where('status','open')->count(),
            'investigating_issues' => TransactionIssue::where('status','investigating')->count(),
            'pending_returns' => SaleReturn::where('approval_status','pending')->count(),
            'pending_verifications' => StockVerificationSession::whereIn('status',['submitted','under_review'])->count(),
            'pending_demands' => CustomerDemand::where('status','new')->count(),
            'recent_ratings_avg' => CustomerRating::avg('rating'),
            'low_stock' => Product::whereColumn('quantity','<=','reorder_level')->count(),
            'pending_offline' => OfflineTransaction::where('sync_status','pending')->count(),
            'failed_offline' => OfflineTransaction::where('sync_status','failed')->count(),
            'today_sales' => Sale::whereDate('created_at', today())->where('status','completed')->sum('total'),
            'today_returns' => SaleReturn::whereDate('created_at', today())->sum('total'),
            'total_demands' => CustomerDemand::count(),
            'total_competitor' => CompetitorIntelligence::count(),
        ];

        $recentIssues = TransactionIssue::with(['reporter','assignee','sale'])->latest()->limit(8)->get();
        $pendingReturns = SaleReturn::with(['sale','user'])->where('approval_status','pending')->latest()->limit(5)->get();
        $pendingVerifications = StockVerificationSession::with(['auditor','branch'])->whereIn('status',['submitted','under_review'])->latest()->limit(5)->get();
        $recentDemands = CustomerDemand::with(['customer','staff'])->latest()->limit(5)->get();
        $offlineQueue = OfflineTransaction::latest()->limit(5)->get();
        $cashiers = User::where('role','cashier')->get();
        // Cashier performance quick snapshot
        $cashierPerf = CashierServiceTime::selectRaw('cashier_id, AVG(duration_seconds) as avg_duration, COUNT(*) as cnt')
            ->where('status','completed')->whereDate('service_start_time', today())->groupBy('cashier_id')->with('cashier')->get();

        return view('store-supervisor.dashboard', compact('stats','recentIssues','pendingReturns','pendingVerifications','recentDemands','offlineQueue','cashierPerf','cashiers'));
    }

    // Unified issues inbox
    public function issues(Request $request)
    {
        $this->ensureSupervisor();
        $q = TransactionIssue::with(['sale','reporter','assignee'])->latest();
        if ($request->filled('status')) $q->where('status', $request->status);
        if ($request->filled('issue_type')) $q->where('issue_type', $request->issue_type);
        if ($request->filled('search')) {
            $s=$request->search;
            $q->where(function($w) use($s){ $w->where('issue_number','like',"%$s%")->orWhere('description','like',"%$s%"); });
        }
        $issues = $q->paginate(20)->withQueryString();
        $users = User::whereIn('role',['admin','manager','store_supervisor','cashier'])->orderBy('name')->get();
        return view('store-supervisor.issues', compact('issues','users'));
    }

    public function updateIssue(Request $request, TransactionIssue $issue)
    {
        $this->ensureSupervisor();
        $data=$request->validate([
            'status'=>'nullable|in:open,investigating,resolved,closed',
            'assigned_to'=>'nullable|exists:users,id',
            'resolution'=>'nullable|string|max:2000',
        ]);
        $old=$issue->toArray();
        $allowed=[
            'open'=>['investigating'],
            'investigating'=>['resolved'],
            'resolved'=>['closed'],
            'closed'=>[],
        ];
        if (isset($data['status']) && $data['status']!==$issue->status) {
            if (!in_array($data['status'], $allowed[$issue->status]??[], true)) {
                return back()->with('error','Invalid status transition from '.$issue->status.' to '.$data['status']);
            }
            if ($data['status']==='resolved') { $data['resolved_by']=Auth::id(); $data['resolved_at']=now(); }
        }
        $issue->update($data);
        AuditService::log('update_transaction_issue','audit', TransactionIssue::class, $issue->id, $old, $issue->fresh()->toArray(), 'Supervisor updated issue '.$issue->issue_number);
        return back()->with('success','Issue updated');
    }

    public function approveReturn(Request $request, SaleReturn $return)
    {
        $this->ensureSupervisor();
        if ($return->approval_status==='approved') return back()->with('error','Already approved');
        DB::transaction(function() use ($return){
            $return->update(['approval_status'=>'approved','approver_id'=>Auth::id(),'approved_at'=>now()]);
            $cogs=0;
            foreach ($return->items as $it){
                $saleItem=\App\Models\SaleItem::find($it->sale_item_id);
                $product=Product::find($saleItem->product_id);
                $product->increment('quantity',$it->quantity);
                $cogs+= $it->quantity * ($product->cost_price ?? 0);
                \App\Models\StockMovement::create(['product_id'=>$saleItem->product_id,'movement_type'=>'return','quantity'=>$it->quantity,'reference_type'=>SaleReturn::class,'reference_id'=>$return->id,'user_id'=>Auth::id(),'notes'=>'Supervisor approved return '.$return->return_number]);
            }
            $return->update(['stock_updated'=>true]);
            $this->createReturnAccounting($return,$cogs);
            AuditService::log('approve_sale_return','audit', SaleReturn::class, $return->id, ['approval_status'=>'pending'], ['approval_status'=>'approved'], 'Supervisor approved '.$return->return_number);
        });
        return back()->with('success','Return approved and stock updated');
    }

    private function createReturnAccounting(SaleReturn $saleReturn, $returnItemsTotal): void
    {
        $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
        $salesAccount = \App\Models\Account::where('name', 'Sales')->first();
        $inventoryAccount = \App\Models\Account::where('name', 'Inventory')->first();
        $cogsAccount = \App\Models\Account::where('name', 'Cost of Goods Sold')->first();
        $journalNumber = 'JE-RET-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);
        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Sale Return: ' . $saleReturn->return_number,
            'reference_type' => SaleReturn::class,
            'reference_id' => $saleReturn->id,
            'is_manual' => false,
        ]);
        \App\Models\AccountingEntry::create(['journal_entry_id'=>$journalEntry->id,'reference_number'=>$saleReturn->return_number,'reference_type'=>SaleReturn::class,'account'=>'Sales','account_id'=>$salesAccount?->id,'type'=>'debit','amount'=>$saleReturn->total,'description'=>'Sale return']);
        \App\Models\AccountingEntry::create(['journal_entry_id'=>$journalEntry->id,'reference_number'=>$saleReturn->return_number,'reference_type'=>SaleReturn::class,'account'=>'Cash','account_id'=>$cashAccount?->id,'type'=>'credit','amount'=>$saleReturn->total,'description'=>'Sale return refund']);
        \App\Models\AccountingEntry::create(['journal_entry_id'=>$journalEntry->id,'reference_number'=>$saleReturn->return_number,'reference_type'=>SaleReturn::class,'account'=>'Inventory','account_id'=>$inventoryAccount?->id,'type'=>'debit','amount'=>$returnItemsTotal,'description'=>'Inventory returned']);
        \App\Models\AccountingEntry::create(['journal_entry_id'=>$journalEntry->id,'reference_number'=>$saleReturn->return_number,'reference_type'=>SaleReturn::class,'account'=>'Cost of Goods Sold','account_id'=>$cogsAccount?->id,'type'=>'credit','amount'=>$returnItemsTotal,'description'=>'COGS reversed']);
    }

    public function rejectReturn(Request $request, SaleReturn $return)
    {
        $this->ensureSupervisor();
        $request->validate(['reason'=>'required|string|max:1000']);
        $return->update(['approval_status'=>'rejected','approver_id'=>Auth::id(),'approved_at'=>now()]);
        AuditService::log('reject_sale_return','audit', SaleReturn::class, $return->id, ['approval_status'=>'pending'], ['approval_status'=>'rejected'], 'Supervisor rejected '.$return->return_number);
        return back()->with('success','Return rejected');
    }

    public function demands(Request $request)
    {
        $this->ensureSupervisor();
        $q = CustomerDemand::with(['customer','staff','branch'])->latest();
        if ($request->filled('status')) $q->where('status',$request->status);
        $demands=$q->paginate(20);
        return view('store-supervisor.demands', compact('demands'));
    }

    public function updateDemand(Request $request, CustomerDemand $demand)
    {
        $this->ensureSupervisor();
        $request->validate(['status'=>'required|in:new,reviewing,planned,ordered,available,closed']);
        $old=$demand->status;
        $demand->update(['status'=>$request->status]);
        AuditService::log('update_customer_demand','customer_demand', CustomerDemand::class, $demand->id, ['status'=>$old], ['status'=>$request->status], 'Supervisor updated demand');
        return back()->with('success','Demand status updated');
    }

    public function verifications(Request $request)
    {
        $this->ensureSupervisor();
        $sessions=StockVerificationSession::with(['auditor','branch'])->whereIn('status',['submitted','under_review','approved','rejected'])->latest()->paginate(20);
        return view('store-supervisor.verifications', compact('sessions'));
    }

    public function competitorReports(Request $request)
    {
        $this->ensureSupervisor();
        $intels=CompetitorIntelligence::with(['product','salesRep'])->latest()->paginate(20);
        return view('store-supervisor.competitors', compact('intels'));
    }
}
