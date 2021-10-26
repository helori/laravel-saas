<template>
    <form-section @submitted="update">
        <template #title>
            Profil utilisateur
        </template>

        <template #description>
            Mettez à jour votre profil votre adresse email.
        </template>

        <template #form>

            <div class="col-span-6 sm:col-span-4">

                <label for="firstname" class="label">
                    Prénom
                </label>

                <input 
                    id="firstname"
                    type="text"
                    name="firstname"
                    class="input mb-2 mt-1 block w-full"
                    :class="{
                        'invalid': updateError
                    }"
                    placeholder="Votre prénom..."
                    autocomplete="firstname"
                    v-model="updateData.firstname">

                <label for="lastname" class="label">
                    Nom
                </label>

                <input 
                    id="lastname"
                    type="text"
                    name="lastname"
                    class="input mb-2 mt-1 block w-full"
                    placeholder="Votre nom..."
                    autocomplete="lastname"
                    v-model="updateData.lastname">

                <label for="email" class="label">
                    Email
                </label>

                <input 
                    id="email"
                    type="text"
                    name="email"
                    class="input mb-2 mt-1 block w-full"
                    placeholder="Votre email..."
                    autocomplete="email"
                    v-model="updateData.email">

           </div>
        </template>

        <template #actions>

            <div class="alert alert-green mr-3" 
                v-if="updateStatus === 'success'">
                Enregistré !
            </div>

            <error :errors="updateError" class="mr-3" />

            <button
                type="submit"
                class="btn btn-blue"
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
            const { 
                data: updateData,
                status: updateStatus,
                error: updateError,
                send: updateSend,
            } = useForm();

            updateData.value.firstname = props.user.firstname;
            updateData.value.lastname = props.user.lastname;
            updateData.value.email = props.user.email;

            function update(){
                updateSend('put', '/user/profile-information ')
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
