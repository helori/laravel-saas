<template>    
    <div class="flex flex-row justify-start"
        @click="toggle">
        <div v-show="item.orderBy === orderKey">
            <div v-show="item.orderDir === 'asc'">
                <ChevronDownIcon class="h-5 w-5"/>
            </div>
            <div v-show="item.orderDir === 'desc'">
                <ChevronUpIcon class="h-5 w-5"/>
            </div>
        </div>
        <div :class="item.orderBy === orderKey ? 'font-bold' : ''">
            {{ label }}
        </div>
    </div>
</template>

<script>
    import { defineComponent, ref } from 'vue'
    import { ChevronUpIcon } from '@heroicons/vue/solid'
    import { ChevronDownIcon } from '@heroicons/vue/solid'

    export default defineComponent({

        components: {
            ChevronUpIcon,
            ChevronDownIcon,
        },

        props: {
            params: Object,
            label: String,
            orderKey: String,
        },

        emits: ['update:params'],

        setup(props){
            const item = ref(props.params);

            function toggle(){
                if(props.orderKey){
                    if(item.value.orderBy !== props.orderKey){
                        item.value.orderBy = props.orderKey;
                    }else{
                        item.value.orderDir = (item.value.orderDir === 'asc') ? 'desc' : 'asc';
                    }
                    this.$emit('update:params', item.value);
                }
            }

            return {
                item,
                toggle,
            }
        }
    })
</script>
