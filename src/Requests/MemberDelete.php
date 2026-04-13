<?php

namespace Helori\LaravelSaas\Requests;

use Helori\LaravelSaas\Saas;


class MemberDelete extends ActionRequest
{
    public function authorize()
    {
        return $this->user()->ownTeam(Saas::$teamModel::findOrFail($this->route('teamId')));
    }

    public function action()
    {
        $user = $this->user();
        $memberId = $this->route('memberId');

        if($user->id == $memberId) {
            abort(422, "Vous ne pouvez pas vous supprimer vous-même");
        }

        $team = Saas::$teamModel::findOrFail($this->route('teamId'));
        $member = $team->users()->findOrFail($memberId);
        $member->delete();

        return $member;
    }
}
