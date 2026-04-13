<?php

namespace Helori\LaravelSaas\Requests;

use Helori\LaravelSaas\Saas;


class MemberUpdate extends ActionRequest
{
    public function authorize()
    {
        return $this->user()->ownTeam(Saas::$teamModel::findOrFail($this->route('teamId')));
    }

    public function action()
    {
        $user = $this->user();
        $memberId = $this->route('memberId');
        $team = Saas::$teamModel::findOrFail($this->route('teamId'));

        $member = $team->users()->findOrFail($memberId);

        $member->fill($this->only(['firstname', 'lastname', 'email', 'phone', 'activated']));

        if($this->has('role'))
        {
            if($user->id == $memberId && $this->role !== 'owner') {
                abort(422, "Vous ne pouvez pas ne plus être propriétaire de l'équipe");
            }
            $member->role = $this->role;
        }

        $member->save();

        return $member;
    }
}
