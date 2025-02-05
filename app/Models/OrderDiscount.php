<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDiscount extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'discount_rule_id', 'discount_amount', 'subtotal'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
