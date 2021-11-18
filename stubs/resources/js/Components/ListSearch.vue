<template>
    <div>
        <input
            type="search"
            class="input"
            placeholder="Rechercher..."
            v-model="item"
            @change="$emit('update:search', $event.target.value)"
            autocomplete="off">
    </div>
</template>

<script>
    import { defineComponent, ref, watch } from 'vue'

    export default defineComponent({

        props: {
            search: String,
        },

        emits: ['update:search'],

        setup(props){
            const item = ref(props.search);

            return {
                item,
            }
        }
    })
</script>
