<?php

namespace Helori\LaravelSaas\Requests;

use Helori\LaravelSaas\Models\Stripe\Price;

class PriceList extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        return Price::get();
    }
}
