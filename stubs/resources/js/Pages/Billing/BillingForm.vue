<template>
    <form-section>
        <template #title>
            Informations de facturation
        </template>

        <template #description>
            Modifiez les informations qui apparaissent sur vos factures.
        </template>

        <template #form>

            <div class="col-span-6 sm:col-span-4" v-if="team">

                <div v-if="team.pivot.role === 'owner'">
                    
                    <label for="billing_name">
                        Nom ou entreprise
                    </label>

                    <input 
                        id="billing_name"
                        type="text"
                        class="input mb-2 mt-1 block w-full"
                        v-model="updateData.billing_name">

                    <label for="billing_email">
                        Email
                    </label>

                    <input 
                        id="billing_email"
                        type="text"
                        class="input mb-2 mt-1 block w-full"
                        v-model="updateData.billing_email">

                    <label for="billing_name">
                        Adresse
                    </label>

                    <input 
                        id="address_line1"
                        type="text"
                        class="input mb-2 mt-1 block w-full"
                        placeholder="Ligne 1..."
                        v-model="updateData.billing_line1">

                    <input 
                        id="billing_line2"
                        type="text"
                        class="input mb-2 mt-1 block w-full"
                        placeholder="Ligne 2..."
                        v-model="updateData.billing_line2">

                    <div class="flex gap-2 items-center">
                        <input 
                            id="billing_postal_code"
                            type="text"
                            class="input mb-2 mt-1 block w-full"
                            placeholder="Code postal..."
                            v-model="updateData.billing_postal_code">

                        <input 
                            id="billing_city"
                            type="text"
                            class="input mb-2 mt-1 block w-full"
                            placeholder="Ville..."
                            v-model="updateData.billing_city">
                    </div>

                    <select 
                        id="billing_country"
                        class="input w-full"
                        v-model="updateData.billing_country">
                        <option :value="null"></option>
                        <option
                            v-for="(country, code) in countriesList"
                            :value="code">
                            {{ country.name }}
                        </option>
                    </select>

                </div>

           </div>
        </template>

        <template #actions>

            <request-error :error="readTeamError" class="inline-block" />
            <request-error :error="updateError" class="inline-block" />

            <div class="alert alert-green" 
                v-if="updateStatus === 'success'">
                Enregistré !
            </div>

            <button 
                v-if="team && team.pivot.role === 'owner'"
                type="button"
                class="btn btn-primary"
                :class="{ 'opacity-25': (updateStatus === 'pending') }"
                :disabled="updateStatus === 'pending'"
                @click="updateTeam">
                Enregistrer
            </button>

        </template>
    </form-section>
</template>

<script>

import { defineComponent, ref, computed, onMounted } from 'vue'
import { useForm } from '../../Functions/useForm'
import FormSection from '../../Components/FormSection'
import { countries } from 'countries-list'

export default defineComponent({
    components: {
        FormSection,
    },

    props: {
        user: {
            required: true,
        },
    },

    setup(props)
    {
        const countriesList = ref(countries);

        // ---------------------------------------------------
        //  Current user's team
        // ---------------------------------------------------
        const team = ref(null);

        const { 
            status: readTeamStatus,
            error: readTeamError,
            send: readTeamSend,
        } = useForm();

        function readTeam()
        {
            readTeamSend('get', '/team').then(r => {
                team.value = r.data;
                updateData.value = r.data;
            });
        }

        onMounted(readTeam)

        // ---------------------------------------------------
        //  Update team
        // ---------------------------------------------------
        const { 
            data: updateData,
            status: updateStatus,
            error: updateError,
            send: updateSend,
        } = useForm();

        function updateTeam()
        {
            updateSend('put', '/team').then(r => {
                readTeam();
            });
        }

        return {
            countriesList,

            team,
            readTeamStatus,
            readTeamError,

            updateData,
            updateStatus,
            updateError,
            updateTeam,
        };
    }
})
</script>
