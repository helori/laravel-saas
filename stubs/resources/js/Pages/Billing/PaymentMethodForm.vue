<template>
    <form-section>
        <template #title>
            Mode de paiement
        </template>

        <template #description>
            Enregistrez un moyen de paiement sécurisé.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">

                <div v-if="card && !showForm" class="mb-2">
                    Votre moyen de paiement est une carte {{ card.card.brand }}
                    terminant par {{ card.card.last4 }}
                    et qui expire fin {{ card.card.exp_month }} / {{ card.card.exp_year }}
                </div>

                <div v-show="showForm">

                    <div class="max-w-xl text-sm text-gray-600 dark:text-gray-200 mb-2">
                        Veuillez saisir le numéro de carte bancaire qui sera votre moyen de paiement par défaut :
                    </div>

                    <!-- Stripe Elements Placeholder -->
                    <div id="card-element"></div>

                </div>

           </div>
        </template>

        <template #actions>

            <error :errors="readError" class="inline-block" />
            <error :errors="updateError" class="inline-block" />
            <error :errors="deleteError" class="inline-block" />

            <button 
                v-show="card && !showForm"
                id="card-button" 
                class="btn btn-blue"
                @click="toggleForm(true)">
                Modifier
            </button>

            <button 
                v-show="card && !showForm"
                id="card-button" 
                class="btn btn-red"
                :disabled="deleteStatus === 'pending'"
                @click="deleteCard">
                Supprimer la carte
            </button>

            <button 
                v-show="showForm"
                id="card-button" 
                class="btn btn-blue"
                :disabled="updateStatus === 'pending'"
                @click="updateCard">
                Enregistrer la carte
            </button>

        </template>
    </form-section>
</template>

<script>
    import { defineComponent, ref, onMounted } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import FormSection from '../../Components/FormSection'
    import Error from '../../Components/Error'
    
    export default defineComponent({
        components: {
            FormSection,
            Error,
        },

        props: {
            user: {
                required: true,
            }
        },

        setup(props) {

            const stripe = window.stripeKey ? Stripe(window.stripeKey) : null;
            const cardElement = ref(null);
            const card = ref(null);
            const intent = ref(null);
            const showForm = ref(false);

            const { 
                status: readStatus,
                error: readError,
                send: readSend,
            } = useForm();

            function readCard()
            {
                readSend('get', '/card').then(r => {
                    card.value = r.data;
                    showForm.value = r.data ? false : true;
                });
            }

            onMounted(() => readCard());


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


            async function updateCard()
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
                        readCard();
                        cardElement.value.clear();
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

            function deleteCard(){
                deleteSend('delete', '/card').then(r => {
                    readCard();
                })
            }

            onMounted(() => {

                // https://stripe.com/docs/js/elements_object/create_element?type=card#elements_create-options

                const elements = stripe.elements();
                cardElement.value = elements.create('card', {
                    hidePostalCode: true,
                    hideIcon: false,
                    iconStyle: 'solid',
                    classes: {
                        base: 'input appearance-none bg-white text-base border-gray-300 dark:border-gray-500 dark:bg-gray-700 dark:text-gray-200 dark:placeholder:text-gray-400 py-2 px-3 border',
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
                cardElement.value.mount('#card-element');

                cardElement.value.on('change', function(event) {
                    if (event.error) {
                        updateError.value = event.error;
                    } else {
                        updateError.value = null;
                    }
                });
            })

            return {
                showForm,
                toggleForm,

                card,
                readStatus,
                readError,
                readSend,
                readCard,

                intent,
                cardElement,
                //cardHolderName,
                
                updateData,
                updateStatus,
                updateError,
                updateSend,
                updateCard,

                deleteStatus,
                deleteError,
                deleteSend,
                deleteCard,
            }
        }
    })
</script>
