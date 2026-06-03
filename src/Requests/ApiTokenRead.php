<?php

namespace Helori\LaravelSaas\Requests;


class ApiTokenRead extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return \Laravel\Sanctum\PersonalAccessToken
     */
    public function action()
    {
        $tokenId = $this->route('tokenId');
        $token = $this->user()->tokens()->findOrFail($tokenId);
        return $token;
    }
}
