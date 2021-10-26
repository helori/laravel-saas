<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class UpdateUser extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'firstname' => 'required|string|max:191',
            'lastname' => 'required|string|max:191',
            'email' => [
                'required',
                'email',
                'max:191',
                Rule::unique('mandants')->ignore($this->id, 'id'),
                function ($attribute, $value, $fail) {
                    if(!filter_var($value, FILTER_VALIDATE_EMAIL))
                    {
                        $fail("Le format de l'email est incorrect");
                    }
                },
            ],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => "L'email est requis",
            'email.email' => "L'email est incorrect",
            'email.max' => "L'email ne doit pas dépasser 191 caractères",
            'email.unique' => "L'email est déjà utilisé",

            'firstname.required' => "Le prénom est requis",
            'firstname.max' => "Le prénom ne doit pas dépasser 191 caractères",

            'lastname.required' => "Le nom est requis",
            'lastname.max' => "Le nom ne doit pas dépasser 191 caractères",
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'firstname' => Str::ucfirst(Str::lower(trim($this->firstname))),
            'lastname' => Str::upper(trim($this->lastname)),
            'email' => Str::lower(trim($this->email)),
        ]);
    }

    /**
     * Apply the action the request is supposed to do
     *
     * @return void
     */
    protected function apply()
    {
        
    }
}
