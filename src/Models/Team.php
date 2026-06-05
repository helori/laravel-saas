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
     * The attributes that are not mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function($team)
        {
            $trialDays = config('saas.trial_days');
            if($trialDays){
                $team->trial_ends_at = now()->addDays($trialDays);
            }
        });

        static::updated(function ($team)
        {
            if($team->hasStripeId())
            {
                $team->syncStripeCustomer();
            }
        });
    }

    /**
     * Get the owner of the team.
     */
    public function owner()
    {
        return $this->belongsTo(Saas::$userModel, 'user_id', 'id');
    }

    /**
     * Get all users that belong to the team.
     */
    public function users()
    {
        return $this->hasMany(Saas::$userModel, 'team_id');
    }

    /**
     * Remove a user from the team.
     */
    public function removeUser($user)
    {
        $user->forceFill([
            'team_id' => null,
            'role' => null
        ])->save();
    }

    /**
     * Determine if the given user belongs to the team.
     */
    public function hasUser($user): bool
    {
        return $user->team_id === $this->id;
    }

    /**
     * Determine if the given email belongs to a user on the team.
     */
    public function hasUserWithEmail(string $email): bool
    {
        return $this->users()->where('email', $email)->exists();
    }

    /**
     * Subscribe (or re-subscribe, or swap subscriptions)
     */
    public function subscribe(string $productId, string $priceId, ?string $promotionCode = null)
    {
        if(!$this->hasStripeId()){
            $this->createAsStripeCustomer([
                'email' => $this->owner->email,
            ]);
        }

        $paymentMethod = $this->defaultPaymentMethod();

        if(!$paymentMethod){
            abort(422, "Vous n'avez pas de moyen de paiement par défaut enregistré");
        }

        $subscriptionName = $productId;
        $subscription = $this->subscription($subscriptionName);
        $quantity = $this->users()->count();

        if(!$subscription || $subscription->ended())
        {
            $subscription = $this->newSubscription($productId, [ $priceId ]);
            $subscription->quantity($quantity, $priceId);

            $promotion = $this->getPromotionFromCode($promotionCode);
            if($promotion){
                $subscription->withPromotionCode($promotion->id);
            }

            $subscription->add();
        }
        else
        {
            if($subscription->hasPrice($priceId))
            {
                if($subscription->canceled())
                {
                    $subscription->resume();
                    $subscription->updateQuantity($quantity);

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
                $subscription->swap([$priceId]);
                $subscription->updateQuantity($quantity);

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

    // ------------------------------------
    // Stripe sync
    // ------------------------------------
    public function syncStripeCustomer()
    {
        $this->updateStripeCustomer([
            'name' => $this->stripeName(),
            'business_name' => $this->stripeBusinessName(),
            'individual_name' => $this->stripeIndividualName(),
            'email' => $this->stripeEmail(),
            'phone' => $this->stripePhone(),
            'address' => $this->stripeAddress(),
            'preferred_locales' => ['fr', 'en'],
        ]);
    }

    public function deleteStripeCustomer()
    {
        if($this->hasStripeId())
        {
            $this->stripe()->customers->delete($this->stripe_id);
            $this->stripe_id = null;
            $this->save();
        }
    }

    public function stripeName() { return $this->billing_name; }
    public function stripeBusinessName() { return $this->billing_name; }
    public function stripeIndividualName() { return $this->owner->firstname.' '.$this->owner->lastname; }
    public function stripeEmail() { return $this->billing_email; }
    public function stripePhone() { return $this->billing_phone; }

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
}
