<template>
    <form-section>
        <template #title>
            {{ product.name }}
        </template>

        <template #description>
            Choisissez votre formule d'abonnement.
        </template>

        <template #form>
            <div class="col-span-6">

                <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                <!-- Sélection de l'abonnement -->
                <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                <div v-if="updating && createSubscriptionStatus !== 'pending'">

                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <!-- Price selection -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <div class="grid grid-cols-10 items-center gap-2">
                        <label 
                            for="price_id"
                            class="col-span-3 text-right">
                            Abonnement :
                        </label>
                        <select
                            id="price_id"
                            class="input col-span-7 w-full"
                            v-model="price">
                            <template v-for="p in product.prices">
                                <option
                                    v-if="p.published"
                                    :value="p">
                                    {{ p.name }}
                                </option>
                            </template>
                        </select>
                    </div>

                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <!-- Features -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <div class="grid grid-cols-10 items-center gap-2 my-4">
                        <div class="col-start-4 col-span-7 text-sm">
                            <features :features="price.features" />
                        </div>
                    </div>

                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <!-- Promo code -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <div class="grid grid-cols-10 items-center gap-2">
                        <label 
                            for="promo_code"
                            class="col-span-3 text-right">
                            Code de réduction :
                        </label>
                        <input
                            id="promo_code"
                            type="text"
                            v-model="createSubscriptionData.promo_code"
                            class="input col-span-7 w-full"
                            placeholder="Entrez votre code...">
                    </div>
                    
                </div>

                <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                <!-- Affichage de la souscription en cours -->
                <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                <div v-if="subscription && !updating 
                    && readSubscriptionStatus !== 'pending'">

                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <!-- Abonnement actuel -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <div class="">
                        <span>
                            Abonnement actuel :
                        </span>
                        <span class="font-bold">
                            {{ subscription.price.name }}
                        </span>
                    </div>

                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <!-- Features -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <features :features="subscription.price.features" class="my-4" />

                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <!-- Etat de l'abonnement -->
                    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                    <subscription-state :subscription="subscription" />

                </div>

                <request-state 
                    :error="readSubscriptionError" 
                    :status="readSubscriptionStatus" 
                    message="Chargement de votre abonnement..."
                    class="mt-2" />

                <request-state 
                    :error="createSubscriptionError" 
                    :status="createSubscriptionStatus" 
                    message="Création de votre abonnement..."
                    class="mt-2" />

                <!--request-state 
                    :error="deleteSubscriptionError" 
                    :status="deleteSubscriptionStatus" 
                    message="Résiliation de votre abonnement..."
                    class="mt-2" /-->

           </div>
        </template>

        <template #actions>

            <!--div class="alert alert-green mr-3" 
                v-if="createSubscriptionStatus === 'success'">
                Enregistré !
            </div>

            <div class="alert alert-green mr-3" 
                v-if="deleteSubscriptionStatus === 'success'">
                Enregistré !
            </div-->

            <template v-if="subscription && !updating">

                <!--a href="/app"
                    class="btn btn-primary"
                    v-if="subscription.is_active">
                    Accéder à l'application
                </a-->

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
                    @click="openDeleteSubscription">
                    Résilier...
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

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <!-- Delete Dialog -->
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <dialog-form 
        ref="deleteSubscriptionDialog"
        type="danger"
        title="Résilier votre abonnement"
        button="Résilier"
        cancel-text="Ne pas résilier"
        max-width-class="max-w-screen-md"
        :callback="deleteSubscription">
        <template #content>
            <div>
                La résiliation de votre abonnement prendra effet à la date de prochaine facturation.
                Votre équipe peut continuer à utiliser le service jusqu'à la fin de cette période.
            </div>
            <div class="h-px my-4 bg-gray-200"></div>
            <div>
                La résiliation de votre abonnement ne supprime pa votre compte : 
                si vous choisissez de reprendre un abonnement plus tard, vous retrouverez les membres de votre équipe et les données associées à votre compte.
            </div>
            <div class="h-px my-4 bg-gray-200"></div>
            <div>
                Si vous souhaitez résilier et être facturé immédiatement, vous pouvez cocher la case  ci-dessous.
                Votre équipe ne pourra plus utiliser le service jusqu'au bout de la période en cours.
            </div>
            <label class="mt-4 flex items-center gap-2"
                for="cancel-now">
                <input 
                    id="cancel-now"
                    type="checkbox" 
                    class="form-checkbox" 
                    name="cancel-now"
                    v-model="deleteSubscriptionDialog.data.cancel_now">
                <div>
                    Résilier et facturer immédiatement
                </div>
            </label>
        </template>
    </dialog-form>

</template>

<script>
    import { defineComponent, ref, computed, onMounted } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import CheckPaymentMethod from './CheckPaymentMethod'
    import SubscriptionState from './SubscriptionState'
    import Features from './Features'
    import FormSection from '../../Components/FormSection'
    import DialogForm from '../../Components/DialogForm'
    import find from 'lodash/find'
    
    export default defineComponent({
        components: {
            CheckPaymentMethod,
            SubscriptionState,
            Features,
            FormSection,
            DialogForm,
        },

        props: {
            user: {
                required: true,
            },
            product: {
                required: true,
            },
        },

        setup(props)
        {
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
                readSubscriptionParams.value.product_id = props.product.product_id;
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

            createSubscriptionData.value.promo_code = null;

            function createSubscription()
            {
                createSubscriptionData.value.product_id = props.product.product_id;
                createSubscriptionData.value.price_id = price.value.price_id;
                
                createSubscriptionSend('post', '/subscription').then(r => {
                    readSubscription();
                    updating.value = false;
                });
            }

            // ---------------------------------------------------
            //  Delete subscription
            // ---------------------------------------------------
            const deleteSubscriptionDialog = ref(null);

            /*const { 
                status: deleteSubscriptionStatus,
                error: deleteSubscriptionError,
                send: deleteSubscriptionSend,
                data: deleteSubscriptionData,
            } = useForm();*/

            function openDeleteSubscription()
            {
                deleteSubscriptionDialog.value.data = {
                    name: props.product.product_id,
                    cancel_now: false,   
                };
                deleteSubscriptionDialog.value.open();
            }

            function deleteSubscription()
            {
                deleteSubscriptionDialog.value.send('delete', '/subscription').then(r => {
                    deleteSubscriptionDialog.value.close();
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

                deleteSubscriptionDialog,
                /*deleteSubscriptionStatus,
                deleteSubscriptionError,
                deleteSubscriptionSend,
                deleteSubscriptionData,*/
                openDeleteSubscription,
                deleteSubscription,
            };
        }
    })
</script>
