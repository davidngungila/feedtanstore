<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnItem extends Model {
    protected $fillable = ['sale_return_id', 'sale_item_id', 'quantity', 'unit_price', 'total'];

    public function saleReturn() {
        return $this->belongsTo(SaleReturn::class);
    }

    public function saleItem() {
        return $this->belongsTo(SaleItem::class);
    }
}
