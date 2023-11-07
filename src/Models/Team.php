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
        'logo',
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
         * Create the team as a Stripe customer
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
     * Delete the Stripe customer 
     *
     * @return null
     */
    public function deleteStripeCustomer()
    {
        if($this->hasStripeId())
        {
            $this->stripe()->customers->delete($this->stripe_id);
            $this->stripe_id = null;
            $this->save();
        }
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
     * Subscribe (or re-subscribe, or swap subscriptions)
     *
     * @return void
     */
    public function subscribe(string $productId, string $priceId, ?string $promotionCode = null)
    {
        // --------------------------------------------------------
        //  Create stripe customer if necessary
        // --------------------------------------------------------
        if(!$this->hasStripeId()){
            $this->createAsStripeCustomer([
                'email' => $this->owner()->email,
            ]);
        }

        // --------------------------------------------------------
        //  Get payment method
        // --------------------------------------------------------
        $paymentMethod = $this->defaultPaymentMethod();
        if(!$paymentMethod){
            abort(422, "Vous n'avez pas de moyen de paiement par défaut enregistré");
        }

        // ----------------------------------------------------------------
        //  Souscription : porte le nom du produit
        // ----------------------------------------------------------------
        $subscriptionName = $productId;
        $subscription = $this->subscription($subscriptionName);
        
        // Pas de souscription (ou terminée) => création d'une nouvelle souscription
        if(!$subscription || $subscription->ended())
        {
            $subscription = $this->newSubscription($productId, [$priceId/*, $priceMembersId*/]);
            
            $subscription->quantity(1, $priceId);
            
            $promotion = $this->getPromotionFromCode($promotionCode);
            if($promotion){
                $subscription->withPromotionCode($promotion->id);
            }

            $subscription->add();
            
            // TODO : comment gérer les cartes refusées ?
            // Voir la doc Cashier sur le sujet
            /*try 
            {
                // Indispensable d'avoir une paymentMethod déjà enregistrée
                $subscription->add();
            }
            catch (IncompletePayment $exception)
            {
                abort(400, route('cashier.payment', [
                    $exception->payment->id, 
                    //'redirect' => route('home')
                ]));
            }*/
        }
        else // Il y a une souscription en cours
        {
            // Déjà souscrit au tarif demandé
            if($subscription->hasPrice($priceId))
            {
                if($subscription->canceled())
                {
                    $subscription->resume();

                    $promotion = $this->getPromotionFromCode($promotionCode);
                    if($promotion){
                        $subscription->applyPromotionCode($promotion->id);
                    }
                }
                else{
                    abort(422, "Vous êtes déjà abonné à ce tarif !");
                }
            }
            else
            {
                $subscription->swap([
                    $priceId,
                ]);
                
                $promotion = $this->getPromotionFromCode($promotionCode);
                if($promotion){
                    $subscription->applyPromotionCode($promotion->id);
                }
            }
        }
    }

    public function getPromotionFromCode($promotionCode)
    {
        $promotion = null;
        if($promotionCode)
        {
            $promotion = $this->findPromotionCode($promotionCode);
            if(!$promotion){
                abort(422, "Code promotionnel introuvable");
            }
        }
        return $promotion;
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
