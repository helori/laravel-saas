<template>
    <div>
        <div class="flex items-center">
            <div class="flex-grow mr-5">
                <div class="text-lg font-bold">
                    {{ title }}
                </div>
            </div>
            <div class="ml-2">
                <input
                    type="search"
                    class="input"
                    placeholder="Rechercher..."
                    v-model="item"
                    @change="$emit('update:search', item)"
                    autocomplete="off">
                <!--list-search
                    v-model:search="item"
                    @update:search="$emit('update:search', item)">
                </list-search-->
            </div>
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
