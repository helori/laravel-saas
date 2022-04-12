<?php

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;


class CardTest extends TestCase
{
    public function testCard()
    {
    	$user = User::factory()->create();
        $team = $user->currentTeam();
        Sanctum::actingAs($user);

        // --------------------------------------------------
        //  Create Card Intent (Needed in UI)
        // --------------------------------------------------
        $response = $this
            ->json('GET', '/card-intent')
            ->assertStatus(200)
            ->assertJsonStructure([
                'client_secret'
            ]);

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
        //  Read Card
        // --------------------------------------------------
        $response = $this
            ->json('GET', '/card')
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
        //  Delete Card
        // --------------------------------------------------
        $response = $this
            ->json('DELETE', '/card')
            ->assertStatus(200);
        
        $user->delete();
        $team->delete();
    }
}
