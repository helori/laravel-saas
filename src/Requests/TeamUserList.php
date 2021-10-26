<?php

namespace Helori\LaravelSaas\Requests;


class TeamUserList extends ActionRequest
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
            abort(403, "Vous ne pouvez pas voir les membres de l'équipe");
        }

        return $team->users;
    }
}
