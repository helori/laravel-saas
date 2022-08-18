<?php

namespace Helori\LaravelSaas\Requests;


class PaymentMethodRead extends ActionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->ownCurrentTeam();
    }

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

        $paymentMethod = $billable->defaultPaymentMethod();
        return $paymentMethod ? $paymentMethod->toArray() : null;
    }
}
