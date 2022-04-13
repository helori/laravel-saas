<template>
    <span>
        <span @click="read">
            <slot />
        </span>

        <dialog-modal 
            :show="updatingPaymentMethod" 
            @close="closeModal"
            max-width="sm">
            <template #title>
                Moyen de paiement
            </template>

            <template #content>

                <div class="max-w-xl text-sm text-gray-600 dark:text-gray-200 mb-2">
                    Veuillez saisir le numéro de carte bancaire qui sera votre moyen de paiement par défaut :
                </div>

                <!-- Stripe Elements Placeholder -->
                <div id="card-element-check"></div>

            </template>

            <template #footer>

                <error :errors="readError" class="inline-block mr-3" />
                <error :errors="updateError" class="inline-block mr-3" />

                <button 
                    type="button"
                    class="btn btn-white"
                    @click="closeModal">
                    Annuler
                </button>

                <button 
                    id="card-button" 
                    class="btn btn-primary ml-3"
                    :disabled="updateStatus === 'pending'"
                    @click="update">
                    Enregistrer la carte
                </button>

            </template>
        </dialog-modal>
    </span>
</template>

<script>
    import { defineComponent, ref, nextTick } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import DialogModal from '../../Components/DialogModal.vue'
    import Error from '../../Components/Error.vue'
    
    export default defineComponent({
        
        emits: ['checked'],

        components: {
            DialogModal,
            Error,
        },

        setup(){

            const stripe = window.stripeKey ? Stripe(window.stripeKey) : null;
            const cardElement = ref(null);
            const intent = ref(null);

            const showForm = async () => {

                await nextTick();

                const elements = stripe.elements();
                cardElement.value = elements.create('card', {
                    hidePostalCode: true,
                    hideIcon: false,
                    iconStyle: 'solid',
                    classes: {
                        base: 'input appearance-none bg-white border-gray-300 dark:border-gray-500 dark:bg-gray-700 dark:text-gray-200 py-2 px-3 text-base border',
                    },
                    style: {
                        base: {
                            fontFamily: 'Nunito',
                            '::placeholder': {
                                color: '#94A3B8',
                            },
                        }
                    }
                });
                cardElement.value.mount('#card-element-check');

                cardElement.value.on('change', function(event) {
                    if (event.error) {
                        updateError.value = event.error;
                    } else {
                        updateError.value = null;
                    }
                });
            }

            const updatingPaymentMethod = ref(false);

            const { 
                status: readStatus,
                error: readError,
                send: readSend,
            } = useForm();

            function read(){
                readSend('get', '/card').then(r => {
                    if(r.data){
                        this.$emit('checked')
                    }else{
                        updatingPaymentMethod.value = true;

                        // https://stripe.com/docs/js/elements_object/create_element?type=card#elements_create-options

                        showForm();
                    }
                });
            }
            
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


            async function update()
            {
                updateStatus.value = 'pending';

                await intentSend('get', '/card-intent').then(r => {
                    intent.value = r.data.client_secret;
                });

                const { setupIntent, error } = await stripe.confirmCardSetup(
                    intent.value, {
                        payment_method: {
                            card: cardElement.value,
                            /*billing_details: {
                                name: cardHolderName.value
                            }*/
                        }
                    }
                );

                if(setupIntent)
                {
                    updateData.value.payment_method = setupIntent.payment_method;
                    updateSend('put', '/card').then(r => {
                        this.$emit('checked');
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
                cardElement,
                intent,
                showForm,

                updatingPaymentMethod,

                readStatus,
                readError,
                readSend,
                read,

                intentStatus,
                intentError,
                intentSend,

                updateData,
                updateStatus,
                updateError,
                updateSend,
                update,

                closeModal,
            };
        },
    })
</script>
