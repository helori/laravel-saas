<?php

namespace Helori\LaravelSaas\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use function Illuminate\Events\queueable;
use Helori\LaravelSaas\Saas;


class User extends Authenticatable
{
    use Notifiable, TwoFactorAuthenticatable, HasApiTokens, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_root' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function($user){
            $teamModel = Saas::$teamModel;
            $team = new $teamModel();
            $team->name = 'Équipe de '.$user->firstname.' '.$user->lastname;
            $team->user_id = $user->id;
            $user->teams()->save($team, [
                'role' => 'owner'
            ]);
        });
        /*static::updated(queueable(function ($customer) {
            $customer->syncStripeCustomerDetails();
        }));*/
    }

    /**
     * Get all of the teams the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Saas::$teamModel)
            ->using(Membership::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get all of the teams owned by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedTeams()
    {
        return $this->hasMany(Saas::$teamModel);
    }

    /**
     * Get the current team of the user's context.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentTeam()
    {
        $this->ensureHasCurrentTeam();
        //return $this->belongsTo(Saas::$teamModel, 'current_team_id');

        // Important when calling currentTeam() after modifying the user's team,
        // (For example after creating the stripe_id of the billable team)
        $this->load('teams');

        $currentTeamId = $this->current_team_id;
        return $this->teams->first(function ($team) use($currentTeamId) {
            return ($team->id === $currentTeamId);
        });
    }

    public function ensureHasCurrentTeam()
    {
        if (is_null($this->current_team_id) && $this->id) {
            $this->switchTeam($this->ownedTeams()->first());
        }
    }

    /**
     * Switch the user's context to the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function switchTeam($team)
    {
        if (! $this->belongsToTeam($team)) {
            return false;
        }

        $this->forceFill([
            'current_team_id' => $team->id,
        ])->save();

        $this->setRelation('currentTeam', $team);

        return true;
    }

    /**
     * Determine if the user belongs to the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function belongsToTeam($team)
    {
        return $this->teams->contains(function ($t) use ($team) {
            return $t->id === $team->id;
        });
    }

    /**
     * Determine if the user owns the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function ownTeam($team)
    {
        return $this->ownedTeams->contains(function ($ownedTeam) use ($team) {
            return $ownedTeam->id === $team->id;
        });
    }

    /**
     * Get the billable model the user depends of.
     * It can be the user itself or the user's team depending on the app configuration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function billable()
    {
        if(true) // For now, we only support team billing
        {
            $currentTeam = $this->currentTeam();
            if(!$this->ownTeam($currentTeam)){
                abort(403, "You cannot access your team's subscriptions");
            }
            return $currentTeam;
        }
        else
        {
            return $this;
        }
    }
}
