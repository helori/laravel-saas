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
            'name',
            'billing_name',
            'billing_email',
            'billing_phone',
            'billing_line1',
            'billing_line2',
            'billing_postal_code',
            'billing_city',
            'billing_state',
            'billing_country',
        ]));

        return $team;
    }
}
