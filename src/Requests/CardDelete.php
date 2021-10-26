<?php

namespace Helori\LaravelSaas\Requests;


class CardDelete extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $billable = $this->user()->billable();
        $paymentMethod = $billable->defaultPaymentMethod();
        if($paymentMethod){
            $paymentMethod->delete(); 
        }
    }
}
