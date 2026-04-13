<?php

namespace Helori\LaravelSaas\Requests;

use Helori\LaravelSaas\Saas;


class MemberInvite extends ActionRequest
{
    public function authorize()
    {
        return $this->user()->ownTeam(Saas::$teamModel::findOrFail($this->route('teamId')));
    }

    public function action()
    {
        $team = Saas::$teamModel::findOrFail($this->route('teamId'));
        $member = $team->users()->findOrFail($this->route('memberId'));
        $member->invite();

        return $member;
    }
}
