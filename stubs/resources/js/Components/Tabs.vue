<template>
   <div class="flex gap-2 w-full text-center">
        <div 
            v-for="(t, idx) in tabs"
            :key="t.value"
            class="py-2 flex-grow b-bottom font-bold rounded-t-md"
            @click="setTab(t)"
            :class="(item === t.value) ? activeClass : inactiveClass">
            {{ t.label }}
        </div>
    </div>
</template>

<script>

import { defineComponent, ref } from 'vue'

export default defineComponent({

    emits: ['update:tab'],
    
    props: {
        tab: {
            type: String,
            required: true,
        },
        tabs: {
            type: Array,
            required: true,
        },
        activeClass: {
            type: Array,
            required: false,
            default: ['bg-gray-100', 'border-gray-100'],
        },
        inactiveClass: {
            type: Array,
            required: false,
            default: ['bg-gray-300', 'border-gray-300', 'text-gray-600', 'pointer'],
        },
    },

    setup(props, { emit }){
        const item = ref(props.tab);

        function setTab(t){
            item.value = t.value;
            emit('update:tab', t.value);
        }

        return {
            item,
            setTab,
        };
    }
})
</script>
