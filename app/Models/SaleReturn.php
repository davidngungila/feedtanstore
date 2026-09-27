<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model {
    protected $fillable = ['return_number','receipt_number','sale_id','branch_id','location_id','user_id','approver_id','total','reason','approval_status','refund_method','refund_amount','stock_updated','approved_at'];

    public const REASONS = [
        'defective_damaged' => 'Defective / Damaged',
        'expired' => 'Expired product',
        'wrong_item' => 'Wrong item sold',
        'quality_issue' => 'Quality issue',
        'customer_changed_mind' => 'Customer changed mind',
        'pricing_error' => 'Pricing / overcharge error',
        'duplicate_sale' => 'Duplicate sale',
        'other' => 'Other (specify below)',
    ];
    protected $casts = ['approved_at'=>'datetime','stock_updated'=>'boolean'];

    public function sale() {
        return $this->belongsTo(Sale::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(SaleReturnItem::class);
    }
    public function approver() { return $this->belongsTo(User::class,'approver_id'); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
