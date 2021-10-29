<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $owners = User::factory()
            ->count(5)
            ->create();

        foreach($owners as $owner)
        {
            $users = User::factory()
                ->count(5)
                ->create();

            $owner->ownedTeams()->first()->users()->attach($users, [
                'role' => 'member'
            ]);
        }
    }
}
