<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Arr;


abstract class SubscriptionBase extends ActionRequest
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
    
    public function subscriptionWithInfos($name)
    {
        $billable = $this->user()->billable();
        $subscription = $billable->subscription($name);

        if(!is_null($subscription))
        {
            if($subscription->hasMultiplePrices())
            {
                $priceIds = $subscription->items->pluck('stripe_price')->all();
            }
            else
            {
                $priceIds = [$subscription['stripe_price']];
            }

            $products = config('saas.products');
            
            $product = Arr::first($products, function($product) use($priceIds) {
                $price = Arr::first($product['prices'], function($price) use($priceIds) {
                    return in_array($price['price_id'], $priceIds);
                });
                return !is_null($price);
            });

            $prices = array_values(Arr::where($product['prices'], function($price) use($priceIds) {
                return in_array($price['price_id'], $priceIds);
            }));

            // Return usefull information about the subscription and product to the user :
            $subscription->product = Arr::except($product, ['prices']);
            $subscription->price = (count($prices) > 0) ? $prices[0] : null;
            $subscription->prices = $prices;
            $subscription->is_active = $subscription->active();
            $subscription->is_recurring = $subscription->recurring();
            $subscription->is_cancelled = $subscription->cancelled();
            $subscription->is_ended = $subscription->ended();
            $subscription->is_on_grace_period = $subscription->onGracePeriod();
            $subscription->is_on_trial = $subscription->onTrial();

            $subscription->active_discount = $subscription->discount();
            if($subscription->active_discount){
                $subscription->active_coupon = $subscription->active_discount->coupon();
            }
        }

        return $subscription;
    }
}
