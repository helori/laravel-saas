<template>

    <div class="min-h-screen -mt-16 pt-16 px-4 flex flex-col justify-center items-center antialiased">
            
        <div class="text-center text-2xl font-bold mb-1 dark:text-white">
            Bonjour {{ user.firstname }} !
        </div>

        <div v-if="!subscribed"
            class="text-center">
            <div class="mt-4">Vous n'avez pas de souscription en cours.</div>
            <router-link
                :to="{name: 'billing'}"
                class="btn btn-primary mx-auto mt-10 text-xl py-4 px-10 font-semibold">
                Choisir une souscription...
            </router-link>
        </div>

        <div v-if="subscribed">

            <div class="text-center text-lg mb-10 text-gray-500 dark:text-gray-300">
                Sélectionnez un module pour démarrer
            </div>

            <div class="flex flex-row gap-8 justify-center pb-24">

                <!--router-link
                    :to="{name: 'app'}"
                    class="w-72 px-4 bg-gray-400 hover:bg-gray-300 dark:bg-gray-900 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 sm:p-6 shadow sm:rounded-md transition">
                    App module
                </router-link-->

            </div>
        </div>

    </div>

</template>

<script>
import { defineComponent, computed } from "vue";
export default defineComponent({
    components: {
        
    },
    props: {
        user: {
            required: true,
        },
    },
    
    setup(props) {
        const subscribed = computed(function(){
            return true; //props.user.is_allowed ...;
        });
        return {
            subscribed,
        };
    },
})
</script>
