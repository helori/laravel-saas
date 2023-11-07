<?php

namespace Helori\LaravelSaas\Models\Stripe;

use Illuminate\Database\Eloquent\Model;


class Subscription extends Model
{
    protected $table = 'stripe_subscriptions';
    
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = [];

    protected $casts = [
        'created' => 'datetime',
        'price_amount' => 'double',
        'amount_off' => 'double',
        'percent_off' => 'double',
        'price_discounted' => 'double',
        'price_discounted_user' => 'double',
    ];
}