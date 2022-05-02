<template>

    <div class="absolute inset-x-0 bottom-0 overflow-y-scroll flex items-center justify-center"
        style="top: 88px">

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
                title="Administrateur"
                v-model:search="readCommonParams.search">
            </list-header>

            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <!-- Body -->
            <!-- - - - - - - - - - - - - - - - - - -     - - - - - - - - - - - - -->
            <div class="table-wrapper overflow-y-scroll"
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
                                    </table-sort-label> /
                                    <table-sort-label
                                        label="Propriétaire"
                                        order-key="lastname"
                                        v-model:params="readCommonParams">
                                    </table-sort-label>
                                </div>
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
                                    label="Abonnement"
                                    order-key="subscription_plan"
                                    v-model:params="readCommonParams">
                                </table-sort-label>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, idx) in pagination.data" >
                            <td>
                                <div>{{ item.name }}</div>
                                <div class="text-gray-500">{{ item.firstname }} {{ item.lastname }}</div>
                            </td>
                            <td>
                                {{ $filters.date(item.created_at, 'DD/MM/YYYY HH:mm') }}
                            </td>
                            <td>
                                {{ item.subscription_plan }}
                            </td>
                            <td class="flex justify-end gap-2">
                                <a class="btn btn-gray"
                                    :href="'/api/admin/login/' + item.user_id">
                                    Connexion
                                </a>
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

        storageKey.value = 'teams';
        locked.value = true;
        readCommonParams.limit = 10;
        locked.value = false;

        onMounted(read);

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
        };
    }
})

</script>
