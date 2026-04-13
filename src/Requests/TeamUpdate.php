<?php

namespace Helori\LaravelSaas\Requests;


class TeamUpdate extends ActionRequest
{
    public function authorize()
    {
        return $this->user()->ownCurrentTeam();
    }

    public function action()
    {
        $team = $this->user()->team;

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
