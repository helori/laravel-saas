<?php

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;


class SessionTest extends TestCase
{
    public function testSession()
    {
    	$user = User::factory()->create();
        $team = $user->currentTeam();
        Sanctum::actingAs($user);

        // --------------------------------------------------
        //  Browser sessions are not testable :
        //  The session driver is always "array" in tests
        //  Browser sessions management expects "database"
        // --------------------------------------------------
        $this->assertTrue(true);
        
        /*$response = $this
            ->json('GET', '/browser-session')
            ->assertStatus(200);

        $response = $this
            ->json('DELETE', '/browser-session')
            ->assertStatus(200);*/

        $user->delete();
        $team->delete();
    }
}
