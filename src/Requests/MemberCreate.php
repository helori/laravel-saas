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
                'email',
                'unique:users',
            ],
            'has_password' => [
                'required',
                'boolean',
            ],
            'password' => [
                'exclude_if:has_password,false',
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
            'invite' => [
                'sometimes', 
                'boolean',
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
            'email.unique' => "L'email est déjà pris",
            'has_password.required' => "Faut-il créer un mot de passe ?",
            'password.required' => "Le mot de passe est requis",
            'password.confirmed' => "La confirmation du mot de passe ne correspond pas",
            'role.required' => "Le rôle doit être membre ou propriétaire",
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

        // Make sure the user will be connected to this team after it's first connexion !
        // (By default, a user is connected to its personnal team)
        $member->current_team_id = $team->id;

        // The IP is the team's owner IP (should be overwritten at the user's first login ?)
        $member->ip = $this->ip();

        // Since the member is created by a verified user, there is no need for email verification
        $member->email_verified_at = now();
        
        if($member->has_password){
            $member->password = Hash::make($this->password);
        }else{
            // Create a dummy password (the user can be invited to reset his password)
            $password = bin2hex(openssl_random_pseudo_bytes(4)); // 8 chars password
            $member->password = Hash::make($password);
        }

        $member->save();

        $team->users()->attach($member, [
            'role' => $this->role,
        ]);

        if($this->has('invite') && $this->invite)
        {
            $member->invite();
        }

        return $member;
    }
}
