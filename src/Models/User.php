<?php

namespace Helori\LaravelSaas\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use function Illuminate\Events\queueable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
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
        'activated',
        'invited_at',
        'current_team_id',
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
        'activated' => 'boolean',
        'email_verified_at' => 'datetime',
        'invited_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function($user)
        {
            $teamModel = Saas::$teamModel;
            $team = new $teamModel();

            $team->user_id = $user->id;
            $team->name = 'Équipe de '.$user->firstname.' '.$user->lastname;
            
            $team->billing_name = $team->name;
            $team->billing_email = $user->email;
            $team->billing_phone = $user->phone;
            $team->billing_line1 = null;
            $team->billing_line2 = null;
            $team->billing_postal_code = null;
            $team->billing_city = null;
            $team->billing_country = 'FR';

            $user->teams()->save($team, [
                'role' => 'owner'
            ]);
        });
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
     * Determine if the user owns the current team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function ownCurrentTeam()
    {
        return $this->ownTeam($this->currentTeam());
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

    /**
     * Send an invitation to the team member
     */
    public function invite()
    {
        $broker = Password::broker('users');
        $token = $broker->createToken($this);
        $this->sendPasswordResetNotification($token);
        $this->invited_at = now();
        $this->save();
    }

    /**
     * Send a reset password notification
     */
    public function sendPasswordResetNotification($token){
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail($notification)
    {
        $name = $this->fistname.' '.$this->lastname;
        $email = $this->email;
        
        return [$email => $name];
    }
}
