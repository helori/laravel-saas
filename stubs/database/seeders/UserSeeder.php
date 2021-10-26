<?php

namespace Helori\LaravelSaas\Seeders;

use Illuminate\Database\Seeder;
use Helori\LaravelSaas\Models\User;


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
