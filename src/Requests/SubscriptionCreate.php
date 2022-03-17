<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Arr;


class SubscriptionCreate extends ActionRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product' => 'required|string',
            'plan' => 'required|string',
            'price' => 'required|string',
            'quantity' => 'sometimes|integer|min:1',
            'coupon' => 'sometimes|string',
            'promo_code' => 'sometimes|string',
            //'payment_method' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'quantity.min' => "Votre souscription est vide",
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
            $billable->createAsStripeCustomer([
                'email' => $this->user()->email,
            ]);
        }

        $paymentMethod = $billable->defaultPaymentMethod();

        if(!$paymentMethod){
            abort(422, "Default payment method is missing");
        }

        $productSlug = $this->product;
        $planSlug = $this->plan;
        $priceSlug = $this->price;

        $products = config('saas.products');

        $product = Arr::first($products, function($product) use($productSlug) {
            return ($product['slug'] === $productSlug);
        }, null);

        $plan = Arr::first($product['plans'], function($plan) use($planSlug) {
            return ($plan['slug'] === $planSlug);
        }, null);

        $price = Arr::first($plan['prices'], function($price) use($priceSlug) {
            return ($price['slug'] === $priceSlug);
        }, null);

        
        $subscription = $billable->subscription($product['slug']);
        
        if(!$subscription || $subscription->ended())
        {
            $subscription = $billable->newSubscription($product['slug'], $price['price_id']);

            if(isset($product['trial_days']) && intVal($product['trial_days']) > 0)
            {
                $subscription->trialDays(intVal($product['trial_days']));
            }

            if($this->has('quantity'))
            {
                $subscription->quantity($this->quantity);
            }

            if($this->has('coupon'))
            {
                $subscription->withCoupon($this->coupon);
            }

            if($this->has('promo_code'))
            {
                $subscription->withPromotionCode($this->promo_code);
            }
            
            $subscription->add(); // If the user already has a default payment method
            
            /*$subscription->create($this->paymentMethodId, [
                // Customer options : https://stripe.com/docs/api/customers/create
            ], [
                // Subscription options : https://stripe.com/docs/api/subscriptions/create
            ]);*/
        }
        else
        {
            if(!$subscription->hasPrice($price['price_id'])){
                $subscription->swap($price['price_id']);
            }else{
                if($subscription->cancelled()){
                    $subscription->resume();
                }else{
                    abort(422, "Already subscribed !");
                }
            }
        }    
    }
}
