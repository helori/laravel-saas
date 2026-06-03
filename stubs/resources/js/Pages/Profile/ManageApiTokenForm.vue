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
                                <div class="flex items-center justify-end gap-1">
                                    <!--button
                                        type="button"
                                        class="btn btn-white btn-sm"
                                        @click="viewOpen(token)">
                                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button-->

                                    <button
                                        type="button"
                                        class="btn btn-red btn-sm"
                                        :disabled="deleteStatus === 'pending'"
                                        @click="deleteOpen(token)">
                                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
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
        ref="viewDialog"
        max-width-class="max-w-screen-sm">
        <template #title>
            Votre clé API
        </template>
        <template #content>

            <div v-if="viewToken"
                class="font-bold mt-3">
                Votre clé ne sera affichée qu'une seule fois, notez-la bien :
            </div>

            <div v-if="viewToken"
                class="mt-3 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-800 rounded-lg">
                {{ viewToken }}
            </div>
        </template>
        <template #footer>
            <button
                type="button"
                class="btn btn-gray"
                @click="viewClose">
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

            // --------------------------------------------------------------
            // List
            // --------------------------------------------------------------
            const tokens = ref([]);

            const {
                status: listStatus,
                error: listError,
                send: listSend,
            } = functions.useRequest();

            function listTokens()
            {
                listSend('get', '/api-token').then(r => {
                    tokens.value = r.data;
                });
            }

            onMounted(listTokens)

            // --------------------------------------------------------------
            // Create
            // --------------------------------------------------------------
            const createDialog = ref(null);
            const createdDialog = ref(null);

            function createOpen()
            {
                createDialog.value.data = {
                    name: '',
                };
                createDialog.value.open();
            }

            function createToken()
            {
                createDialog.value.send('post', '/api-token').then(r => {
                    createDialog.value.data.name = '';
                    createDialog.value.close();
                    listTokens();
                    viewOpen(r.data);
                });
            }

            // --------------------------------------------------------------
            // View
            // --------------------------------------------------------------
            const viewDialog = ref(null);
            const viewToken = ref(null);

            /*const {
                status: readStatus,
                error: readError,
                send: readSend,
            } = functions.useRequest();

            function readToken(token)
            {
                readSend('get', '/api-token/' + token.id).then(r => {
                    viewToken.value = r.data;
                });
            }*/

            function viewOpen(token)
            {
                viewToken.value = token;
                //readToken(token);
                viewDialog.value.open();
            }

            function viewClose()
            {
                viewToken.value = null;
                viewDialog.value.close();
            }

            // --------------------------------------------------------------
            // Delete
            // --------------------------------------------------------------
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
                    listTokens();
                });
            }

            return {
                tokens,

                listStatus,
                listError,
                listSend,
                listTokens,

                createDialog,
                createOpen,
                createToken,

                destroyDialog,
                deleteOpen,
                deleteToken,

                viewToken,
                viewDialog,
                viewOpen,
                viewClose,
            }
        }
    })
</script>
