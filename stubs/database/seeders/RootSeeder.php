<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;


class RootSeeder extends Seeder
{
    /**
     * Run the Stripe seeds.
     *
     * @return void
     */
    public function run()
    {
        $root = new User();
        $root->firstname = 'Helori';
        $root->lastname = 'LANOS';
        $root->email = 'helori@algoart.fr';
        $root->email_verified_at = now();
        $root->remember_token = Str::random(10);
        $root->password = bcrypt('aaaaaaaa');
        $root->is_root = true;
        $root->save();

        $users = User::factory()
            ->count(5)
            ->create();

        // Tous les users sont dans l'équipe du root
        $root->ownedTeams()->first()->users()->attach($users, [
            'role' => 'member'
        ]);

        // Le root est dans l'équipe de tous les users
        foreach($users as $user)
        {
            $user->ownedTeams()->first()->users()->attach($root, [
                'role' => 'member'
            ]);
        }
    }
}
