<template>
    <div>
        <div class="max-w-7xl mx-auto sm:py-10 sm:px-6 lg:px-8">

            <template v-for="product in products"
                :key="product.product_id">
                <subscription-form 
                    :user="user"
                    :product="product" />
                <separator />
            </template>

            <payment-method-form :user="user" />

            <separator />

            <invoices-form :user="user" />

        </div>
    </div>
</template>

<script>

import { defineComponent, ref, onMounted } from "vue";
import { useForm } from '../../Functions/useForm'
import Separator from '../../Components/Separator.vue'
import PaymentMethodForm from './PaymentMethodForm.vue'
import SubscriptionForm from './SubscriptionForm.vue'
import InvoicesForm from './InvoicesForm.vue'

export default defineComponent({

    components: {
        Separator,
        PaymentMethodForm,
        SubscriptionForm,
        InvoicesForm,
    },

    props: {
        user: {
            required: true,
        },
    },
    
    setup() {
        const products = ref([]);

        const { 
            status: readStatus,
            error: readError,
            send: readSend,
        } = useForm();

        function readProducts()
        {
            readSend('get', '/products').then(r => {
                products.value = r.data;
            });
        }

        onMounted(() => readProducts());

        return {
            products,
            readStatus,
            readError,
            readSend,
            readProducts,
        }
    },
})
</script>
