<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Helori\LaravelSaas\Requests\UserUpdate;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        $request = app(UserUpdate::class);
        $request->action();
    }
}
