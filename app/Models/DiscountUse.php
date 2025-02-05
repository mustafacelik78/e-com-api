<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountUse extends Model
{
    protected $fillable = ['discount_rule_id', 'customer_id', 'order_id', 'usage_count'];

    public function discountRule()
    {
        return $this->belongsTo(DiscountRule::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
