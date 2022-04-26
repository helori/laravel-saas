<template>
    <div>
        <div class="flex items-center gap-2">
            <div class="flex-grow mr-5">
                <div class="text-lg font-bold">
                    {{ title }}
                </div>
            </div>
            <div>
                <input
                    type="search"
                    class="input"
                    placeholder="Rechercher..."
                    v-model="item"
                    @change="$emit('update:search', item)"
                    autocomplete="off">
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
