<template>

    <div class="my-10 md:my-0 px-4 md:px-0">

        <list-header
            class="mb-2"
            title="Membres de l'équipe"
            v-model:search="readCommonParams.search">
            <button 
                class="btn btn-primary" 
                @click="openCreate()">
                Créer un membre...
            </button>
        </list-header>

        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
        <!-- Body -->
        <!-- - - - - - - - - - - - - - - - - - -     - - - - - - - - - - - - -->
        <div class="table-wrapper overflow-y-scroll dark:text-white"
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
                                    label="Email"
                                    order-key="email"
                                    v-model:params="readCommonParams"
                                    class="mx-2">
                                </table-sort-label>
                            </div>
                        </th>
                        <th>
                            <table-sort-label
                                label="Rôle"
                                order-key="role"
                                v-model:params="readCommonParams"
                                class="mx-2">
                            </table-sort-label>
                        </th>
                        <th>
                            <table-sort-label
                                label="Activé"
                                order-key="activated"
                                v-model:params="readCommonParams">
                            </table-sort-label>
                        </th>
                        <th>
                            <table-sort-label
                                label="Création"
                                order-key="created_at"
                                v-model:params="readCommonParams">
                            </table-sort-label>
                        </th>
                        <th>
                            <table-sort-label
                                label="Invitation"
                                order-key="invited_at"
                                v-model:params="readCommonParams">
                            </table-sort-label>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, idx) in pagination.data" 
                        :key="item.id">
                        <td>
                            <div>{{ item.lastname }} {{ item.firstname }}</div>
                            <div class="text-gray-500 dark:text-gray-400 text-sm">{{ item.email }}</div>
                        </td>
                        <td>
                            <div :class="{
                                'text-green-500': (item.role === 'owner'),
                                'text-blue-500': (item.role === 'member'),
                            }" class="text-sm uppercase">
                                <span v-if="item.role === 'owner'">Propriétaire</span>
                                <span v-if="item.role === 'member'">Membre</span>
                            </div>
                        </td>
                        <td>
                            <span v-if="item.activated" class="text-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <span v-if="!item.activated" class="text-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        </td>
                        <td>
                            <div>{{ $filters.date(item.created_at, 'DD/MM/YYYY à HH:mm') }}</div>
                        </td>
                        <td>
                            <div v-if="item.invited_at">
                                {{ $filters.date(item.invited_at, 'DD/MM/YYYY à HH:mm') }}
                            </div>
                        </td>
                        <td class="flex items-center justify-end gap-2">
                            <button class="btn btn-gray" 
                                :title="'Se connecter en tant que ' + item.firstname + ' ' + item.lastname"
                                @click="openUpdate(item)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button class="btn btn-gray" 
                                :title="'Envoyer une invitation à ' + item.firstname + ' ' + item.lastname"
                                v-if="item.id !== user.id"
                                @click="openInvite(item)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </button>
                            <button class="btn btn-gray" 
                                v-if="item.id !== user.id"
                                @click="openDestroy(item)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            <a class="btn btn-gray" 
                                :title="'Se connecter en tant que ' + item.firstname + ' ' + item.lastname"
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

        <list-footer
            :pagination="pagination"
            v-model:limit="readCommonParams.limit"
            v-model:page="readCommonParams.page">
        </list-footer>

   </div>


    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <!-- Create -->
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <dialog-form 
        ref="createDialog"
        title="Ajouter un utilisateur"
        button="Enregistrer"
        max-width-class="max-w-screen-sm"
        :callback="create">
        <template #content>
            <form-member-create
                v-model:member="createDialog.data">
            </form-member-create>
        </template>
    </dialog-form>

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <!-- Update -->
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <dialog-form 
        ref="updateDialog"
        title="Modifier l'utilisateur"
        button="Enregistrer"
        max-width-class="max-w-screen-sm"
        :callback="update">
        <template #content>
            <form-member-update
                v-model:member="updateDialog.data">
            </form-member-update>
        </template>
    </dialog-form>

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <!-- Invite -->
    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
    <dialog-form 
        ref="inviteDialog"
        title="Envoyer une invitation"
        button="Envoyer"
        cancel-text="Ne pas envoyer d'invitation"
        max-width-class="max-w-screen-sm"
        :callback="invite">
        <template #content>
            <form-member-invite
                v-model:member="inviteDialog.data">
            </form-member-invite>
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
        max-width-class="max-w-screen-md"
        :callback="destroy">
        <template #content>
            <!--div>
                Votre abonnement dépend du nombre de membres de votre équipe.
                La suppression d'un membre sera prise en compte à votre prochaine échéance de facturation.
            </div>
            <div class="h-px my-4 bg-gray-200"></div-->
            <div class="font-semibold text-red-600">
                Vous êtes sur le point de supprimer définitivement l'utilisateur : 
                <strong>{{ destroyDialog.data.firstname }} {{ destroyDialog.data.lastname }}</strong>.
                Toutes les données associées à son compte seront définitivement supprimées.
                Voulez-vous vraiment continuer ?
            </div>
        </template>
    </dialog-form>
</template>

<script>
    
import { defineComponent, ref, onMounted, inject } from 'vue'
import { PencilAltIcon, TrashIcon } from '@heroicons/vue/solid'

import { useForm } from '../../Functions/useForm'
import { useList } from '../../Functions/useList'

import ListHeader from '../../Components/ListHeader'
import ListFooter from '../../Components/ListFooter'
import TableSortLabel from '../../Components/TableSortLabel'

import FormSection from '../../Components/FormSection'

import FormMemberCreate from './FormMemberCreate'
import FormMemberUpdate from './FormMemberUpdate'
import FormMemberInvite from './FormMemberInvite'

export default defineComponent({

    components: {
        ListHeader,
        ListFooter,
        TableSortLabel,
        FormSection,
        PencilAltIcon,
        TrashIcon,
        FormMemberCreate,
        FormMemberUpdate,
        FormMemberInvite,
    },

    props: {
        user: {
            required: true,
        }
    },

    emits: [
        'dialog-message'
    ],

    setup(props, context)
    {
        // ---------------------
        // https://learnvue.co/tutorials/vue-event-handling-guide
        // context contains 3 properties : "attrs", "slots" and "emit"
        // ---------------------

        let openDialogMessage = inject('openDialogMessage');

        function read()
        {
            readParams.value = {
                page: readCommonParams.page,
                search: readCommonParams.search,
                order_by: readCommonParams.orderBy,
                order_dir: readCommonParams.orderDir,
                limit: readCommonParams.limit,
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
        locked.value = false;
        
        onMounted(read);

        // ----------------------------------------------------
        //  Create
        // ----------------------------------------------------
        const createDialog = ref(null);

        function openCreate(){

            createDialog.value.data = {
                firstname: null,
                lastname: null,
                email: null,
                phone: null,
                role: 'member',
                has_password: false,
                password: null,
                password_confirmation: null,
                activated: true,
            };
            createDialog.value.open();
        }

        function create(){

            /*if(!createDialog.value.data.password){
                var dummyPasswordNobodyKnows = Math.random().toString(36).substr(2, 8);
                createDialog.value.data.password = dummyPasswordNobodyKnows;
                createDialog.value.data.password_confirmation = dummyPasswordNobodyKnows;
            }*/

            return createDialog.value.send('post', '/team/' + props.user.current_team.id + '/member').then(r => {
                createDialog.value.close();
                openInvite(r.data); // important to get the user id
                read();
            })
        }

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
                activated: item.activated,
            };
            updateDialog.value.open();
        }

        function update(){
            return updateDialog.value.send('put', '/team/' + props.user.current_team.id + '/member/' + updateDialog.value.data.id).then(r => {
                updateDialog.value.close();
                openDialogMessage({
                    type: 'success',
                    title: "Membre modifié",
                    message: "Le membre a été modifié avec succès",
                });
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

        // ----------------------------------------------------
        //  Invite
        // ----------------------------------------------------
        const inviteDialog = ref(null);

        function openInvite(item){
            inviteDialog.value.data = {
                id: item.id,
                firstname: item.firstname,
                lastname: item.lastname,
                email: item.email,
            };
            inviteDialog.value.open();
        }

        function invite(){
            return inviteDialog.value.send('post', '/team/' + props.user.current_team.id + '/member/' + inviteDialog.value.data.id + '/invite').then(r => {
                inviteDialog.value.close();
                openDialogMessage({
                    type: 'success',
                    title: "Invitation envoyée",
                    message: inviteDialog.value.data.firstname + " " + inviteDialog.value.data.lastname + 
                        " a bien reçu une invitation par email à l'adresse : " + inviteDialog.value.data.email,
                });
                read();
            })
        }

        return {
            pagination,
            readCommonParams,
            filters,

            readStatus,
            readError,
            readSend,
            readParams,
            read,

            createDialog,
            openCreate,
            create,

            updateDialog,
            openUpdate,
            update,

            destroyDialog,
            openDestroy,
            destroy,

            inviteDialog,
            openInvite,
            invite,
        }
    }
})
</script>
