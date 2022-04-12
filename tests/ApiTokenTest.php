<?php

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;


class ApiTokenTest extends TestCase
{
    public function testApiToken()
    {
    	$user = User::factory()->create();
        $team = $user->currentTeam();
        Sanctum::actingAs($user);

        // --------------------------------------------------
        //  Read API Token
        // --------------------------------------------------
        $response = $this
            ->json('GET', '/api-token')
            ->assertNoContent();

        // --------------------------------------------------
        //  Create API Token
        // --------------------------------------------------
        $response = $this
            ->json('POST', '/api-token')
            ->assertCreated();

        // --------------------------------------------------
        //  Delete API Token
        // --------------------------------------------------
        $response = $this
            ->json('DELETE', '/api-token')
            ->assertStatus(200);

        $user->delete();
        $team->delete();
    }
}
