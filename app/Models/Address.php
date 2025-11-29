<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //
    protected $fillable = [
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'contact_name',
        'contact_phone',
        'contact_verification',
    ];
}
