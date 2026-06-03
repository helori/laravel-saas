<?php

namespace Helori\LaravelSaas\Requests;


class ApiTokenCreate extends ActionRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    /**
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $user = $this->user();

        // Revoke all tokens
        //$user->tokens()->delete();

        // create new token
        $token = $user->createToken($this->input('name'));

        // Return the plain text version (visible only once !)
        return response($token->plainTextToken, 201);
    }
}
