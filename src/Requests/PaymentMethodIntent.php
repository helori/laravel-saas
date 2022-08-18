<?php

namespace Helori\LaravelSaas\Requests;


class PaymentMethodIntent extends ActionRequest
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

        // https://stripe.com/docs/api/setup_intents/create#create_setup_intent-payment_method_types
        return $billable->createSetupIntent([
            'payment_method_types' => ['card', 'sepa_debit'],
        ]);
    }
}
