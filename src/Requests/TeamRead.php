<?php

namespace Helori\LaravelSaas\Requests;

use Illuminate\Support\Facades\Auth;


class TeamRead extends ActionRequest
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

        $team = $user->teams()->with('users')->findOrFail($user->current_team_id);
        
        return $team;

        //$request = app(TeamReadContract::class);
        //$request->action();

        /*$reader = app(TeamReadContract::class);
        $reader->read($request->user(), $teamId);

        $team = Team::findOrFail($teamId);

        Gate::authorize('view', $team);

        return [
            'team' => $team->load('owner', 'users', 'teamInvitations'),
            'availableRoles' => array_values(Jetstream::$roles),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
            'permissions' => [
                'canAddTeamMembers' => Gate::check('addTeamMember', $team),
                'canDeleteTeam' => Gate::check('delete', $team),
                'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
                'canUpdateTeam' => Gate::check('update', $team),
            ],
        ];*/
    }
}
