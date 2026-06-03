<?php

namespace Helori\LaravelSaas\Requests;


class ApiTokenList extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return \Illuminate\Support\Collection
     */
    public function action()
    {
        $user = $this->user();
        return $user->tokens()->get();
    }
}
