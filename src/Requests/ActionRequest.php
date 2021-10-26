<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Foundation\Http\FormRequest;


abstract class ActionRequest extends FormRequest
{
    /**
     * The action performed by the request
     *
     * @return array
     */
    abstract public function action();

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
