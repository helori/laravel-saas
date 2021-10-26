<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Helori\LaravelSaas\Requests\UserPassword;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        $request = app(UserPassword::class);
        $request->action();
    }
}
