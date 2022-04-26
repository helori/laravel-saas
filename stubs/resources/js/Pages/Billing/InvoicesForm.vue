<template>
    <form-section>
        <template #title>
            Factures
        </template>

        <template #description>
            Téléchargez vos factures en PDF
        </template>

        <template #form>
            <div class="col-span-6">

                <div class="border border-gray-300 rounded-md overflow-hidden">
                    <table class="w-full text-left">
                        <tr class="bg-gray-100">
                            <th class="font-normal px-3 py-2">Période</th>
                            <th class="font-normal px-3 py-2">Montant</th>
                            <th class="font-normal px-3 py-2">Paiement</th>
                            <th class="font-normal px-3 py-2"></th>
                            <th class="font-normal px-3 py-2"></th>
                        </tr>
                        <tr v-for="invoice in invoices">
                            <td class="px-3 py-2 text-sm text-gray-600">
                                <div>Du : {{ $filters.date(invoice.period_start, 'DD/MM/YYYY', 'X') }}</div>
                                <div>Au : {{ $filters.date(invoice.period_end, 'DD/MM/YYYY', 'X') }}</div>
                            </td>
                            <td class="px-3 py-2">{{ $filters.number(invoice.amount_due / 100, 2) }} €</td>
                            <td class="px-3 py-2">
                                <span v-if="invoice.status === 'paid'">
                                    {{ $filters.date(invoice.status_transitions.paid_at, 'DD/MM/YYYY', 'X') }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <a :href="invoice.hosted_invoice_url" target="_blank" class="underline text-primary-500">Voir le détail</a>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a :href="invoice.invoice_pdf" target="_blank" class="btn btn-primary">Facture PDF</a>
                            </td>
                        </tr>
                    </table>
                </div>

           </div>
        </template>

    </form-section>
</template>

<script>

import { defineComponent, ref, computed, onMounted } from 'vue'
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
        },
    },

    setup(props) {

        const invoices = ref([]);
        const upcoming = ref([]);

        const { 
            status: listInvoiceStatus,
            error: listInvoiceError,
            send: listInvoiceSend,
            params: listInvoiceParams,
        } = useForm();

        function listInvoice()
        {
            listInvoiceSend('get', '/invoice').then(r => {
                invoices.value = r.data.invoices;
                upcoming.value = r.data.upcoming;
            });
        };

        onMounted(listInvoice);

        return {
            invoices,
            upcoming,
        };
    }
})
</script>
