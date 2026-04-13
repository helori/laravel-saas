<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;


class TeamList extends ActionRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function action()
    {
        return [$this->user()->load('team')->team];
    }
}
