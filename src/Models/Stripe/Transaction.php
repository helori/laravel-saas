<?php

namespace Helori\LaravelSaas\Models\Stripe;

use Illuminate\Database\Eloquent\Model;


class Transaction extends Model
{
    protected $table = 'stripe_transactions';
    
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = [];

    protected $casts = [
        'created' => 'datetime',
        'available_on' => 'datetime',
        'amount' => 'double',
        'fee' => 'double',
        'net' => 'double',
    ];
}
