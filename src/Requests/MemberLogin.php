<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;


class MemberLogin extends ActionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();
        $team = $user->teams()->findOrFail($this->route('teamId'));
        return $user->ownTeam($team);
    }

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

        $auth = Auth::guard('user');
        $memberId = $this->route('memberId');

        $auth->loginUsingId($memberId);
        $auth->user()->switchTeam($team);

        return redirect()->route('app');
    }
}
