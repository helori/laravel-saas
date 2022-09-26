<template>

    <div class="absolute inset-x-0 bottom-0 overflow-y-scroll flex items-center justify-center"
        :class="user.is_on_trial ? 'offset-nav-top-trial' : 'offset-nav-top'">

        <div v-if="!user.is_root"
            class="p-10">
            Vous n'avez pas l'autorisation d'accès à ce module.
        </div>

        <div class="absolute h-full w-full p-10 flex flex-col"
            v-if="user.is_root">

            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <!-- Header -->
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <list-header
                class="mb-2"
                title="Clients"
                v-model:search="readCommonParams.search">
            </list-header>

            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <!-- Body -->
            <!-- - - - - - - - - - - - - - - - - - -     - - - - - - - - - - - - -->
            <div class="table-wrapper overflow-y-scroll text-white"
                v-if="pagination !== null">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <div class="flex gap-1">
                                    <table-sort-label
                                        label="Entreprise"
                                        order-key="teams.name"
                                        v-model:params="readCommonParams">
                                    </table-sort-label>
                                </div>
                            </th>
                            <th>
                                <table-sort-label
                                    label="Création"
                                    order-key="users.created_at"
                                    v-model:params="readCommonParams">
                                </table-sort-label>
                            </th>
                            <th>
                                <table-sort-label
                                    label="Vérification"
                                    order-key="users.email_verified_at"
                                    v-model:params="readCommonParams">
                                </table-sort-label>
                            </th>
                            <th>
                                <table-sort-label
                                    label="Mode de paiement"
                                    order-key="teams.pm_type"
                                    v-model:params="readCommonParams">
                                </table-sort-label>
                            </th>
                            <th>
                                Souscriptions
                            </th>
                            <th>
                                Membres
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, idx) in pagination.data" >
                            <td>
                                <div>{{ item.firstname }} {{ item.lastname }}</div>
                                <div class="text-gray-400">{{ item.email }}</div>
                                <div class="text-gray-400">{{ item.name }}</div>
                            </td>
                            <td>
                                {{ $filters.date(item.owner_created_at, 'DD/MM/YYYY') }}
                            </td>
                            <td>
                                {{ $filters.date(item.owner_email_verified_at, 'DD/MM/YYYY') }}
                            </td>
                            <td>
                                {{ item.pm_type }}
                            </td>
                            <td>
                                <div v-if="item.subscriptions.length > 0">
                                    <div>{{ item.subscriptions.length }} souscriptions</div>
                                    <div :class="{
                                        'text-green-400 dark:text-green-600': activeSubscriptions(item.subscriptions).length > 0,
                                        'text-red-400 dark:text-red-600': activeSubscriptions(item.subscriptions).length == 0,
                                    }">
                                        {{ activeSubscriptions(item.subscriptions).length }} actives
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ item.users_count }}
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a class="btn btn-gray"
                                        :href="'/api/admin/login/' + item.owner_id">
                                        Connexion
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <!-- Footer -->
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <list-footer
                :pagination="pagination"
                v-model:limit="readCommonParams.limit"
                v-model:page="readCommonParams.page">
            </list-footer>

        </div>
    </div>
</template>

<script>

import { defineComponent, ref, onMounted } from "vue"

import { useForm } from '../../Functions/useForm'
import { useList } from '../../Functions/useList'

import ListHeader from '../../Components/ListHeader'
import ListFooter from '../../Components/ListFooter'
import TableSortLabel from '../../Components/TableSortLabel'

export default defineComponent({

    components: {
        ListHeader,
        ListFooter,
        TableSortLabel,
    },

    props: {
        user: {
            required: true,
        },
    },

    setup()
    {
        // ----------------------------------------------------
        //  Read
        // ----------------------------------------------------
        function read()
        {
            readParams.value = {
                page: readCommonParams.page,
                search: readCommonParams.search,
                order_by: readCommonParams.orderBy,
                order_dir: readCommonParams.orderDir,
                limit: readCommonParams.limit,
            };

            readSend('get', '/api/admin/team').then(r => {
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

        storageKey.value = 'admin-teams1';
        locked.value = true;
        readCommonParams.limit = 10;
        locked.value = false;

        onMounted(read);

        function activeSubscriptions(subscriptions){
            return subscriptions.filter(subscription => {
                return (subscription.status == 'active');
            })
        }

        // ----------------------------------------------------
        //  Return
        // ----------------------------------------------------
        return {
            pagination,
            readCommonParams,
            
            readStatus,
            readError,
            readSend,
            readParams,
            read,

            activeSubscriptions,
        };
    }
})

</script>
