<template>
    <div>
        <div class="max-w-7xl mx-auto sm:py-10 sm:px-6 lg:px-8">

            <form-section :has-block="false">
                <template #title>
                    Modèle tarifaire
                </template>

                <template #description>
                    Choisissez votre modèle tarifaire
                </template>

                <template #form>
                    <div class="col-span-6 sm:col-span-4">

                        <select
                            class="input"
                            v-model="plan">
                            <option
                                v-for="p in plans"
                                :key="p.slug"
                                :value="p">
                                {{ p.name }}
                            </option>
                        </select>

                   </div>
                </template>
            </form-section>

            <separator />

            <template v-for="product in products"
                :key="product.product_id">
                <subscription-form 
                    :user="user"
                    :product="product"
                    :plan-slug="plan.slug" />
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
        const plan = ref(null);
        const plans = ref([]);
        const products = ref([]);

        const { 
            status: readStatus,
            error: readError,
            send: readSend,
        } = useForm();

        function readProducts()
        {
            readSend('get', '/products').then(r => {
                products.value = r.data.products;
                plans.value = r.data.plans;
                if(plans.value.length > 0){
                    plan.value = plans.value[0];
                }
            });
        }

        onMounted(() => readProducts());

        return {
            plan,
            plans,
            products,
            readStatus,
            readError,
            readSend,
            readProducts,
        }
    },
})
</script>
