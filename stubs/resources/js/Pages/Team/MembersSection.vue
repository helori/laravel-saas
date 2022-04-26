<template>
    <form-section>
        <template #title>
            Membres de l'équipe
        </template>

        <template #description>
            Gérer les membres de votre équipe
        </template>

        <template #form>
            <div class="col-span-6">

                <list-header
                    class="mb-2"
                    title="Membres"
                    v-model:search="readCommonParams.search">
                    <!-- Button create member -->
                </list-header>

                <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                <!-- Body -->
                <!-- - - - - - - - - - - - - - - - - - -     - - - - - - - - - - - - -->
                <div class="table-wrapper"
                    style="overflow-y: scroll"
                    v-if="pagination !== null">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <div class="flex">
                                        <table-sort-label
                                            label="Nom"
                                            order-key="lastname"
                                            v-model:params="readCommonParams"
                                            class="mx-2">
                                        </table-sort-label> /
                                        <table-sort-label
                                            label="Prénom"
                                            order-key="firstname"
                                            v-model:params="readCommonParams"
                                            class="mx-2">
                                        </table-sort-label> /
                                        <table-sort-label
                                            label="Rôle"
                                            order-key="role"
                                            v-model:params="readCommonParams"
                                            class="mx-2">
                                        </table-sort-label> /
                                        <table-sort-label
                                            label="Email"
                                            order-key="email"
                                            v-model:params="readCommonParams"
                                            class="mx-2">
                                        </table-sort-label>
                                    </div>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, idx) in pagination.data" 
                                :key="item.id">
                                <td>
                                    <div>{{ item.firstname }} {{ item.lastname }}</div>
                                    <div :class="{
                                        'text-green-500': (item.role === 'owner'),
                                        'text-blue-500': (item.role === 'member'),
                                    }" class="text-sm uppercase">
                                        <span v-if="item.role === 'owner'">Propriétaire</span>
                                        <span v-if="item.role === 'member'">Membre</span>
                                    </div>
                                    <div class="text-gray-500 dark:text-gray-400 text-sm">{{ item.email }}</div>
                                </td>
                                <td class="text-right">
                                    <span class="btn btn-gray ml-2" 
                                        @click="openUpdate(item)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </span>
                                    <span class="btn btn-gray ml-2" 
                                        @click="openDestroy(item)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </span>
                                    <a class="btn btn-gray ml-2" 
                                        :href="'/team/' + user.current_team.id + '/member/' + item.id + '/login'">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

           </div>
        </template>

        <template #actions>
            <list-footer
                :pagination="pagination"
                v-model:limit="readCommonParams.limit"
                v-model:page="readCommonParams.page">
            </list-footer>
        </template>

    </form-section>

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <!-- Update -->
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <dialog-form 
        ref="updateDialog"
        title="Modifier l'utilisateur"
        button="Enregistrer"
        max-width="sm"
        :callback="update">
        <template #content>
            <form-member
                :user="user"
                v-model:member="updateDialog.data"
                type="mandant">
            </form-member>
        </template>
    </dialog-form>

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <!-- Delete -->
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <dialog-form 
        ref="destroyDialog"
        type="danger"
        title="Supprimer l'utilisateur"
        button="Supprimer"
        :callback="destroy">
        <template #content>
            <alert type="danger">
                Vous êtes sur le point de supprimer définitivement l'utilisateur : 
                <strong>{{ destroyDialog.data.firstname }} {{ destroyDialog.data.lastname }}</strong>.
                Voulez-vous vraiment continuer ?
            </alert>
        </template>
    </dialog-form>
</template>

<script>
    
import { defineComponent, ref, onMounted } from 'vue'
import { PencilAltIcon, TrashIcon } from '@heroicons/vue/solid'

import { useForm } from '../../Functions/useForm'
import { useList } from '../../Functions/useList'

import ListHeader from '../../Components/ListHeader'
import ListFooter from '../../Components/ListFooter'
import TableSortLabel from '../../Components/TableSortLabel'

import FormSection from '../../Components/FormSection'
import Alert from '../../Components/Alert'
import Error from '../../Components/Error'
import DialogForm from '../../Components/DialogForm'

import FormMember from './FormMember'

export default defineComponent({

    components: {
        ListHeader,
        ListFooter,
        TableSortLabel,
        Alert,
        Error,
        DialogForm,
        FormSection,
        PencilAltIcon,
        TrashIcon,
        FormMember,
    },

    props: {
        user: {
            required: true,
        }
    },

    setup(props) {

        function read()
        {
            readParams.value = {
                page: readCommonParams.page,
                search: readCommonParams.search,
                order_by: readCommonParams.orderBy,
                order_dir: readCommonParams.orderDir,
                limit: readCommonParams.limit,

                state: filters.state,
                tours: filters.tours,
            };

            readSend('get', '/team/' + props.user.current_team.id + '/member').then(r => {
                pagination.value = r.data;
            })
        }

        const { 
            status: readStatus,
            error: readError,
            send: readSend,
            params: readParams,
        } = useForm();

        const { 
            pagination,
            readCommonParams,
            filters,
            storageKey,
            locked,
        } = useList(read);

        storageKey.value = null;
        locked.value = true;
        readCommonParams.limit = 10;
        filters.state = null;
        filters.tours = null;
        locked.value = false;
        
        onMounted(read);

        // ----------------------------------------------------
        //  Update
        // ----------------------------------------------------
        const updateDialog = ref(null);

        function openUpdate(item){
            updateDialog.value.data = {
                id: item.id,
                firstname: item.firstname,
                lastname: item.lastname,
                email: item.email,
                phone: item.phone,
                role: item.role,
            };
            updateDialog.value.open();
        }

        function update(){
            return updateDialog.value.send('put', '/team/' + props.user.current_team.id + '/member/' + updateDialog.value.data.id).then(r => {
                updateDialog.value.close();
                read();
            })
        }

        // ----------------------------------------------------
        //  Destroy
        // ----------------------------------------------------
        const destroyDialog = ref(null);

        function openDestroy(item){
            destroyDialog.value.data = {
                id: item.id,
                firstname: item.firstname,
                lastname: item.lastname,
            };
            destroyDialog.value.open();
        }

        function destroy(){
            return destroyDialog.value.send('delete', '/team/' + props.user.current_team.id + '/member/' + destroyDialog.value.data.id).then(r => {
                destroyDialog.value.close();
                read();
            })
        }

        return {
            pagination,
            readCommonParams,
            filters,
            storageKey,

            readStatus,
            readError,
            readSend,
            readParams,
            read,

            updateDialog,
            openUpdate,
            update,

            destroyDialog,
            openDestroy,
            destroy,
        }
    }
})
</script>
