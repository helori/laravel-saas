<template>
    <form-section @submitted="update">
        <template #title>
            Mot de passe
        </template>

        <template #description>
            Assurez-vous d'utiliser un mot de passe long et robuste.
        </template>

        <template #form>

            <div class="col-span-6 sm:col-span-4">

                <label for="current_password" 
                    class="label">
                    Mot de passe actuel
                </label>

                <input 
                    id="current_password"
                    type="password"
                    name="current_password"
                    class="input mb-2 mt-1 block w-full"
                    v-model="updateData.current_password">

                <label for="password" 
                    class="label">
                    Nouveau mot de passe
                </label>

                <input 
                    id="password"
                    type="password"
                    name="password"
                    class="input mb-2 mt-1 block w-full"
                    v-model="updateData.password">

                <label for="password_confirmation" 
                    class="label">
                    Confirmation du nouveau mot de passe
                </label>

                <input 
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="input mb-2 mt-1 block w-full"
                    v-model="updateData.password_confirmation">

           </div>
        </template>

        <template #actions>

            <div class="alert alert-green mr-3" 
                v-if="updateStatus === 'success'">
                Enregistré !
            </div>

            <request-error :error="updateError" class="mr-3" />

            <button
                type="submit"
                class="btn btn-primary"
                :class="{ 'opacity-25': updateStatus === 'pending' }"
                :disabled="updateStatus === 'pending'">
                Enregistrer
            </button>

        </template>
    </form-section>
</template>

<script>
    import { defineComponent } from 'vue'
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
            const { 
                data: updateData,
                status: updateStatus,
                error: updateError,
                send: updateSend,
            } = useForm();

            updateData.value = {
                current_password: '',
                password: '',
                password_confirmation: '',
            }

            function update(){
                updateSend('put', '/user/password')
            }

            return {
                updateData,
                updateStatus,
                updateError,
                update,
            }
        }
    })
</script>
