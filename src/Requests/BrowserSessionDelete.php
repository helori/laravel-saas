<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class BrowserSessionDelete extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        /*$confirmed = app(ConfirmPassword::class)(
            $guard, $this->user(), $this->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }*/

        $guard = Auth::guard('user');
        $guard->logoutOtherDevices($this->password);

        if (config('session.driver') === 'database')
        {
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', $this->user()->getAuthIdentifier())
                ->where('id', '!=', $this->session()->getId())
                ->delete();
        }
    }
}
