<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Arr;


class PromotionRead extends SubscriptionBase
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|string|max:191',
        ];
    }

    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $billable = $this->user()->billable();
        $promotion = $billable->findPromotionCode($this->code);

        if(!$promotion){
            abort(422, "Ce code de remise n'est pas valide");
        }
        
        return $promotion;
    }
}
