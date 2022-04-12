<?php

use Tests\TestCase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Faker\Factory as Faker;
use App\Models\User;


class TeamTest extends TestCase
{
    public function testTeam()
    {
    	$faker = Faker::create('fr_FR');

        $user = User::factory()->create();
        $team = $user->currentTeam();

        $user2 = User::factory()->create();
        $team2 = $user2->currentTeam();

        // TODO : invite (or add) user2 to team1
        $team2->users()->attach($user, ['role' => 'member']);
        $team->users()->attach($user2, ['role' => 'member']);

        $this->assertTrue(!is_null($team));
        $this->assertTrue($team->owner->id === $user->id);
        $this->assertTrue($user->ownTeam($team));
        $this->assertTrue(!$user2->ownTeam($team));

        $this->assertTrue(!is_null($team2));
        $this->assertTrue($team2->owner->id === $user2->id);
        $this->assertTrue($user2->ownTeam($team2));
        $this->assertTrue(!$user->ownTeam($team2));

        Sanctum::actingAs($user);

        // --------------------------------------------------
        //	Read Current Team
        // --------------------------------------------------
        $response = $this
            ->json('GET', '/team')
            ->assertStatus(200)
            ->assertJson([
                'id' => $team->id,
                'name' => $team->name,
            ]);

        // --------------------------------------------------
        //  Read User Teams
        // --------------------------------------------------
        $response = $this
            ->json('GET', '/teams')
            ->assertStatus(200)
            ->assertJsonCount(2);

        // --------------------------------------------------
        //	Update Team
        // --------------------------------------------------
        $company = $faker->company;

        $response = $this
            ->json('PUT', '/team', [
                'name' => $company,
            ])
            ->assertStatus(200)
            ->assertJson([
                'id' => $team->id,
                'name' => $company,
            ]);

        // --------------------------------------------------
        //  Read team members
        // --------------------------------------------------
        $response = $this
            ->json('GET', '/team/'.$team->id.'/member')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $response = $this
            ->json('GET', '/team/'.$team2->id.'/member')
            ->assertStatus(403);

        // --------------------------------------------------
        //  Update team member
        // --------------------------------------------------
        $firstname = $faker->firstname;
        $lastname = $faker->lastname;
        $email = $faker->email;

        $response = $this
            ->json('PUT', '/team/'.$team->id.'/member/'.$user2->id, [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
            ])
            ->assertStatus(200)
            ->assertJson([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
            ]);

        // --------------------------------------------------
        //  Switch Team
        // --------------------------------------------------
        $response = $this
            ->json('POST', '/team/switch/'.$team2->id, [])
            ->assertStatus(200);

        $response = $this
            ->json('GET', '/team')
            ->assertStatus(200)
            ->assertJson([
                'id' => $team2->id,
            ]);

        $this->assertTrue($user->currentTeam()->id === $team2->id);

        // --------------------------------------------------
        //  Delete team member (not detach ! ... => TODO ?)
        // --------------------------------------------------
        $response = $this
            ->json('DELETE', '/team/'.$team->id.'/member/'.$user->id)
            ->assertStatus(422);

        $response = $this
            ->json('DELETE', '/team/'.$team2->id.'/member/'.$user2->id)
            ->assertStatus(403);

        $response = $this
            ->json('DELETE', '/team/'.$team->id.'/member/'.$user2->id)
            ->assertStatus(200);

        $user->delete();
        $team->delete();
        $team2->delete();
    }
}
