<template>
    <span>
        <span @click="readPayment">
            <slot />
        </span>

        <dialog-modal 
            :show="updatingPaymentMethod" 
            @close="closeModal"
            max-width-class="max-w-screen-md">

            <template #title>
                Moyen de paiement
            </template>

            <template #content>

                <div class="text-gray-600 dark:text-gray-200 mb-2">
                    Veuillez saisir votre moyen de paiement qui sera utilisé par défaut :
                </div>

                <!-- Stripe Elements Placeholder -->
                <div id="payment-element-check"></div>

            </template>

            <template #footer>

                <request-error :error="readError" class="inline-block mr-3" />
                <request-error :error="updateError" class="inline-block mr-3" />

                <button 
                    type="button"
                    class="btn btn-gray"
                    v-show="updateStatus !== 'pending'"
                    @click="closeModal">
                    Annuler
                </button>

                <button 
                    class="btn btn-primary ml-2"
                    :disabled="updateStatus === 'pending' || !valid"
                    @click="updatePayment">
                    Enregistrer
                </button>

            </template>
        </dialog-modal>
        
    </span>
</template>

<script>
    import { defineComponent, ref, nextTick } from 'vue'
    import { useForm } from '../../Functions/useForm'
    
    export default defineComponent({
        
        emits: ['checked'],

        setup(props, { emit }){

            const stripe = window.stripeKey ? Stripe(window.stripeKey) : null;
            let elements = null;

            const paymentElement = ref(null);
            const paymentMethod = ref(null);
            const updatingPaymentMethod = ref(false);
            const valid = ref(false);

            const { 
                status: intentStatus,
                error: intentError,
                send: intentSend,
            } = useForm();

            const { 
                status: readStatus,
                error: readError,
                send: readSend,
            } = useForm();

            const { 
                data: updateData,
                status: updateStatus,
                error: updateError,
                send: updateSend,
            } = useForm();

            const showForm = async () => {

                await nextTick();

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
                    paymentElement.value.mount('#payment-element-check');

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
            }

            function readPayment()
            {
                readSend('get', '/payment-method').then(r => {
                    if(r.data){
                        emit('checked');
                        paymentMethod.value = r.data;
                    }else{
                        updatingPaymentMethod.value = true;
                        showForm();
                    }
                });
            }

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
                        emit('checked');
                        closeModal();
                    });
                }
                else
                {
                    updateStatus.value = 'error';
                    updateError.value = error;
                }
            }

            function closeModal(){
                updatingPaymentMethod.value = false;
            }

            return {
                paymentElement,
                paymentMethod,
                updatingPaymentMethod,
                valid,

                showForm,
                closeModal,

                readPayment,
                readStatus,
                readError,
                
                intentStatus,
                intentError,

                updatePayment,
                updateData,
                updateStatus,
                updateError,
            };
        },
    })
</script>
