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
        $tokens = $user->tokens()->get();
        return $tokens->count() ? $tokens->first() : response(null, 204);
    }
}
