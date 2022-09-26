<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;


class UserDelete extends ActionRequest
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
        $user = $this->user();

        // --------------------------------------------------------
        //  Vérification qu'aucun abonnement en cours
        // --------------------------------------------------------
        $ownTeams = $user->ownedTeams()->get();
        foreach($ownTeams as $team)
        {
            if($team->subscriptions()->recurring()->count() > 0)
            {
                abort(422, "Vous avez une souscription en cours. Vous devez la résilier pour supprimer votre compte.");
            }
            else if($team->subscriptions()->onGracePeriod()->count() > 0)
            {
                abort(422, "Vous avez une souscription résiliée qui n'est pas encore arrivée à échéance");
            }
        }

        // --------------------------------------------------------
        //  Détachements des membres des équipes détenues pas l'utilisateur
        //  Suppression des équipes détenues par l'utilisateur
        // --------------------------------------------------------
        foreach($ownTeams as $team)
        {
            $userIds = $team->users()->pluck('users.id')->all();
            $team->users()->detach(Arr::except($userIds, [ $user->id ]));
            $team->delete();
        }

        // --------------------------------------------------------
        //  Détachements de l'utilisateur de toutes ses autres équipes
        // --------------------------------------------------------
        $teams = $user->teams()->get();
        foreach($teams as $team)
        {
            $team->users()->detach($user->id);
        }
        
        // --------------------------------------------------------
        //  Suppression de l'utilisateur
        // --------------------------------------------------------
        $user->delete();
    }
}
