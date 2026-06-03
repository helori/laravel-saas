<?php

namespace Helori\LaravelSaas\Requests;


class ApiTokenDelete extends ActionRequest
{
    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $tokenId = $this->route('tokenId');
        return $this->user()->tokens()->findOrFail($tokenId)->delete();
    }
}
