<template>        
    <!-- Primary Navigation Menu -->
    <nav class="bg-white dark:bg-gray-900 border-b border-white dark:border-gray-900 antialiased relative z-10">
        <div class="mx-auto px-6 py-3">
            <div class="flex items-center justify-between h-16 gap-3">

                <slot name="logo">Logo</slot>
                
                <div class="hidden sm:flex sm:items-center sm:ml-6">

                    <!-- Settings Dropdown -->
                    <div class="relative">
                        <dropdown align="right" width="48"
                            :content-classes="['py-1', 'bg-white', 'dark:bg-gray-900']">
                            
                            <template #trigger>
                                <button type="button" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md focus:outline-none transition text-gray-500 bg-white hover:text-gray-700 dark:text-gray-400 dark:bg-gray-900 dark:hover:text-gray-200">
                                    <div class="text-right">
                                        <div class="text-sm text-gray-700 dark:text-gray-200">{{ user.firstname }} {{ user.lastname }}</div>
                                        <div class="text-xs">{{ user.current_team.name }}</div>
                                    </div>
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>

                                <a
                                    href="/app"
                                    class="dropdown-link">
                                    Menu principal
                                </a>

                                <div class="h-px bg-gray-200 dark:bg-gray-700"></div>

                                <router-link
                                    :to="{name: 'profile'}"
                                    class="dropdown-link">
                                    Mon compte
                                </router-link>

                                <router-link
                                    :to="{name: 'team'}"
                                    class="dropdown-link">
                                    Mon équipe
                                </router-link>

                                <router-link
                                    :to="{name: 'billing'}"
                                    class="dropdown-link">
                                    Facturation
                                </router-link>

                                <div class="h-px bg-gray-200 dark:bg-gray-700"></div>

                                <form @submit.prevent="logout">
                                    <button
                                        type="submit"
                                        class="dropdown-link text-red-500">
                                        Déconnexion
                                    </button>
                                </form>

                            </template>
                        </dropdown>
                    </div>
                </div>

                <!-- Hamburger -->
                <!--div class="-mr-2 flex items-center sm:hidden">
                    <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div-->

            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="antialiased">
        <router-view :user="user"></router-view>
    </main>

</template>

<script>

import { defineComponent, ref } from "vue";
import Dropdown from '../Components/Dropdown.vue'

export default defineComponent({

    components: {
        Dropdown,
    },

    props: {
        user: {
            required: true,
        },
    },
    
    setup() {
        let showingNavigationDropdown = ref(false)
        

        function logout(){
            axios.post('/logout').then(() => {
                //window.location.reload();
                window.location.href = '/';
            });
        }

        return {
            showingNavigationDropdown,
            logout,
        };
    },
})
</script>
