<?php

namespace Helori\LaravelSaas\Requests;


class MemberDelete extends ActionRequest
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

        if($user->id == $memberId)
        {
            abort(422, "Vous ne pouvez pas vous supprimer vous-même");
        }

        $team = $user->teams()->findOrFail($teamId);

        if(!$user->ownTeam($team))
        {
            abort(403, "Vous ne pouvez pas supprimer les membres de l'équipe");
        }

        $team->users()->findOrFail($memberId)->delete();
    }
}
