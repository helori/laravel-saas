<?php

namespace Helori\LaravelSaas\Requests;


class TeamUpdate extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $user = $this->user();
        $team = $user->currentTeam();

        if(!$user->ownTeam($team)){
            abort(403, "Vous n'avez pas le droit de modifier l'équipe");
        }
        
        $team->update($this->only([
            'name'
        ]));

        return $team;
    }
}
