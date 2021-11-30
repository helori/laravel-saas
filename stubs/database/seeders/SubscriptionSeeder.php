<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;


class SubscriptionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::findOrFail(1);

        $billable = $user->billable();
        if(!$billable->hasStripeId()){
            $billable->createAsStripeCustomer();
        }

        // https://stripe.com/docs/testing#regulatory-cards
        $billable->updateDefaultPaymentMethod('pm_card_bypassPending');

        $products = config('saas.products');

        foreach($products as $product)
        {
            $plan = $product['plans'][0];
            $price = $plan['prices'][0];

            $subscriptionBuilder = $billable->newSubscription($product['slug'], $price['price_id']);
            if(intVal($product['trial_days']) > 0)
            {
                $subscriptionBuilder->trialDays(intVal($product['trial_days']));
            }
            $subscriptionBuilder->add();
        }
    }
}
