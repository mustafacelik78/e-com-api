<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
    protected $fillable = ['type', 'name', 'conditions', 'value'];

    protected $casts = [
        'conditions' => 'array',
    ];
}
