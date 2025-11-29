<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'product_id',
        'quantity',
        'unit_price',
        'cost_price',
        'customer_id',
        'order_ref',
        'order_status',
       'address_id',
    ];
}
