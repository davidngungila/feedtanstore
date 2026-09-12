<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model {
    use SoftDeletes;
    protected $fillable = ['invoice_number', 'local_transaction_id','sync_status','synced_at','offline_created_at','device_info','sales_channel','branch_id','location_id','sales_rep_id','gross_sales','cost_of_goods_sold','gross_profit','customer_id', 'user_id', 'shift_id', 'discount_id', 'subtotal', 'tax', 'discount', 'total', 'paid', 'change', 'payment_method', 'type', 'status', 'notes', 'cancellation_reason', 'cash_drawer_session_id', 'tra_receipt_number', 'tra_verification_link', 'tra_qr_code', 'tra_status', 'tra_gc_used', 'tra_dc_used', 'tra_znum_used'];

    protected $casts = ['synced_at'=>'datetime','offline_created_at'=>'datetime'];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function shift() {
        return $this->belongsTo(Shift::class);
    }

    public function discountApplied() {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function items() {
        return $this->hasMany(SaleItem::class);
    }

    public function returns() {
        return $this->hasMany(SaleReturn::class);
    }

    public function accountingEntries() {
        return $this->morphMany(AccountingEntry::class, 'reference');
    }

    public function cashDrawerSession() {
        return $this->belongsTo(CashDrawerSession::class);
    }

    public function branch() { return $this->belongsTo(Branch::class); }
    public function location() { return $this->belongsTo(Location::class); }
    public function salesRep() { return $this->belongsTo(User::class,'sales_rep_id'); }
    public function serviceTime() { return $this->hasOne(CashierServiceTime::class); }
    public function rating() { return $this->hasOne(CustomerRating::class); }
    public function issues() { return $this->hasMany(TransactionIssue::class); }
}
