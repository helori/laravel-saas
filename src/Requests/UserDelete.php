<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;


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
        $team = $user->team;

        if($user->role === 'owner' && $team)
        {
            // --------------------------------------------------------
            //  Vérification qu'aucun abonnement en cours
            // --------------------------------------------------------
            if($team->subscriptions()->recurring()->count() > 0)
            {
                abort(422, "Vous avez une souscription en cours. Vous devez la résilier pour supprimer votre compte.");
            }
            else if($team->subscriptions()->onGracePeriod()->count() > 0)
            {
                abort(422, "Vous avez une souscription résiliée qui n'est pas encore arrivée à échéance");
            }

            // --------------------------------------------------------
            //  Détachement des membres, suppression de l'équipe
            // --------------------------------------------------------
            $team->users()->where('id', '!=', $user->id)->update(['team_id' => null, 'role' => null]);
            $team->delete();
        }
        else if($team)
        {
            // Member leaving their team
            $user->forceFill(['team_id' => null, 'role' => null])->save();
        }

        // --------------------------------------------------------
        //  Suppression de l'utilisateur
        // --------------------------------------------------------
        $user->delete();
    }
}
