<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;


class TeamCreate extends ActionRequest
{
    //use ActionWhenResolvedTrait;

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
     * Run the action the request is supposed to execute
     *
     * @return void
     */
    public function action()
    {
        $user = $this->user();

        //$team = $user->teams()->with('users')->findOrFail($user->current_team_id);

        $ownTeamCount = $user->ownedTeams()->count();
        if($ownTeamCount > 0)
        {
            abort(422, "Vous êtes déjà propriétaire d'une équipe !");
        }
        
        $ownTeam = $user->createOwnTeam();

        // Pas de période d'essai sur les nouvelles équipes !
        $ownTeam->trial_ends_at = now();
        $ownTeam->save();
        
        $user->switchTeam($ownTeam);

        return $ownTeam;
    }
}
