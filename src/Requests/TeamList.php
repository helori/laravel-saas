<?php

namespace Helori\LaravelSaas\Requests;


class TeamList extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $this->user()->load('teams');
        return $this->user()->teams;
    }
}
