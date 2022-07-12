<template>
    <div class="mt-2 flex flex-col sm:flex-row items-center justify-end gap-2"
        v-if="pagination !== null">
        
        <div class="w-full sm:w-auto text-sm text-center sm:text-left sm:text-md">
            Résultats {{ (pagination.meta.current_page - 1) * pagination.meta.per_page + 1 }}
            à {{ Math.min(pagination.meta.current_page * pagination.meta.per_page, pagination.meta.total) }} 
            sur {{ pagination.meta.total }}
        </div>

        <list-pagination 
            v-if="pagination !== null"
            :pagination="pagination.meta" 
            v-model:page.number="pageValue"
            @update:page="$emit('update:page', pageValue)">
        </list-pagination>

        <select
            class="input w-full sm:w-auto"
            v-model.number="limitValue"
            @update:modelValue="$emit('update:limit', limitValue)">
            <option :value="5">5 par page</option>
            <option :value="10">10 par page</option>
            <option :value="20">20 par page</option>
            <option :value="50">50 par page</option>
            <option :value="100">100 par page</option>
        </select>

    </div>
</template>

<script>
    import { defineComponent, ref } from 'vue'
    import ListPagination from './ListPagination'

    export default defineComponent({
        components: {
            ListPagination,
        },

        props: {
            page: {
                type: Number,
                required: true,
            },
            limit: {
                type: Number,
                required: true,
            },
            pagination: {
                required: true,
            },
        },

        emits: [
            'update:page',
            'update:limit',
        ],

        setup(props)
        {
            const limitValue = ref(props.limit);
            const pageValue = ref(props.page);

            return {
                limitValue,
                pageValue,
            }
        }
    })
</script>
