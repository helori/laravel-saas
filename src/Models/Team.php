<?php

namespace Helori\LaravelSaas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Cashier\Billable;
use Helori\LaravelSaas\Saas;


class Team extends Model
{
    use HasFactory, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_line1',
        'billing_line2',
        'billing_postal_code',
        'billing_city',
        'billing_state',
        'billing_country',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'is_pilote' => 'boolean',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [

    ];

    protected static function booted()
    {
        /**
         * This defines the "generic" trial period for a team,
         * regardless of the subscription / product
         */
        static::creating(function($team)
        {
            $trialDays = config('saas.trial_days');
            if($trialDays){
                $team->trial_ends_at = now()->addDays($trialDays);
            }
        });

        /**
         * This creates the team as a Stripe customer
         */
        static::created(function($team)
        {
            if(!$team->hasStripeId()){
                $team->createAsStripeCustomer([
                    'email' => $team->owner->email,
                ]);
            }
        });

        /**
         * Stripe customer details (country used to calculate tax rate)
         */
        static::updated(function ($billable)
        {
            if($billable->hasStripeId())
            {
                //$billable->syncStripeCustomerDetails();
                $billable->syncStripeCustomer();
            }
        });
    }

    /**
     * Sync billing details with Stripe
     *
     * @return null
     */
    public function syncStripeCustomer()
    {
        $this->updateStripeCustomer([
            'name' => $this->stripeName(),
            'email' => $this->stripeEmail(),
            'phone' => $this->stripePhone(),
            'address' => $this->stripeAddress(),
            'preferred_locales' => ['fr', 'en'],
        ]);
    }

    /**
     * Get the customer name that should be synced to Stripe.
     *
     * @return string|null
     */
    public function stripeName()
    {
        return $this->billing_name;
    }

    /**
     * Get the customer name that should be synced to Stripe.
     *
     * @return string|null
     */
    public function stripeEmail()
    {
        return $this->billing_email;
    }

    /**
     * Get the customer name that should be synced to Stripe.
     *
     * @return string|null
     */
    public function stripePhone()
    {
        return $this->billing_phone;
    }

    /**
     * Get the customer name that should be synced to Stripe.
     *
     * @return string|null
     */
    public function stripeAddress()
    {
        return [
            'line1' => $this->billing_line1,
            'line2' => $this->billing_line2,
            'postal_code' => $this->billing_postal_code,
            'city' => $this->billing_city,
            'state' => $this->billing_state,
            'country' => $this->billing_country,
        ];
    }

    /**
     * Get the owner of the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(Saas::$userModel, 'user_id', 'id');
    }

    /**
     * Get all of the users that belong to the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Saas::$userModel)
            ->using(Membership::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get all of the team's users including its owner.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allUsers()
    {
        return $this->users->merge([$this->owner]);
    }

    
    









    

    /**
     * Determine if the given user belongs to the team.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function hasUser($user)
    {
        return $this->users->contains($user) || $user->ownsTeam($this);
    }

    /**
     * Determine if the given email address belongs to a user on the team.
     *
     * @param  string  $email
     * @return bool
     */
    public function hasUserWithEmail(string $email)
    {
        return $this->allUsers()->contains(function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Determine if the given user has the given permission on the team.
     *
     * @param  \App\Models\User  $user
     * @param  string  $permission
     * @return bool
     */
    public function userHasPermission($user, $permission)
    {
        return $user->hasTeamPermission($this, $permission);
    }

    /**
     * Get all of the pending user invitations for the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamInvitations()
    {
        return $this->hasMany(TeamInvitation::class);
    }

    /**
     * Remove the given user from the team.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function removeUser($user)
    {
        if ($user->current_team_id === $this->id) {
            $user->forceFill([
                'current_team_id' => null,
            ])->save();
        }

        $this->users()->detach($user);
    }

    /**
     * Purge all of the team's resources.
     *
     * @return void
     */
    public function purge()
    {
        $this->owner()->where('current_team_id', $this->id)
                ->update(['current_team_id' => null]);

        $this->users()->where('current_team_id', $this->id)
                ->update(['current_team_id' => null]);

        $this->users()->detach();

        $this->delete();
    }
}
