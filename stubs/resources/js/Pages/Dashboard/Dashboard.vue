<template>
    <div class="max-w-7xl mx-auto sm:py-10 sm:px-6 lg:px-8">

        <div class="text-center my-5 font-size-lg font-bold">
            Bonjour {{ user.firstname }}
        </div>

        <div class="flex flex-row justify-center">

            <router-link
                v-if="user.has_procuration"
                :to="{name: 'procurations'}"
                class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md">
                Procurations
            </router-link>

        </div>
    </div>
</template>

<script>

import { defineComponent, ref } from "vue";

export default defineComponent({

    components: {
        
    },

    props: {
        user: {
            required: true,
        },
    },
    
    setup() {
        return {
            
        };
    },
})
</script>
