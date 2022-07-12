<template>
    <div>
        <div class="flex flex-col md:flex-row md:items-center gap-2">
            <div class="flex-grow"
                v-if="title">
                <div class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ title }}
                </div>
            </div>
            <input
                type="search"
                class="input flex-grow w-full md:w-auto"
                placeholder="Rechercher..."
                v-model="item"
                @change="$emit('update:search', item)"
                autocomplete="off">
            <div>
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
    import { defineComponent, ref, watch } from 'vue'
    import ListSearch from './ListSearch'

    export default defineComponent({
        components: {
            ListSearch,
        },

        props: {
            search: String,
            title: String,
        },

        emits: ['update:search'],

        setup(props){
            const item = ref(props.search);

            watch(() => props.search, (newValue) => {
                console.log('WATCH', newValue);
                item.value = newValue;
            })

            return {
                item,
            }
        }
    })
</script>
