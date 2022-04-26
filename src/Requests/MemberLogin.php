<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;


class MemberLogin extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $user = $this->user();
        $teamId = $this->route('teamId');
        $team = $user->teams()->with('users')->findOrFail($teamId);

        if(!$user->ownTeam($team))
        {
            abort(403, "Vous ne pouvez accéder aux membres de l'équipe");
        }

        $auth = Auth::guard('user');
        $memberId = $this->route('memberId');

        $auth->loginUsingId($memberId);
        $auth->user()->switchTeam($team);

        return redirect()->route('app');
    }
}
