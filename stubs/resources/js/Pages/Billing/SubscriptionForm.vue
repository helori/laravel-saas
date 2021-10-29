<template>
    <form-section>
        <template #title>
            {{ product.name }}
        </template>

        <template #description>
            {{ product.short_description }}
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">

                <div v-if="updating">

                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <!-- If multiple plans : show selector -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <div class="mb-2"
                        v-if="product.plans.length > 1">
                        <select
                            class="input"
                            v-model="plan">
                            <option
                                v-for="p in product.plans"
                                :key="p.slug"
                                :value="p">
                                {{ p.name }}
                            </option>
                        </select>
                    </div>

                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <!-- Show selected plan only -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <div v-if="plan">

                        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                        <!-- If multiple prices : show selector -->
                        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                        <div class="mb-2"
                            v-if="plan.prices.length > 1">
                            <select
                                class="input"
                                v-model="price">
                                <option
                                    v-for="p in plan.prices"
                                    :key="p.slug"
                                    :value="p">
                                    {{ p.name }}
                                </option>
                            </select>
                        </div>

                        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                        <!-- Show selected price only -->
                        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                        <div v-if="price">

                            <div class="font-bold text-lg">
                                {{ plan.name }} - {{ price.name }}
                            </div>

                            <div v-for="p in plan.prices"
                                :key="p.slug">
                                <div v-if="p.slug == price.slug"
                                    class="text-lg mb-2">
                                    {{ p.price }} {{ p.unit }}  
                                </div>
                            </div>

                        </div>

                        <div v-for="feature in product.features"
                            class="flex">
                            <CheckCircleIcon class="h-5 w-5 text-green-500"/>
                            <div class="ml-2 text-gray-500">{{ feature }}</div>
                        </div>

                    </div>
                </div>


                <div v-if="!updating && subscription">
                    <div class="font-bold text-lg">
                        {{ subscription.plan.name }} - {{ subscription.price.name }}
                    </div>
                    <div class="text-lg mb-2">
                        {{ subscription.price.price }} {{ subscription.price.unit }}  
                    </div>
                    
                    <div v-if="subscription.is_on_trial"
                        class="text-warning-500 font-bold">
                        Votre période d'essai se terminera le {{ subscription.trial_ends_at }}
                    </div>
                    <div v-if="subscription.is_recurring"
                        class="text-green-500 font-bold">
                        Votre abonnement est actif depuis le {{ subscription.created_at }}
                    </div>
                    <div v-if="subscription.is_on_grace_period"
                        class="text-red-500 font-bold">
                        Votre abonnement se terminera le {{ subscription.ends_at }}
                    </div>
                    <div v-if="subscription.is_ended"
                        class="text-red-500 font-bold">
                        Votre abonnement est terminé depuis le {{ subscription.ends_at }}
                    </div>
                </div>

           </div>
        </template>

        <template #actions>

            <error :errors="readSubscriptionError" class="inline-block mr-3" />
            <error :errors="createSubscriptionError" class="inline-block mr-3" />
            <error :errors="deleteSubscriptionError" class="inline-block mr-3" />

            <!--div class="alert alert-green mr-3" 
                v-if="createSubscriptionStatus === 'success'">
                Enregistré !
            </div>

            <div class="alert alert-green mr-3" 
                v-if="deleteSubscriptionStatus === 'success'">
                Enregistré !
            </div-->

            <div v-if="subscription && !updating">

                <button 
                    type="button"
                    class="btn btn-blue"
                    @click="setUpdating(true)">
                    {{ subscription.is_cancelled ? "Reprendre un abonnement" : "Changer d'abonnement" }}
                </button>

                <button 
                    v-if="!subscription.is_cancelled"
                    type="button"
                    class="btn btn-red ml-3"
                    :class="{ 'opacity-25': (deleteSubscriptionStatus === 'pending') }"
                    :disabled="deleteSubscriptionStatus === 'pending'"
                    @click="deleteSubscription">
                    Résilier
                </button>

            </div>

            <div v-if="updating">

                <button 
                    v-if="subscription"
                    type="button"
                    class="btn btn-white mr-3"
                    @click="setUpdating(false)">
                    Annuler
                </button>

                <check-payment-method @checked="createSubscription">
                    <button 
                        type="button"
                        class="btn btn-blue"
                        :disabled="createSubscriptionStatus === 'pending'">
                        Souscrire
                    </button>
                </check-payment-method>
                
            </div>

        </template>
    </form-section>
</template>

<script>
    import { defineComponent, ref, computed, onMounted } from 'vue'
    import { CheckCircleIcon } from '@heroicons/vue/solid'
    import { useForm } from '../../Functions/useForm'
    import CheckPaymentMethod from './CheckPaymentMethod'
    import FormSection from '../../Components/FormSection'
    import Error from '../../Components/Error'
    import find from 'lodash/find'
    
    export default defineComponent({
        components: {
            CheckCircleIcon,
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
            //  Selected a default plan and a default price
            // ---------------------------------------------------
            const plan = ref(props.product.plans[0]);
            const price = ref(props.product.plans[0].prices[0]);
            
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
                readSubscriptionParams.value.product = props.product.slug;
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

            function createSubscription()
            {
                createSubscriptionData.value.product = props.product.slug;
                createSubscriptionData.value.plan = plan.value.slug;
                createSubscriptionData.value.price = price.value.slug;

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
                deleteSubscriptionData.value.product = props.product.slug;

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
                plan,
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
