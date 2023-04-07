<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;


class AppController extends Controller
{
    public function home(Request $request)
    {
        return view('saas::home');
    }
    
    public function app(Request $request)
    {
        $user = Auth::guard('user')->user();

        $user->current_team = $user->currentTeam();
        $user->has_own_team = ($user->ownedTeams()->count() > 0);
        $user->two_factor_enabled = !is_null($user->two_factor_secret);
        //$user->can_do_something = Gate::allows('use-something');

        return view('saas::app', [
            'user' => $user,
            'laravelVersion' => app()->version(),
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
