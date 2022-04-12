<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Arr;


class SubscriptionRead extends SubscriptionBase
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
        ];
    }

    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        return $this->subscriptionWithInfos($this->name);
    }
}
