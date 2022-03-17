<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Arr;


class SubscriptionDelete extends ActionRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product' => 'required|string|max:191',
            'cancel_now' => 'sometimes|boolean',
        ];
    }

    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $cancelNow = $this->input('cancel_now', false);
        $billable = $this->user()->billable();
        $productSlug = $this->product;
        $subscription = $billable->subscription($productSlug);

        if($cancelNow){
            $subscription->cancelNow();
        }else{
            $subscription->cancel();
        }
        
    }
}
