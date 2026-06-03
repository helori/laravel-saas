<template>
    <form-section>
        <template #title>
            Clés API
        </template>

        <template #description>
            Connectez d'autres application grâce à votre clé API
        </template>

        <template #form>

            <div class="col-span-6 table-wrapper text-center overflow-hidden w-full">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Nom de la clé</th>
                            <th>Date de création</th>
                            <th>Date d'expiration</th>
                            <th>Dernière utilisation</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="token in tokens" :key="token.id">
                            <td>{{ token.name }}</td>
                            <td>{{ $filters.date(token.created_at, 'dd/MM/yyyy à HH:mm') }}</td>
                            <td>{{ token.expires_at ? $filters.date(token.expires_at, 'dd/MM/yyyy à HH:mm') : 'Jamais' }}</td>
                            <td>{{ token.last_used_at ? $filters.date(token.last_used_at, 'dd/MM/yyyy à HH:mm') : 'Jamais' }}</td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-red btn-sm"
                                    :disabled="deleteStatus === 'pending'"
                                    @click="deleteOpen(token)">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <template #actions>

            <button
                type="button"
                class="btn btn-primary"
                @click="createOpen">
                Créer une clé API
            </button>

        </template>

    </form-section>

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <!-- Create -->
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <dialog-form
        ref="createDialog"
        title="Ajouter une clé API"
        button="Enregistrer"
        max-width-class="max-w-screen-sm"
        :callback="createToken">
        <template #content>
            <label for="name" class="block text-sm font-medium text-gray-700">Nom de la clé</label>
            <input
                id="name"
                type="text"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50"
                v-model="createDialog.data.name">
        </template>
    </dialog-form>

    <dialog-modal
        ref="createdDialog"
        max-width-class="max-w-screen-sm">
        <template #title>
            Votre nouvelle clé API
        </template>
        <template #content>

            <div v-if="newToken"
                class="font-bold mt-3">
                Votre clé ne sera affichée qu'une seule fois, notez-la bien :
            </div>

            <div v-if="newToken"
                class="mt-3 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-800 rounded-lg">
                {{ newToken }}
            </div>
        </template>
        <template #footer>
            <button
                type="button"
                class="btn btn-gray"
                @click="closeCreated">
                Fermer
            </button>
        </template>
    </dialog-modal>

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <!-- Delete -->
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <dialog-form
        ref="destroyDialog"
        type="danger"
        title="Supprimer la clé API"
        button="Supprimer"
        max-width-class="max-w-screen-sm"
        :callback="deleteToken">
        <template #content>
            <div class="font-medium text-red-600">
                Voulez-vous vraiment supprimer cette clé API ?
            </div>
        </template>
    </dialog-form>

</template>

<script>
    import { defineComponent, ref, onMounted } from 'vue'
    import { functions } from 'helorui'
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

            const tokens = ref([]);

            const {
                status: readStatus,
                error: readError,
                send: readSend,
            } = functions.useRequest();

            function readTokens()
            {
                readSend('get', '/api-token').then(r => {
                    tokens.value = r.data;
                });
            }

            onMounted(readTokens)

            const newToken = ref(null);
            const createDialog = ref(null);
            const createdDialog = ref(null);

            function createOpen()
            {
                createDialog.value.data = {
                    name: '',
                };
                createDialog.value.open();
            }

            function closeCreated()
            {
                newToken.value = null;
                createdDialog.value.close();
            }

            function createToken()
            {
                createDialog.value.send('post', '/api-token').then(r => {
                    newToken.value = r.data;
                    createDialog.value.data.name = '';
                    createDialog.value.close();
                    createdDialog.value.open();
                    readTokens();
                    openNewToken();
                });
            }

            const destroyDialog = ref(null);

            function deleteOpen(token)
            {
                destroyDialog.value.data = token;
                destroyDialog.value.open();
                console.log(destroyDialog.value.data);
            }

            function deleteToken()
            {
                destroyDialog.value.send('delete', '/api-token/' + destroyDialog.value.data.id).then(r => {
                    destroyDialog.value.close();
                    readTokens();
                });
            }

            return {
                tokens,

                readStatus,
                readError,
                readSend,
                readTokens,

                newToken,
                createDialog,
                createdDialog,
                createOpen,
                createToken,
                closeCreated,

                destroyDialog,
                deleteOpen,
                deleteToken,
            }
        }
    })
</script>
