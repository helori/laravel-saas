<?php

namespace App\Http\Controllers;

use Helori\LaravelSaas\Controllers\AppController as BaseController;


class AppController extends BaseController
{
    public function home(Request $request)
    {
        return view('saas::home');
    }
    
    public function app(Request $request)
    {
        $user = Auth::guard('user')->user();

        $user->current_team = $user->currentTeam();

        if (!is_null($user))
        {
            $user->two_factor_enabled = !is_null($user->two_factor_secret);
        }

        return view('saas::app', [
            'user' => $user,
            'laravelVersion' => app()->version(),
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
