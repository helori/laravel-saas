<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;
use App\Models\User;
use Helori\LaravelSaas\Saas;


class MemberCreate extends ActionRequest
{
    public function authorize()
    {
        return $this->user()->ownTeam(Saas::$teamModel::findOrFail($this->route('teamId')));
    }

    public function rules()
    {
        return [
            'firstname' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'has_password' => ['required', 'boolean'],
            'password' => [
                'exclude_if:has_password,false',
                'required', 'string', new Password, 'confirmed',
            ],
            'role' => ['required', 'string', 'in:member,owner'],
            'invite' => ['sometimes', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'firstname.required' => "Le prénom est requis",
            'lastname.required' => "Le nom est requis",
            'email.required' => "L'email est requis",
            'email.email' => "L'email n'est pas correct",
            'email.unique' => "L'email est déjà pris",
            'has_password.required' => "Faut-il créer un mot de passe ?",
            'password.required' => "Le mot de passe est requis",
            'password.confirmed' => "La confirmation du mot de passe ne correspond pas",
            'role.required' => "Le rôle doit être membre ou propriétaire",
        ];
    }

    public function action()
    {
        $teamId = $this->route('teamId');
        $team = Saas::$teamModel::findOrFail($teamId);

        $member = new User();
        $member->fill($this->only(['firstname', 'lastname', 'email', 'phone', 'activated']));
        $member->team_id = $team->id;
        $member->role = $this->role;
        $member->ip = $this->ip();
        $member->email_verified_at = now();

        if($this->has_password){
            $member->password = Hash::make($this->password);
        } else {
            $password = bin2hex(openssl_random_pseudo_bytes(4));
            $member->password = Hash::make($password);
        }

        $member->save();

        if($this->has('invite') && $this->invite) {
            $member->invite();
        }

        return $member;
    }
}
