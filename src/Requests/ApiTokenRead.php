<?php

namespace Helori\LaravelSaas\Requests;


class ApiTokenRead extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $user = $this->user();
        return $user->tokens()->count() ? $user->tokens()->first() : null;
    }
}
