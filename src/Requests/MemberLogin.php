<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;
use Helori\LaravelSaas\Saas;


class MemberLogin extends ActionRequest
{
    public function authorize()
    {
        return $this->user()->ownTeam(Saas::$teamModel::findOrFail($this->route('teamId')));
    }

    public function action()
    {
        $team = Saas::$teamModel::findOrFail($this->route('teamId'));
        $member = $team->users()->findOrFail($this->route('memberId'));

        Auth::guard('user')->login($member);

        return redirect()->route('app');
    }
}
