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

        </div>
    </div>
</template>

<script>

import { defineComponent, ref, onMounted } from "vue";
import { useForm } from '../Functions/useForm'
import PaymentMethodForm from './Billing/PaymentMethodForm.vue'
import SubscriptionForm from './Billing/SubscriptionForm.vue'
import FormSection from '../Components/FormSection.vue'
import Separator from '../Components/Separator.vue'

export default defineComponent({

    components: {
        PaymentMethodForm,
        SubscriptionForm,
        FormSection,
        Separator,
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
