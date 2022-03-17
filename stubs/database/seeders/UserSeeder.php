<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Team;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the Stripe seeds.
     *
     * @return void
     */
    public function run()
    {
        $teams = Team::all();
        foreach($teams as $team){
            $team->delete();
        }
        
        $users = User::all();
        foreach($users as $user){
            $user->delete();
        }

        $root = new User();
        $root->firstname = 'Helori';
        $root->lastname = 'LANOS';
        $root->email = 'helori@algoart.fr';
        $root->email_verified_at = now();
        $root->remember_token = Str::random(10);
        $root->password = bcrypt('aaaaaaaa');
        $root->is_root = true;
        $root->save();

        // Create team members
        $members = $this->createTeamMembers($root->ownedTeams()->first(), 5);

        // Each member has his own team, adding the root user to their team allows testing team switching
        foreach($members as $member)
        {
            $member->ownedTeams()->first()->users()->attach($root, [
                'role' => 'member'
            ]);
        }

        // Create an active subscription for the root user
        $this->subscribeUser($root);
    }

    public function createTeamMembers($team, $count)
    {
        $users = User::factory()
            ->count($count)
            ->create();

        // Tous les users sont dans l'équipe du root
        $team->users()->attach($users, [
            'role' => 'member'
        ]);

        return $users;
    }

    public function subscribeUser($user, $coupon = null, $promoCode = null)
    {
        $team = $user->billable();

        $quantity = 1;

        if(!$team->hasStripeId()){
            $team->createAsStripeCustomer([
                'email' => $user->email,
            ]);
        }

        // https://stripe.com/docs/testing#regulatory-cards
        $team->updateDefaultPaymentMethod('pm_card_bypassPending');

        $products = config('saas.products');

        foreach($products as $product)
        {
            $plan = $product['plans'][0];
            $price = $plan['prices'][0];

            $subscription = $team->newSubscription($product['slug'], $price['price_id']);
            $subscription->quantity($quantity);

            if(intVal($product['trial_days']) > 0)
            {
                $subscription->trialDays(intVal($product['trial_days']));
            }

            if(!is_null($coupon))
            {
                $subscription->withCoupon($coupon);
            }

            if(!is_null($promoCode))
            {
                $subscription->withPromotionCode($promoCode);
            }
            
            $subscription->add();
        }
    }
}
