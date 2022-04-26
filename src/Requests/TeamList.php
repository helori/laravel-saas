<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;


class TeamList extends ActionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

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
