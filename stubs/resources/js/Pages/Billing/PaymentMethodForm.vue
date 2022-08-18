<template>
    <form-section>
        <template #title>
            Mode de paiement
        </template>

        <template #description>
            Enregistrez un moyen de paiement sécurisé.
        </template>

        <template #form>
            <div class="col-span-6">

                <alert 
                    type="success"
                    v-if="paymentMethod && !showForm">

                    <div v-if="paymentMethod.type == 'card'">
                        Votre moyen de paiement est une carte {{ paymentMethod.card.brand }}
                        terminant par {{ paymentMethod.card.last4 }}
                        qui expire fin 
                        {{ $filters.date(paymentMethod.card.exp_year + '-' + paymentMethod.card.exp_month + '-01', 'MMMM', 'YYYY-M-DD') }} 
                        {{ paymentMethod.card.exp_year }}
                    </div>

                    <div v-else-if="paymentMethod.type == 'sepa_debit'">
                        Votre moyen de paiement est une autorisation de prélèvement SEPA enregistrée le 
                        {{ $filters.date(paymentMethod.created, 'DD/MM/YYYY', 'X') }}
                        pour le compte bancaire dont les 4 derniers chiffres sont
                        {{ paymentMethod.sepa_debit.last4 }}.
                    </div>

                    <div v-else>
                        {{ paymentMethod }}
                    </div>

                </alert>

                <div v-show="showForm">

                    <div class="text-gray-600 dark:text-gray-200 mb-2">
                        Veuillez renseigner votre moyen de paiement par défaut :
                    </div>

                    <!-- Stripe Elements Placeholder -->
                    <div id="payment-element"></div>

                </div>

           </div>
        </template>

        <template #actions>

            <request-error :error="readError" class="inline-block" />
            <request-error :error="updateError" class="inline-block" />
            <request-error :error="deleteError" class="inline-block" />

            <button 
                v-show="paymentMethod && !showForm"
                class="btn btn-primary"
                @click="toggleForm(true)">
                Modifier
            </button>

            <button 
                v-show="paymentMethod && !showForm"
                class="btn btn-red"
                :disabled="deleteStatus === 'pending'"
                @click="openDialogConfirm">
                Supprimer le moyen de paiement
            </button>

            <button 
                v-show="showForm"
                class="btn btn-gray"
                :disabled="updateStatus === 'pending'"
                @click="toggleForm(false)">
                Annuler
            </button>
            
            <button 
                v-show="showForm"
                class="btn btn-primary"
                :disabled="updateStatus === 'pending' || !valid"
                @click="updatePayment">
                Enregistrer
            </button>

        </template>
    </form-section>

    <dialog-modal
        ref="dialogConfirm"
        max-width-class="max-w-screen-sm"
        header-class="bg-gray-100 dark:bg-gray-900 dark:text-red-400">
        
        <template #title>
            Attention
        </template>

        <template #content>
            <div class="text-red-500">
                Vos souscriptions en cours seront suspendues si vous n'enregistrez pas de nouveau moyen de paiement d'ici votre prochaine échéance.
                Voulez-vous vraiment supprimer votre moyen de paiement actuel ?
            </div>
        </template>
        
        <template #footer>
            <div class="flex items-center justify-end gap-2">
                <button 
                    type="button"
                    class="btn btn-gray"
                    @click="closeDialogConfirm">
                    Annuler
                </button>

                <button 
                    type="button"
                    class="btn btn-red"
                    @click="confirmDialogConfirm">
                    Supprimer
                </button>
            </div>
        </template>

    </dialog-modal>
    
</template>

<script>
    import { defineComponent, ref, onMounted } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import FormSection from '../../Components/FormSection'
    
    export default defineComponent({
        components: {
            FormSection,
        },

        props: {
            user: {
                required: true,
            }
        },

        setup(props) {

            const stripe = window.stripeKey ? Stripe(window.stripeKey) : null;
            let elements = null;

            const paymentElement = ref(null);
            const paymentMethod = ref(null);
            const valid = ref(null);
            const showForm = ref(false);
            
            const { 
                status: readStatus,
                error: readError,
                send: readSend,
            } = useForm();

            function readPayment()
            {
                readSend('get', '/payment-method').then(r => {
                    paymentMethod.value = r.data;
                    showForm.value = r.data ? false : true;
                });
            }

            onMounted(() => readPayment());

            const { 
                status: intentStatus,
                error: intentError,
                send: intentSend,
            } = useForm();

            const { 
                data: updateData,
                status: updateStatus,
                error: updateError,
                send: updateSend,
            } = useForm();

            async function updatePayment()
            {
                updateStatus.value = 'pending';

                // https://stripe.com/docs/js/setup_intents/confirm_setup
                const { setupIntent, error } = await stripe.confirmSetup({
                    elements, 
                    redirect: 'if_required',
                    confirmParams: {
                        return_url: window.location.protocol + "//" + window.location.host,
                    }
                });

                if(setupIntent)
                {
                    updateData.value.payment_method = setupIntent.payment_method;
                    updateSend('put', '/payment-method').then(r => {
                        readPayment();
                        paymentElement.value.clear();
                    });
                }
                else
                {
                    updateStatus.value = 'error';
                    updateError.value = error;
                }
            }

            function toggleForm(toggle)
            {
                showForm.value = toggle;
            }

            const { 
                status: deleteStatus,
                error: deleteError,
                send: deleteSend,
            } = useForm();

            function deletePayment(){
                deleteSend('delete', '/payment-method').then(r => {
                    readPayment();
                })
            }

            onMounted(() => {

                intentSend('get', '/payment-method-intent').then(r => {

                    elements = stripe.elements({
                        clientSecret: r.data.client_secret,
                        locale: 'fr',
                        loader: 'auto',
                        appearance: {
                            theme: 'night',
                            labels: 'floating',
                            variables: {
                                colorPrimary: '#43A6DA',
                                fontFamily: 'Quicksand',
                                /*colorBackground: '#ffffff',
                                colorText: '#30313d',
                                colorDanger: '#df1b41',
                                spacingUnit: '2px',
                                borderRadius: '4px',*/
                            }
                        },
                        fonts: [
                            {
                                cssSrc: 'https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500',
                            }
                        ],
                        
                    });

                    // https://stripe.com/docs/js/elements_object/create_payment_element
                    paymentElement.value = elements.create('payment');
                    paymentElement.value.mount('#payment-element');

                    paymentElement.value.on('change', function(event) 
                    {
                        valid.value = event.complete;

                        if (event.error) {
                            updateError.value = event.error;
                        } else {
                            updateError.value = null;
                        }
                    });
                });
            })

            const dialogConfirm = ref(null);

            function openDialogConfirm()
            {
                dialogConfirm.value.open();
            }

            function closeDialogConfirm()
            {
                dialogConfirm.value.close();
            }

            function confirmDialogConfirm()
            {
                dialogConfirm.value.close();
                deletePayment();
            }

            return {
                showForm,
                toggleForm,

                paymentMethod,
                paymentElement,
                valid,
                
                readPayment,
                readStatus,
                readError,
                
                updatePayment,
                updateStatus,
                updateError,

                deletePayment,
                deleteStatus,
                deleteError,

                dialogConfirm,
                openDialogConfirm,
                closeDialogConfirm,
                confirmDialogConfirm,
            }
        }
    })
</script>
