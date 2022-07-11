<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;
use App\Models\User;


class MemberCreate extends ActionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();
        $team = $user->teams()->findOrFail($this->route('teamId'));
        return $user->ownTeam($team);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'firstname' => [
                'required', 
                'string'
            ],
            'lastname' => [
                'required', 
                'string'
            ],
            'email' => [
                'required', 
                'email'
            ],
            'password' => [
                'required', 
                'string', 
                new Password, 
                'confirmed'
            ],
            'role' => [
                'required', 
                'string',
                'in:member,owner'
            ],
            'invitation_email' => [
                'nullable', 
                'email',
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
            'firstname.required' => "Le prénom est requis",
            'lastname.required' => "Le nom est requis",
            'email.required' => "L'email est requis",
            'email.email' => "L'email n'est pas correct",
            'password.required' => "Le mot de passe est requis",
            'password.confirmed' => "La confirmation du mot de passe ne correspond pas",
            'role.required' => "Le rôle doit être membre ou propriétaire",
            'invitation_email.email' => "L'email d'invitation n'est pas correct",
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
        $teamId = $this->route('teamId');
        $team = $user->teams()->findOrFail($teamId);

        $member = new User();
        $member->fill($this->only([
            'firstname',
            'lastname',
            'password',
            'email',
            'phone',
            'activated',
        ]));
        $member->current_team_id = $team->id;
        $member->password = Hash::make($this->password);
        $member->save();

        $team->users()->attach($member, [
            'role' => $this->role,
        ]);

        if($this->invitation_email)
        {
            $member->invite($this->invitation_email);
        }

        return $member;
    }
}
