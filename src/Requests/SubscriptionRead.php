<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Arr;


class SubscriptionRead extends ActionRequest
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
        if(!$billable->hasStripeId()){
            $billable->createAsStripeCustomer();
        }

        $productSlug = $this->product;

        $subscription = $billable->subscription($productSlug);

        $product = null;
        $plan = null;
        $price = null;

        if($subscription)
        {
            $products = config('saas.billables.team.products');
            $priceId = $subscription['stripe_price'];

            $product = Arr::first($products, function($product) use($productSlug) {
                return ($product['slug'] === $productSlug);
            });

            $plan = Arr::first($product['plans'], function($plan) use($priceId) {
                foreach($plan['prices'] as $price){
                    if($price['price_id'] === $priceId){
                        return true;
                    }
                }
                return false;
            });

            $price = Arr::first($plan['prices'], function($pri) use($priceId) {
                return ($pri['price_id'] === $priceId);
            });
        }

        $subscription->product = Arr::except($product, ['plans']);
        $subscription->plan = Arr::except($plan, ['prices']);
        $subscription->price = $price;

        $subscription->is_active = $subscription->active();
        $subscription->is_recurring = $subscription->recurring();
        $subscription->is_cancelled = $subscription->cancelled();
        $subscription->is_ended = $subscription->ended();
        $subscription->is_on_grace_period = $subscription->onGracePeriod();
        $subscription->is_on_trial = $subscription->onTrial();

        return $subscription;
    }
}
