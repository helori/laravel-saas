<template>
    <form-section>
        <template #title>
            Clés API
        </template>

        <template #description>
            Connectez d'autres application grâce à votre clé API
        </template>

        <template #form>

            <div class="col-span-6 sm:col-span-4">

                <div v-if="apiKeyInfo">
                    <h3 class="text-lg font-medium text-green-500 mb-2">
                        Votre clé API est active.
                    </h3>
                    <div class="text-gray-500 dark:text-gray-400 text-sm">
                        <div>Créée le : {{ apiKeyInfo.created_at }}</div>
                        <div>Dernière utilisation : {{ apiKeyInfo.last_used_at }}</div>
                    </div>
                </div>

                <div v-else>
                    <h3 class="text-lg font-medium text-red-500">
                        Vous n'avez pas de clé API active.
                    </h3>
                </div>

                <div v-if="apiKey"
                    class="font-bold mt-3">
                    Votre clé ne sera affichée qu'une seule fois, notez-la bien :
                </div>

                <div v-if="apiKey"
                    class="mt-3 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-800 rounded-lg">
                    {{ apiKey }}
                </div>

           </div>
        </template>

        <template #actions>

            <error :errors="createError" class="inline-block" />
            <error :errors="deleteError" class="inline-block" />

            <button 
                v-if="!apiKeyInfo"
                type="button"
                class="btn btn-primary"
                :disabled="createStatus === 'pending'"
                @click="createApiToken">
                Générer une clé API
            </button>

            <button 
                v-if="apiKeyInfo"
                type="button"
                class="btn btn-red"
                :disabled="deleteStatus === 'pending'"
                @click="deleteApiToken">
                Supprimer la clé
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

            const apiKeyInfo = ref(null);

            const { 
                status: readStatus,
                error: readError,
                send: readSend,
            } = useForm();

            function readApiTokens()
            {
                readSend('get', '/api-token').then(r => {
                    apiKeyInfo.value = r.data;
                });
            }

            onMounted(readApiTokens)

            const apiKey = ref(null);

            const { 
                status: createStatus,
                error: createError,
                send: createSend,
            } = useForm();

            function createApiToken()
            {
                createSend('post', '/api-token').then(r => {
                    apiKey.value = r.data;
                    readApiTokens()
                });
            }

            const { 
                status: deleteStatus,
                error: deleteError,
                send: deleteSend,
            } = useForm();

            function deleteApiToken()
            {
                deleteSend('delete', '/api-token').then(r => {
                    apiKey.value = null;
                    readApiTokens()
                });
            }

            return {
                apiKeyInfo,
                readStatus,
                readError,
                readSend,
                readApiTokens,

                apiKey,
                createStatus,
                createError,
                createSend,
                createApiToken,

                deleteStatus,
                deleteError,
                deleteSend,
                deleteApiToken,
            }
        }
    })
</script>
