<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white shadow border-b border-gray-100">

            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">

                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            Logo
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            
                        </div>
                        
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">

                        <!-- Settings Dropdown -->
                        <div class="ml-3 relative">
                            <dropdown align="right" width="48">

                                <template #trigger>
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                        <div class="text-right">
                                            <div class="text-sm text-gray-700">{{ user.firstname }} {{ user.lastname }}</div>
                                            <div class="text-xs">{{ user.current_team.name }}</div>
                                        </div>
                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>

                                    <!-- Account Management -->
                                    <!--div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Account
                                    </div-->

                                    <router-link
                                        :to="{name: 'dashboard'}"
                                        class="dropdown-link">
                                        Tableau de bord
                                    </router-link>

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
        <main>
            <router-view :user="user"></router-view>
        </main>
    </div>
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
                window.location.reload();
            });
        }

        return {
            showingNavigationDropdown,
            logout,
        };
    },
})
</script>
