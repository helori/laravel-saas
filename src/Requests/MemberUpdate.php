<?php

namespace Helori\LaravelSaas\Requests;


class MemberUpdate extends ActionRequest
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
        $memberId = $this->route('memberId');

        $team = $user->teams()->findOrFail($teamId);
        if(!$user->ownTeam($team))
        {
            abort(403, "Vous ne pouvez pas modifier les membres de l'équipe");
        }

        $member = $team->users()->findOrFail($memberId);
        
        $member->update($this->only([
            'firstname',
            'lastname',
            'email',
            'phone',
        ]));

        $member->save();

        return $member;
    }
}
