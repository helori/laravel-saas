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
            //'payment_method' => 'required|string',
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

        if(!$subscription)
        {
            return $billable
                ->newSubscription($product['slug'], $price['price_id'])
                ->trialDays(isset($product['trial_days']) ? intVal($product['trial_days']) : 0)
                // ->withCoupon('code')
                // ->withPromotionCode('promo_code')
                ->add(); // If the user already has a default payment method
                /*->create($this->paymentMethodId, [
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
