<?php

namespace Helori\LaravelSaas\Models\Stripe;

use Illuminate\Database\Eloquent\Model;


class Invoice extends Model
{
    protected $table = 'stripe_invoices';

    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = [];

    protected $casts = [
        'date' => 'datetime',
        'created' => 'datetime',
        'finalized_at' => 'datetime',
        'due_date' => 'datetime',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'paid' => 'boolean',
        'subtotal' => 'double',
        'subtotal_excluding_tax' => 'double',
        'total_excluding_tax' => 'double',
        'tax' => 'double',
        'total' => 'double',
        'amount_due' => 'double',
        'amount_refunded' => 'double',
    ];
}
