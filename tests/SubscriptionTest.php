<?php

use Tests\TestCase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Faker\Factory as Faker;
use App\Models\User;


class SubscriptionTest extends TestCase
{
    public function testSubscription()
    {
    	$user = User::factory()->create();
        $team = $user->currentTeam();
        Sanctum::actingAs($user);

        // --------------------------------------------------
        //  Create Card (using test cards)
        // --------------------------------------------------
        // https://stripe.com/docs/testing#regulatory-cards
        $response = $this
            ->json('PUT', '/card', [
                'payment_method' => 'pm_card_bypassPending',
            ])
            ->assertStatus(200)
            ->assertJson([
                'type' => 'card',
                'card' => [
                    'brand' => 'visa',
                    'last4' => '0077',
                    'exp_year' => '2023',
                ],
            ]);

        // --------------------------------------------------
        //  Read Products
        // --------------------------------------------------
        $response = $this
            ->json('GET', '/products')
            ->assertStatus(200);

        // --------------------------------------------------
        //  Test first product
        // --------------------------------------------------
        $products = json_decode($response->getContent(), true);
        $product = $products[0];

        $response = $this
            ->json('POST', '/subscription', [
                'name' => 'main-subscription',
                'product' => $product['product_id'],
                'price' => $product['prices'][0]['price_id'],
                'quantity' => 1,
            ])
            ->assertStatus(200)
            ->assertJson([
                'is_active' => true,
                'is_on_grace_period' => false,
            ]);

        $response = $this
            ->json('GET', '/subscription', [
                'name' => 'main-subscription',
            ])
            ->assertStatus(200)
            ->assertJson([
                'is_active' => true,
                'is_on_grace_period' => false,
            ]);

        $response = $this
            ->json('DELETE', '/subscription', [
                'name' => 'main-subscription',
            ])
            ->assertStatus(200)
            ->assertJson([
                'is_active' => true,
                'is_on_grace_period' => true,
            ]);

        $response = $this
            ->json('DELETE', '/subscription', [
                'name' => 'main-subscription',
                'cancel_now' => true,
            ])
            ->assertStatus(200)
            ->assertJson([
                'is_active' => false,
                'is_on_grace_period' => false,
            ]);

        $user->delete();
        $team->delete();
    }
}
