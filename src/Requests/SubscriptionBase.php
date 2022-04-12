<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Arr;


abstract class SubscriptionBase extends ActionRequest
{
    public function subscriptionWithInfos($name)
    {
        $billable = $this->user()->billable();
        $subscription = $billable->subscription($name);

        if(!is_null($subscription))
        {
            $priceId = $subscription['stripe_price'];

            $products = config('saas.products');
            $product = Arr::first($products, function($product) use($priceId) {
                $price = Arr::first($product['prices'], function($price) use($priceId) {
                    return ($price['price_id'] === $priceId);
                });
                return !is_null($price);
            });

            $price = Arr::first($product['prices'], function($price) use($priceId) {
                return ($price['price_id'] === $priceId);
            });

            // Return usefull information about the subscription and product to the user :
            $subscription->product = Arr::except($product, ['prices']);
            $subscription->price = $price;
            $subscription->is_active = $subscription->active();
            $subscription->is_recurring = $subscription->recurring();
            $subscription->is_cancelled = $subscription->cancelled();
            $subscription->is_ended = $subscription->ended();
            $subscription->is_on_grace_period = $subscription->onGracePeriod();
            $subscription->is_on_trial = $subscription->onTrial();
        }

        return $subscription;
    }
}
