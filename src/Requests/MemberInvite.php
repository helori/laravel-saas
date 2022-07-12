<?php

namespace Helori\LaravelSaas\Requests;


class MemberInvite extends ActionRequest
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
        $teamId = $this->route('teamId');
        $memberId = $this->route('memberId');

        $user = $this->user();
        $team = $user->teams()->findOrFail($teamId);
        $member = $team->users()->findOrFail($memberId);

        $member->invite();

        return $member;
    }
}
