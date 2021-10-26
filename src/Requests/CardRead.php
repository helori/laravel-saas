<?php

namespace Helori\LaravelSaas\Requests;


class CardRead extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $billable = $this->user()->billable();
        if(!$billable->hasStripeId()){
            $billable->createAsStripeCustomer();
        }
        return $billable->defaultPaymentMethod();
    }
}
