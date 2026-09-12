<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorIntelligence extends Model
{
    protected $fillable = [
        'competitor_name','competitor_location','product_id','product_name','product_name_normalized',
        'competitor_price','our_price','price_difference','price_difference_percent','availability',
        'date_checked','sales_rep_id','branch_id','location_id','notes','photo','channel'
    ];

    protected $casts = ['date_checked'=>'date'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function salesRep(): BelongsTo { return $this->belongsTo(User::class,'sales_rep_id'); }

    protected static function booted(): void
    {
        static::saving(function($m){
            $m->product_name_normalized = strtolower(trim($m->product_name));
            $m->price_difference = $m->our_price - $m->competitor_price;
            $m->price_difference_percent = $m->our_price !=0 ? round(($m->price_difference / $m->our_price)*100,2) : 0;
        });
    }
}
