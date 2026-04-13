<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Helori\LaravelSaas\Notifications\Admin\UserCreated;
use App\Models\User;
use App\Models\Team;


class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'cgvu' => ['required', 'in:1'],
        ])->validate();

        $createdUser = User::create([
            'firstname' => $input['firstname'],
            'lastname' => $input['lastname'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'activated' => true,
        ]);
        
        $team = Team::create([
            'user_id' => $createdUser->id,
            'name' => 'Équipe de '.$createdUser->firstname.' '.$createdUser->lastname,
            'billing_name' => 'Équipe de '.$createdUser->firstname.' '.$createdUser->lastname,
            'billing_email' => $createdUser->email,
            'billing_country' => 'FR',
        ]);

        $createdUser->forceFill([
            'team_id' => $team->id,
            'role' => 'owner',
        ])->save();

        return $createdUser;
    }
}
