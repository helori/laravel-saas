<?php

namespace Helori\LaravelSaas\Requests;


class MemberDelete extends ActionRequest
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
        $memberId = $this->route('memberId');

        if($user->id == $memberId)
        {
            abort(422, "Vous ne pouvez pas vous supprimer vous-même");
        }

        $team = $user->teams()->findOrFail($teamId);

        if(!$user->ownTeam($team))
        {
            abort(403, "Vous ne pouvez pas supprimer les membres de l'équipe");
        }

        $member = $team->users()->findOrFail($memberId);
        $member->delete();
        
        return $member;
    }
}
