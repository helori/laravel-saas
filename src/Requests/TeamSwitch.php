<?php

namespace Helori\LaravelSaas\Requests;

use Helori\LaravelSaas\Models\Team;


class TeamSwitch extends ActionRequest
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

        if(!$user->switchTeam(Team::findOrFail($teamId)))
        {
            abort(403, 'Vous ne faites pas partie de cette équipe');
        }
    }
}
