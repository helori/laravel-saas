<?php

namespace Helori\LaravelSaas\Models\Stripe;

use Illuminate\Database\Eloquent\Model;


class Price extends Model
{
    protected $table = 'stripe_prices';

    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = [];

    protected $casts = [
        'created' => 'datetime',
        'unit_amount' => 'integer',
        'interval_count' => 'integer',
    ];
}
