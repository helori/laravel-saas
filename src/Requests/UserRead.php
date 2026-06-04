<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;
use Helori\LaravelSaas\Resources\User as UserResource;


class UserRead extends ActionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('user')->check();
    }

    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        return new UserResource($this->user());
    }
}
