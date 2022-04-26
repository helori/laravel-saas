<?php

namespace Helori\LaravelSaas\Requests;


class TeamUpdate extends ActionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->ownCurrentTeam();
    }

    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $user = $this->user();
        $team = $user->currentTeam();
        
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
