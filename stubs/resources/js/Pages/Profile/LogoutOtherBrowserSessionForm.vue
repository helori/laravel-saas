<template>
    <form-section>
        <template #title>
            Sessions utilisateurs
        </template>

        <template #description>
            Gérez et déconnectez les sessions actives sur d'autres appareils.
        </template>

        <template #content>

            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-200">
                Si nécessaire, vous pouvez déconnecter toutes vos sessions actives sur d'autres appareils.
                Vos sessions récentes sont listées ci-dessous, mais cette liste peut ne pas être exhaustive.
                Si vous pensez que la sécurité de votre compte est compromise, vous devriez aussi changer de mot de passe.
            </div>

            <!-- Other Browser Sessions -->
            <div class="mt-5 space-y-6" v-if="sessions.length > 0">
                <div class="flex items-center" v-for="(session, i) in sessions" :key="i">
                    <div>
                        <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8 text-gray-500" v-if="session.agent.is_desktop">
                            <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-gray-500" v-else>
                            <path d="M0 0h24v24H0z" stroke="none"></path><rect x="7" y="4" width="10" height="16" rx="1"></rect><path d="M11 5h2M12 17v.01"></path>
                        </svg>
                    </div>

                    <div class="ml-3">
                        <div class="text-sm text-gray-600 dark:text-gray-200">
                            {{ session.agent.platform }} - {{ session.agent.browser }}
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ session.ip_address }},

                                <span class="text-green-500 font-semibold" v-if="session.is_current_device">Cet appareil</span>
                                <span v-else>Dernière activité {{ session.last_active }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </template>

        <template #actions>

            <div class="alert alert-green mr-3"
                v-if="logoutStatus === 'success'">
                C'est fait !
            </div>

            <button 
                type="button"
                class="btn btn-primary"
                @click="openLogout">
                Déconnecter les autres sessions
            </button>

            <error :errors="logoutError" />

            <dialog-modal 
                :show="confirmingLogout"
                @close="closeLogout"
                max-width-class="max-w-screen-sm">
                <template #title>
                    Déconnexion des autres sessions
                </template>

                <template #content>

                    <div class="mb-3 text-sm">
                        Saisissez votre mot de passe pour confirmer la déconnexion de tous vos autres appareils.
                    </div>

                    <input 
                        ref="password"
                        type="password"
                        name="password"
                        class="input block w-3/4"
                        v-model="logoutData.password"
                        @keyup.enter="logout">

                </template>

                <template #footer>

                    <error :errors="logoutError" class="inline-block mr-3" />

                    <button 
                        type="button"
                        class="btn btn-white mr-3"
                        @click="closeLogout">
                        Annuler
                    </button>

                    <button 
                        type="button"
                        class="btn btn-primary"
                        :disabled="logoutStatus === 'pending'"
                        @click="logout">
                        Déconnecter les appareils
                    </button>

                </template>
            </dialog-modal>

        </template>

    </form-section>
</template>

<script>
    import { defineComponent, ref, onMounted } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import FormSection from '../../Components/FormSection'
    import Error from '../../Components/Error'
    import DialogModal from '../../Components/DialogModal'
    
    export default defineComponent({

        components: {
            FormSection,
            Error,
            DialogModal,
        },

        props: {
            user: {
                required: true,
            },
        },

        setup(props) {

            const sessions = ref([]);

            const { 
                status: sessionsStatus,
                error: sessionsError,
                send: sessionsSend,
            } = useForm();

            function readSessions()
            {
                sessionsSend('get', '/browser-session').then(r => {
                    sessions.value = r.data;
                });
            }

            onMounted(readSessions);


            const confirmingLogout = ref(false);

            const { 
                data: logoutData,
                status: logoutStatus,
                error: logoutError,
                send: logoutSend,
            } = useForm();

            function openLogout()
            {
                confirmingLogout.value = true;
                setTimeout(() => this.$refs.password.focus(), 250)
            }

            function closeLogout()
            {
                confirmingLogout.value = false;
            }

            function logout()
            {
                logoutSend('delete', '/browser-session').then(r => {
                    readSessions();
                    closeLogout();
                });
            }

            return {
                sessions,
                sessionsStatus,
                sessionsError,
                sessionsSend,
                readSessions,

                logoutData,
                logoutStatus,
                logoutError,
                logoutSend,
                logout,

                confirmingLogout,
                openLogout,
                closeLogout,
            }
        },
    })
</script>
