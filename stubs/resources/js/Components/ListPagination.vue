<template>
    <nav aria-label="Page navigation" 
        class="my-0"
        v-show="pagination.total > pagination.per_page" 
        v-if="pagination !== null">
        <ul class="list-none m-0 flex flex-row">

            <li class="block" 
                v-if="pagination.current_page == 1"
                aria-label="Previous">
                <a class="block bg-white border border-gray-300 rounded-l-md px-3 py-2 text-gray-500">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <li class="block" v-else>
                <a class="block bg-white border border-gray-300 rounded-l-md px-3 py-2"
                    @click="$event.preventDefault(); setPage(pagination.current_page - 1)" 
                    aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <li class="block"
                v-if="pagination.current_page >= 3">
                <a class="block bg-white border border-gray-300 px-3 py-2"
                    href="#" 
                    @click="$event.preventDefault(); setPage(1)">1</a>
            </li>

            <li class="block" 
                v-if="pagination.current_page >= 4">
                <a class="block bg-white border border-gray-300 px-3 py-2 text-gray-500" 
                    href="#" 
                    @click="$event.preventDefault();">...</a>
            </li>

            <li class="block" 
                v-for="p in pagination.last_page" 
                v-show="(p >= (pagination.current_page - 1)) && (p <= (pagination.current_page + 1))">

                <a class="block bg-white border border-gray-300 px-3 py-2"
                    href="#" 
                    @click="$event.preventDefault(); setPage(p)" 
                    v-if="p !== pagination.current_page">{{ p }}</a>
                <a class="block bg-blue-700 text-white border border-blue-700 px-3 py-2" 
                    href="#" 
                    @click="$event.preventDefault()" 
                    v-else>{{ p }}</a>
            </li>

            <li class="block" 
                v-if="pagination.current_page <= pagination.last_page - 3">
                <a class="block bg-white border border-gray-300 px-3 py-2 text-gray-500" 
                href="#">...</a>
            </li>

            <li class="block"
                v-if="pagination.current_page <= pagination.last_page - 2">
                <a class="block bg-white border border-gray-300 px-3 py-2" 
                    href="#" 
                    @click="$event.preventDefault(); setPage(pagination.last_page)" >
                    {{ pagination.last_page }}
                </a>
            </li>

            <li class="block" 
                v-if="pagination.current_page == pagination.last_page">
                <a class="block bg-white border border-gray-300 rounded-r-md px-3 py-2 text-gray-500" 
                    href="#">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>

            <li class="block" v-else>
                <a class="block bg-white border border-gray-300 rounded-r-md px-3 py-2"
                    href="#" 
                    @click="$event.preventDefault(); setPage(pagination.current_page + 1)" 
                    aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>

import { defineComponent, ref } from "vue"

export default defineComponent({

    emits: ['update:page'],

    props: {
        page: {
            type: Number,
            required: true,
        },
        pagination: {
            required: true,
        },
    },

    setup(props)
    {
        const item = ref(props.page);

        function setPage(page){
            item.value = page;
            this.$emit('update:page', page);
        }

        return {
            item,
            setPage,
        };
    },
})
</script>
