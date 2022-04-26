<template>
    <form-section>
        <template #title>
            {{ product.name }}
        </template>

        <template #description>
            {{ product.short_description }}
        </template>

        <template #form>
            <div class="col-span-6">

                <div v-if="updating">

                    <div class="grid grid-cols-10 items-center gap-2">
                       
                        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                        <!-- Price selection -->
                        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                        <label 
                            for="price_id"
                            class="col-span-3 text-right">
                            Formule :
                        </label>
                        <select
                            id="price_id"
                            class="input col-span-7 w-full"
                            v-model="price">
                            <template v-for="p in product.prices">
                                <option
                                    v-if="p.published"
                                    :value="p">
                                    {{ p.name }} |
                                    {{ $filters.number(p.amount, 2) }}
                                    <span v-if="p.interval === 'month'">€ HT / mois</span>
                                    <span v-if="p.interval === 'year'">€ HT / an</span>
                                    + {{ $filters.number(priceSignatures.amount, 2) }} € HT / signature
                                </option>
                            </template>
                        </select>

                        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                        <!-- Coupon -->
                        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                        <label 
                            for="coupon"
                            class="col-span-3 text-right">
                            Code promotionnel :
                        </label>
                        <input
                            id="coupon"
                            type="text"
                            v-model="createSubscriptionData.coupon"
                            class="input col-span-7 w-full"
                            placeholder="Entrez votre code...">
                    </div>

                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <!-- Show product features -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <div v-if="product.features.length > 0"
                        v-for="feature in product.features">
                        <div v-if="feature === 'separator'" class="border-t border-gray-100 my-2 w-full"></div>
                        <div v-else class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-900 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="ml-2 text-gray-600 dark:text-gray-200">{{ feature }}</div>
                        </div>
                    </div>

                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <!-- Show selected price features -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <div v-if="price && price.features.length > 0">
                        <div v-for="feature in price.features">
                            <div v-if="feature === 'separator'" class="border-t border-gray-100 my-2 w-full"></div>
                            <div v-else class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-900 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="ml-2 text-gray-600 dark:text-gray-200">{{ feature }}</div>
                            </div>
                        </div>
                    </div>
                    
                </div>


                <div v-if="!updating && subscription">

                    <div class="font-bold text-lg">
                        {{ subscription.price.name }}
                    </div>

                    <div class="text-lg mb-2">
                        {{ $filters.number(subscription.price.amount, 0) }}
                        <span v-if="subscription.price.interval === 'month'">€ HT / mois</span>
                        <span v-if="subscription.price.interval === 'year'">€ HT / an</span>
                    </div>
                    
                    <div v-if="subscription.is_on_trial"
                        class="text-warning-500 font-bold">
                        Votre période d'essai se terminera le {{ $filters.date(subscription.trial_ends_at, 'DD/MM/YYYY') }}
                    </div>
                    <div v-if="subscription.is_recurring"
                        class="text-green-500 font-bold">
                        Votre abonnement est actif depuis le {{ $filters.date(subscription.created_at, 'DD/MM/YYYY') }}
                    </div>
                    <div v-if="subscription.is_on_grace_period"
                        class="text-red-500 font-bold">
                        Votre abonnement se terminera le {{ $filters.date(subscription.ends_at, 'DD/MM/YYYY') }}
                    </div>
                    <div v-if="subscription.is_ended"
                        class="text-red-500 font-bold">
                        Votre abonnement est terminé depuis le {{ $filters.date(subscription.ends_at, 'DD/MM/YYYY') }}
                    </div>

                </div>

           </div>
        </template>

        <template #actions>

            <error :errors="readSubscriptionError" class="inline-block" />
            <error :errors="createSubscriptionError" class="inline-block" />
            <error :errors="deleteSubscriptionError" class="inline-block" />

            <!--div class="alert alert-green mr-3" 
                v-if="createSubscriptionStatus === 'success'">
                Enregistré !
            </div>

            <div class="alert alert-green mr-3" 
                v-if="deleteSubscriptionStatus === 'success'">
                Enregistré !
            </div-->

            <template v-if="subscription && !updating">

                <a href="/app"
                    class="btn btn-primary"
                    v-if="subscription.is_active">
                    Accéder à l'application
                </a>

                <button 
                    type="button"
                    class="btn btn-primary"
                    @click="setUpdating(true)">
                    {{ subscription.is_cancelled ? "Reprendre un abonnement" : "Changer d'abonnement" }}
                </button>

                <button 
                    v-if="!subscription.is_cancelled"
                    type="button"
                    class="btn btn-red"
                    :class="{ 'opacity-25': (deleteSubscriptionStatus === 'pending') }"
                    :disabled="deleteSubscriptionStatus === 'pending'"
                    @click="deleteSubscription">
                    Résilier
                </button>

            </template>

            <template v-if="updating">

                <button 
                    v-if="subscription"
                    type="button"
                    class="btn btn-white"
                    @click="setUpdating(false)">
                    Annuler
                </button>

                <check-payment-method @checked="createSubscription">
                    <button 
                        type="button"
                        class="btn btn-primary"
                        :disabled="createSubscriptionStatus === 'pending'">
                        Souscrire
                    </button>
                </check-payment-method>
                
            </template>

        </template>
    </form-section>
</template>

<script>
    import { defineComponent, ref, computed, onMounted } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import CheckPaymentMethod from './CheckPaymentMethod'
    import FormSection from '../../Components/FormSection'
    import Error from '../../Components/Error'
    import find from 'lodash/find'
    
    export default defineComponent({
        components: {
            CheckPaymentMethod,
            FormSection,
            Error,
        },

        props: {
            user: {
                required: true,
            },
            product: {
                required: true,
            },
        },

        setup(props) {

            // ---------------------------------------------------
            //  Selected a default price
            // ---------------------------------------------------
            const price = ref(props.product.prices[0]);
            
            // ---------------------------------------------------
            //  Current subscription
            // ---------------------------------------------------
            const subscription = ref(null);

            const { 
                status: readSubscriptionStatus,
                error: readSubscriptionError,
                send: readSubscriptionSend,
                params: readSubscriptionParams,
            } = useForm();

            function readSubscription()
            {
                readSubscriptionParams.value.name = 'main';
                readSubscriptionSend('get', '/subscription').then(r => {
                    subscription.value = r.data;
                    if(!r.data){
                        updating.value = true;
                    }
                });
            };

            onMounted(readSubscription);

            // ---------------------------------------------------
            //  Create subscription
            // ---------------------------------------------------
            const { 
                status: createSubscriptionStatus,
                error: createSubscriptionError,
                send: createSubscriptionSend,
                data: createSubscriptionData,
            } = useForm();

            createSubscriptionData.value.coupon = null;

            function createSubscription()
            {
                createSubscriptionData.value.name = 'main';
                createSubscriptionData.value.product = props.product.product_id;
                createSubscriptionData.value.price = price.value.price_id;

                createSubscriptionSend('post', '/subscription').then(r => {
                    readSubscription();
                    updating.value = false;
                });
            }

            // ---------------------------------------------------
            //  Delete subscription
            // ---------------------------------------------------
            const { 
                status: deleteSubscriptionStatus,
                error: deleteSubscriptionError,
                send: deleteSubscriptionSend,
                data: deleteSubscriptionData,
            } = useForm();

            function deleteSubscription()
            {
                deleteSubscriptionData.value.name = 'main';
                deleteSubscriptionSend('delete', '/subscription').then(r => {
                    readSubscription();
                });
            }

            // ---------------------------------------------------
            //  Updating state
            // ---------------------------------------------------
            const updating = ref(false);

            function setUpdating(show)
            {
                createSubscriptionStatus.value = null;
                createSubscriptionError.value = null;
                deleteSubscriptionStatus.value = null;
                deleteSubscriptionError.value = null;
                updating.value = show;
            }

            return {
                price,
                subscription,

                updating,
                setUpdating,
                
                readSubscriptionStatus,
                readSubscriptionError,
                readSubscriptionSend,
                readSubscriptionParams,
                readSubscription,

                createSubscriptionStatus,
                createSubscriptionError,
                createSubscriptionSend,
                createSubscriptionData,
                createSubscription,

                deleteSubscriptionStatus,
                deleteSubscriptionError,
                deleteSubscriptionSend,
                deleteSubscriptionData,
                deleteSubscription,
            };
        }
    })
</script>
