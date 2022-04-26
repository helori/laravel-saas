<template>

    <div class="p-1 rounded-md bg-gray-100">

        <div class="flex gap-1 items-center">
            <div class="flex-grow"
                v-for="option in options">
                {{ option.label }}
            </div>

            <button 
                type="button" 
                class="btn btn-primary" 
                @click="insert()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>

        <div class="flex gap-1 items-center mt-1"
            v-for="(data, idx) in item">
            <div v-for="option in options"
                class="flex-grow">
                <input
                    type="text"
                    class="input w-full"
                    v-if="option.options === null"
                    v-model="data[option.key]"
                    @input="update" />
                <select
                    v-if="option.options !== null"
                    v-model="data[option.key]"
                    class="input w-full"
                    @input="update">
                    <option v-for="opt in option.options"
                        :value="opt.value">
                        {{ opt.label }}
                    </option>
                </select>
            </div>
            <button 
                type="button" 
                class="btn btn-gray" 
                @click="removeAt(idx)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>

    </div>

</template>

<script>

import { defineComponent, ref, computed } from 'vue'

export default defineComponent(
{
    props: {
        modelValue: {
            required: true,
        },
        options: {
            type: Array,
            required: true
        },
    },

    emits: [
        'update:modelValue',
    ],

    setup(props, { emit })
    {
        const item = ref(props.modelValue);

        const gridClass = computed(function(){
            let cols = props.options.length + 1;
            if(cols === 1){
                return 'grid-cols-1';
            }else if(cols === 2){
                return 'grid-cols-2';
            }else if(cols === 3){
                return 'grid-cols-3';
            }else if(cols === 4){
                return 'grid-cols-4';
            }else if(cols === 5){
                return 'grid-cols-5';
            }else if(cols === 6){
                return 'grid-cols-6';
            }
        });

        function update()
        {
            emit('update:modelValue', item.value);
        }

        function removeAt(idx)
        {
            item.value.splice(idx, 1);
            emit('update:modelValue', item.value);
        }

        function insert()
        {
            var elt = {};
            for(var i=0; i<props.options.length; ++i){
                elt[props.options[i].key] = '';
            }
            item.value.push(elt);
            emit('update:modelValue', item.value);
        }

        return {
            item,
            gridClass,
            update,
            removeAt,
            insert,
        };
    }
})
</script>

