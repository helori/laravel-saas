<template>
    <div class="mt-2 flex items-center justify-end"
        v-if="pagination !== null">
        
        <div>
            Résultats {{ (pagination.meta.current_page - 1) * pagination.meta.per_page + 1 }}
            à {{ Math.min(pagination.meta.current_page * pagination.meta.per_page, pagination.meta.total) }} 
            sur {{ pagination.meta.total }}
        </div>

        <select
            class="input ml-2"
            v-model="limitValue"
            @update:modelValue="$emit('update:limit', limitValue)">
            <option :value="5">5 par page</option>
            <option :value="10">10 par page</option>
            <option :value="20">20 par page</option>
            <option :value="50">50 par page</option>
            <option :value="100">100 par page</option>
        </select>
        
        <list-pagination 
            v-if="pagination !== null"
            :pagination="pagination.meta" 
            v-model:page="pageValue"
            @update:page="$emit('update:page', pageValue)"
            class="ml-2">
        </list-pagination>
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
