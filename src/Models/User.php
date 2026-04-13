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
use App\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use App\Notifications\Auth\VerifyEmail as VerifyEmailNotification;
use Helori\LaravelSaas\Saas;


class User extends Authenticatable implements MustVerifyEmail
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
        'team_id',
        'role',
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

    /**
     * Get the team the user belongs to.
     */
    public function team()
    {
        return $this->belongsTo(Saas::$teamModel, 'team_id');
    }

    /**
     * Determine if the user owns the given team.
     */
    public function ownTeam($team): bool
    {
        return $this->team_id === $team->id && $this->role === 'owner';
    }

    /**
     * Determine if the user owns their current team.
     */
    public function ownCurrentTeam(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Get the billable model the user depends on (the team).
     * Only the team owner can access billing.
     */
    public function billable()
    {
        if($this->role !== 'owner') {
            abort(403, "You cannot access your team's subscriptions");
        }
        return $this->team;
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
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
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
