<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class UserUpdate extends ActionRequest
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
                Rule::unique('users')->ignore($this->user()->id, 'id'),
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
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $user = $this->user();
        $data = $this->validated();

        if ($data['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $data);
        } else {
            $user->forceFill([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser($user, array $data)
    {
        $user->forceFill([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
