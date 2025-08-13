<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'product_name',
        'product_category',
        'initial_price',
        'selling_price',
        'product_description',
        'product_quantity',
        'product_image',
        'vendor_id',
    ];
}
