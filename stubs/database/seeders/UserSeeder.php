<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Team;
use App\Models\User;


class UserSeeder extends Seeder
{
    public function run()
    {
        Team::query()->delete();
        User::query()->delete();

        // Create root user with their own team
        $root = new User();
        $root->firstname = 'Helori';
        $root->lastname = 'LANOS';
        $root->email = 'helori@algoart.fr';
        $root->email_verified_at = now();
        $root->remember_token = Str::random(10);
        $root->password = bcrypt('aaaaaaaa');
        $root->is_root = true;
        $root->save();

        $rootTeam = Team::create([
            'user_id' => $root->id,
            'name' => 'Équipe de '.$root->firstname.' '.$root->lastname,
            'billing_name' => 'Équipe de '.$root->firstname.' '.$root->lastname,
            'billing_email' => $root->email,
            'billing_country' => 'FR',
        ]);

        $root->forceFill(['team_id' => $rootTeam->id, 'role' => 'owner'])->save();

        // Create additional team members
        $members = User::factory()->count(5)->create();
        foreach($members as $member) {
            $member->forceFill(['team_id' => $rootTeam->id, 'role' => 'member'])->save();
        }

        // Create an active subscription for the root user's team
        $this->subscribeTeam($rootTeam, $root);
    }

    public function subscribeTeam(Team $team, User $owner, $coupon = null, $promoCode = null)
    {
        if(!$team->hasStripeId()){
            $team->createAsStripeCustomer(['email' => $owner->email]);
        }

        // https://stripe.com/docs/testing#regulatory-cards
        $team->updateDefaultPaymentMethod('pm_card_bypassPending');

        $products = config('saas.products');

        foreach($products as $product)
        {
            $plan = $product['plans'][0];
            $price = $plan['prices'][0];
            $quantity = $team->users()->count();

            $subscription = $team->newSubscription($product['slug'], $price['price_id']);
            $subscription->quantity($quantity);

            if(intVal($product['trial_days'] ?? 0) > 0) {
                $subscription->trialDays(intVal($product['trial_days']));
            }
            if(!is_null($coupon)) {
                $subscription->withCoupon($coupon);
            }
            if(!is_null($promoCode)) {
                $subscription->withPromotionCode($promoCode);
            }

            $subscription->add();
        }
    }
}
