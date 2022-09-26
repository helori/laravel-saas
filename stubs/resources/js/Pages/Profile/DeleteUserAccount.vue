<template>
    <form-section>
        <template #title>
            Suppression du compte
        </template>

        <template #description>
            Supprimez complètement votre compte utilisateur.
        </template>

        <template #content>

            <div class="text-sm">
                Si vous êtes propriétaire d'une équipe, vous devez résilier vos abonnements en cours et supprimer les comptes des membres de votre équipe avant de pouvoir supprimer votre compte.
                Après avoir supprimé votre compte, vos données seront effacées et vous ne pourrez plus les retrouver, même si vous reprenez un abonnement à l'avenir.
            </div>

            <request-state :error="deleteError" :status="deleteStatus" />

        </template>

        <template #actions>

            <button 
                v-if="deleteDialog !== null"
                type="button"
                class="btn btn-red"
                :disabled="deleteDialog.status === 'pending'"
                @click="openDelete">
                Supprimer le compte
            </button>

        </template>

    </form-section>

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <!-- Delete -->
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <dialog-form 
        ref="deleteDialog"
        type="danger"
        title="Supprimer le compte"
        button="Supprimer le compte"
        max-width-class="max-w-screen-md"
        :callback="deleteUser">
        <template #content>
            <div class="font-semibold text-red-600">
                Si vous êtes propriétaire d'une équipe, vous devez résilier vos abonnements en cours et supprimer les comptes des membres de votre équipe avant de pouvoir supprimer votre compte.
                Après avoir supprimé votre compte, vos données seront effacées et vous ne pourrez plus les retrouver, même si vous reprenez un abonnement à l'avenir.
                Voulez-vous vraiment continuer ?
            </div>
        </template>
    </dialog-form>

</template>

<script>
    import { defineComponent, ref, onMounted } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import FormSection from '../../Components/FormSection'
    
    export default defineComponent({

        components: {
            FormSection,
        },

        props: {
            user: {
                required: true,
            },
        },

        setup(props) {

            const deleteDialog = ref(null);

            function deleteUser()
            {
                return deleteDialog.value.send('delete', '/user').then(r => {
                    window.location.href = "/";
                })
            }

            function openDelete()
            {
                deleteDialog.value.open();
            }

            return {
                deleteDialog,
                openDelete,
                deleteUser,
            }
        },
    })
</script>
