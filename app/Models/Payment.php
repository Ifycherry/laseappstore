<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $fillable = [
       'order_ref',
       'total',
       'payment_status',
       'payment_ref',
       'customer_id',
       'payment_method',
       'address_id',
    ];
}
