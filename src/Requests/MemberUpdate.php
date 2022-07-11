<?php

namespace Helori\LaravelSaas\Requests;


class MemberUpdate extends ActionRequest
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
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $user = $this->user();
        $teamId = $this->route('teamId');
        $memberId = $this->route('memberId');

        $team = $user->teams()->findOrFail($teamId);
        if(!$user->ownTeam($team))
        {
            abort(403, "Vous ne pouvez pas modifier les membres de l'équipe");
        }

        $member = $team->users()->findOrFail($memberId);
        
        $member->fill($this->only([
            'firstname',
            'lastname',
            'email',
            'phone',
            'activated',
        ]));

        if($this->has('role'))
        {
            if(($user->id == $memberId) && ($this->role != 'owner')){
                abort(422, "Vous ne pouvez pas ne plus être propriétaire de l'équipe");
            }else{
                $member->pivot->role = $this->role;
                $member->pivot->save();
            }
        }

        $member->save();

        return $member;
    }
}
