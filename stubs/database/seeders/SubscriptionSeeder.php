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

        $billable->updateDefaultPaymentMethod('tok_visa');

        $products = config('saas.products');

        foreach($products as $product)
        {
            $plan = $product['plans'][0];
            $price = $plan['prices'][0];

            $billable
                ->newSubscription($product['slug'], $price['price_id'])
                ->trialDays(isset($product['trial_days']) ? intVal($product['trial_days']) : 0)
                ->add();
        }
    }
}
