<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Branch;
use App\Models\Location;
use App\Models\StockMovement;
use App\Models\StockAdjustment;
use App\Models\StockVerificationSession;
use App\Models\StockVerificationItem;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockVerificationController extends Controller
{
    private function ensureCanManage(): void
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['admin','manager','inventory_manager','store_supervisor'], true)) {
            abort(403, 'Only management / Store Supervisor can manage stock verification sessions');
        }
    }

    // Management: list all sessions
    public function index(Request $request)
    {
        $role = Auth::user()->role;
        if ($role === 'stock_auditor') {
            // Auditors see only their assigned sessions, blind view
            $sessions = StockVerificationSession::where('assigned_auditor_id', Auth::id())
                ->withCount('items')
                ->latest()->paginate(20);
            return view('stock-verification.auditor-index', compact('sessions'));
        }
        if ($role === 'external_auditor') {
            $sessions = StockVerificationSession::with(['branch','location','auditor'])->latest()->paginate(20);
            return view('stock-verification.external-index', compact('sessions'));
        }
        $this->ensureCanManage();
        $sessions = StockVerificationSession::with(['branch','location','auditor','creator'])->latest()->paginate(20);
        return view('stock-verification.index', compact('sessions'));
    }

    public function create()
    {
        $this->ensureCanManage();
        $branches = Branch::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->get();
        $auditors = \App\Models\User::where('role','stock_auditor')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock-verification.create', compact('branches','locations','auditors','products'));
    }

    public function store(Request $request)
    {
        $this->ensureCanManage();
        $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'location_id' => 'nullable|exists:locations,id',
            'assigned_auditor_id' => 'nullable|exists:users,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_monthly_audit' => 'nullable|boolean',
            'audit_month' => 'nullable|date',
        ]);
        // Default: include ALL active products if none selected – ensures auditor sees full catalog without numbers
        $productIds = $request->input('product_ids');
        if (empty($productIds)) {
            $productIds = Product::where('is_active', true)->pluck('id')->toArray();
            if (empty($productIds)) {
                return back()->with('error','No active products found to verify.');
            }
        }

        return DB::transaction(function () use ($request, $productIds) {
            $session = StockVerificationSession::create([
                'session_number' => StockVerificationSession::generateNumber(),
                'title' => $request->title,
                'branch_id' => $request->branch_id,
                'location_id' => $request->location_id,
                'status' => $request->assigned_auditor_id ? 'assigned' : 'draft',
                'assigned_auditor_id' => $request->assigned_auditor_id,
                'created_by' => Auth::id(),
                'assigned_at' => $request->assigned_auditor_id ? now() : null,
                'notes' => $request->notes,
                'is_monthly_audit' => $request->boolean('is_monthly_audit'),
                'audit_month' => $request->audit_month,
            ]);

            foreach ($productIds as $productId) {
                $product = Product::find($productId);
                if (!$product) continue;
                StockVerificationItem::create([
                    'session_id' => $session->id,
                    'product_id' => $productId,
                    'system_quantity' => $product->quantity ?? 0,
                    'status' => 'pending',
                ]);
            }
            $session->recalcCounts();
            AuditService::log('create_stock_verification','stock_verification', StockVerificationSession::class, $session->id, null, $session->toArray(), 'Created verification session '.$session->session_number);
            return redirect()->route('stock-verification.show', $session)->with('success','Verification session created');
        });
    }

    public function show(StockVerificationSession $session)
    {
        $user = Auth::user();
        $isAuditor = $user->role === 'stock_auditor';
        $isExternal = $user->role === 'external_auditor';
        $isManager = in_array($user->role, ['admin','manager','inventory_manager','store_supervisor'], true);

        if ($isAuditor && (int)$session->assigned_auditor_id !== (int)$user->id) {
            abort(403, 'You are not assigned to this session');
        }
        if ($isAuditor) {
            // Blind view: never expose system_quantity / variance
            // Ensure ALL active products are shown without numbers – if session was created with subset, auto-add missing active products
            $activeIds = Product::where('is_active', true)->pluck('id')->toArray();
            $existingIds = $session->items()->pluck('product_id')->toArray();
            $missing = array_diff($activeIds, $existingIds);
            if (!empty($missing) && in_array($session->status, ['assigned','in_progress','draft'], true)) {
                foreach ($missing as $pid) {
                    $prod = Product::find($pid);
                    if ($prod) {
                        StockVerificationItem::create([
                            'session_id' => $session->id,
                            'product_id' => $pid,
                            'system_quantity' => $prod->quantity ?? 0,
                            'status' => 'pending',
                        ]);
                    }
                }
                $session->recalcCounts();
                $session->refresh();
            }
            $session->load(['items.product','branch','location']);
            return view('stock-verification.auditor-show', compact('session'));
        }
        if ($isExternal) {
            $session->load(['items.product','branch','location','auditor','creator']);
            // External auditor sees variance but read-only
            return view('stock-verification.external-show', compact('session'));
        }
        // Manager view: full variance after submission
        $session->load(['items.product','branch','location','auditor','creator','reviewer','approver']);
        return view('stock-verification.show', compact('session'));
    }

    // Auditor submits counts (blind)
    public function auditorSubmit(Request $request, StockVerificationSession $session)
    {
        $user = Auth::user();
        if ($user->role !== 'stock_auditor' || (int)$session->assigned_auditor_id !== (int)$user->id) {
            abort(403);
        }
        if (!in_array($session->status, ['assigned','in_progress'], true)) {
            return back()->with('error','Session not in progress');
        }
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stock_verification_items,id',
            'items.*.physical_quantity' => 'required|integer|min:0',
        ]);

        return DB::transaction(function () use ($request, $session, $user) {
            if (!$session->started_at) $session->update(['started_at'=>now(),'status'=>'in_progress']);
            foreach ($request->items as $row) {
                $item = StockVerificationItem::where('id',$row['id'])->where('session_id',$session->id)->firstOrFail();
                $item->update([
                    'physical_quantity' => $row['physical_quantity'],
                    'status' => 'counted',
                    'counted_at' => now(),
                    'counted_by' => $user->id,
                ]);
                // Silent variance calculation (not shown to auditor)
                $item->calculateVariance();
                $item->save();
            }
            $session->recalcCounts();
            // If all counted, mark submitted
            if ($session->items()->where('status','pending')->count() === 0) {
                $session->update(['status'=>'submitted','submitted_at'=>now()]);
                AuditService::log('submit_stock_verification','stock_verification', StockVerificationSession::class, $session->id, null, ['submitted'=>true], 'Auditor submitted verification '.$session->session_number);
                return redirect()->route('stock-verification.show',$session)->with('success','Verification submitted successfully');
            }
            AuditService::log('save_stock_verification','stock_verification', StockVerificationSession::class, $session->id, null, ['partial'=>true], 'Auditor saved counts for '.$session->session_number);
            return back()->with('success','Counts saved');
        });
    }

    // Management reviews variance
    public function review(Request $request, StockVerificationSession $session)
    {
        $this->ensureCanManage();
        if ($session->status !== 'submitted') return back()->with('error','Session not submitted');
        $request->validate(['review_notes'=>'nullable|string','variance_reasons'=>'nullable|array']);
        $session->update([
            'status'=>'under_review',
            'reviewed_at'=>now(),
            'reviewed_by'=>Auth::id(),
            'review_notes'=>$request->review_notes,
        ]);
        if ($request->variance_reasons) {
            foreach ($request->variance_reasons as $itemId=>$reason) {
                StockVerificationItem::where('id',$itemId)->where('session_id',$session->id)->update(['variance_reason'=>$reason]);
            }
        }
        AuditService::log('review_stock_verification','stock_verification', StockVerificationSession::class, $session->id, ['status'=>'submitted'], ['status'=>'under_review'], 'Reviewed session '.$session->session_number);
        return back()->with('success','Session marked under review');
    }

    public function approve(Request $request, StockVerificationSession $session)
    {
        $this->ensureCanManage();
        if (!in_array($session->status, ['submitted','under_review'], true)) return back()->with('error','Invalid status');
        $createAdjustments = $request->boolean('create_adjustments', true);

        return DB::transaction(function () use ($session, $createAdjustments) {
            $session->update(['status'=>'approved','approved_at'=>now(),'approved_by'=>Auth::id()]);
            if ($createAdjustments) {
                foreach ($session->items as $item) {
                    if ($item->variance_quantity == 0) continue;
                    if ($item->adjustment_created) continue;
                    $product = $item->product;
                    $before = $product->quantity;
                    $after = $item->physical_quantity;
                    $change = $after - $before;
                    $type = $change > 0 ? 'addition' : 'subtraction';
                    $adj = StockAdjustment::create([
                        'reference_number' => 'ADJ-SV-'. $session->session_number . '-P'.$product->id,
                        'product_id' => $product->id,
                        'quantity_before' => $before,
                        'quantity_change' => $change,
                        'quantity_after' => $after,
                        'type' => $type,
                        'reason' => 'Stock verification adjustment (Session '.$session->session_number.') Variance: '.$item->variance_quantity,
                        'adjustment_date' => now()->toDateString(),
                        'notes' => $item->variance_reason,
                    ]);
                    $product->update(['quantity'=>$after]);
                    $movement = StockMovement::create([
                        'product_id'=>$product->id,
                        'movement_type'=> $change>0?'adjustment_in':'adjustment_out',
                        'quantity'=> abs($change),
                        'reference_type'=> StockVerificationSession::class,
                        'reference_id'=> $session->id,
                        'user_id'=> Auth::id(),
                        'notes'=>'Verification '.$session->session_number.' variance '.$item->variance_quantity,
                    ]);
                    \App\Models\StockVerificationAdjustment::create([
                        'session_id'=>$session->id,
                        'product_id'=>$product->id,
                        'quantity_before'=>$before,
                        'quantity_after'=>$after,
                        'quantity_change'=>$change,
                        'stock_adjustment_id'=>$adj->id,
                        'stock_movement_id'=>$movement->id,
                    ]);
                    $item->update(['adjustment_created'=>true]);
                    AuditService::log('create_stock_adjustment','inventory', StockAdjustment::class, $adj->id, null, $adj->toArray(), 'Auto adjustment from verification '.$session->session_number);
                }
            }
            AuditService::log('approve_stock_verification','stock_verification', StockVerificationSession::class, $session->id, ['status'=>'submitted'], ['status'=>'approved'], 'Approved session '.$session->session_number);
            return back()->with('success','Session approved'.($createAdjustments?' and adjustments created':''));
        });
    }

    public function reject(Request $request, StockVerificationSession $session)
    {
        $this->ensureCanManage();
        $request->validate(['reason'=>'required|string']);
        $session->update(['status'=>'rejected','review_notes'=>$request->reason,'reviewed_at'=>now(),'reviewed_by'=>Auth::id()]);
        AuditService::log('reject_stock_verification','stock_verification', StockVerificationSession::class, $session->id, null, ['status'=>'rejected'], 'Rejected '.$session->session_number);
        return back()->with('success','Session rejected');
    }

    // API endpoints (JSON) for auditor app
    public function apiAssignedSessions(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'stock_auditor') return response()->json(['message'=>'Forbidden'],403);
        $sessions = StockVerificationSession::where('assigned_auditor_id',$user->id)->withCount('items')->latest()->get();
        // hide variance data
        return response()->json($sessions);
    }

    public function apiSessionDetail(Request $request, StockVerificationSession $session)
    {
        $user = $request->user();
        if ($user->role === 'stock_auditor' && (int)$session->assigned_auditor_id !== (int)$user->id) return response()->json(['message'=>'Forbidden'],403);
        // For auditor, strip system_quantity/variance
        $session->load('items.product');
        $data = $session->toArray();
        if ($user->role === 'stock_auditor') {
            $data['items'] = $session->items->map->toAuditorArray();
        }
        return response()->json($data);
    }

    public function apiSubmit(Request $request, StockVerificationSession $session)
    {
        $user = $request->user();
        if ($user->role !== 'stock_auditor' || (int)$session->assigned_auditor_id !== (int)$user->id) return response()->json(['message'=>'Forbidden'],403);
        $request->validate([
            'items'=>'required|array',
            'items.*.id'=>'required|exists:stock_verification_items,id',
            'items.*.physical_quantity'=>'required|integer|min:0',
        ]);
        return DB::transaction(function() use ($request,$session,$user){
            foreach ($request->items as $row) {
                $item = StockVerificationItem::where('id',$row['id'])->where('session_id',$session->id)->firstOrFail();
                $item->update([
                    'physical_quantity'=>$row['physical_quantity'],
                    'status'=>'counted',
                    'counted_at'=>now(),
                    'counted_by'=>$user->id,
                ]);
                $item->calculateVariance();
                $item->save();
            }
            $session->recalcCounts();
            $allCounted = $session->items()->where('status','pending')->count()===0;
            if ($allCounted) $session->update(['status'=>'submitted','submitted_at'=>now()]);
            else if (!$session->started_at) $session->update(['started_at'=>now(),'status'=>'in_progress']);
            AuditService::log('submit_stock_verification','stock_verification', StockVerificationSession::class, $session->id, null, ['api'=>true], 'API submit '.$session->session_number);
            return response()->json(['message'=>'Verification Submitted Successfully','status'=>$session->status]);
        });
    }
}
