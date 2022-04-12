<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Arr;


class SubscriptionDelete extends SubscriptionBase
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:191',
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
        $subscription = $billable->subscription($this->name);

        if($cancelNow){
            $subscription->cancelNow();
        }else{
            $subscription->cancel();
        }
        return $this->subscriptionWithInfos($this->name);
    }
}
