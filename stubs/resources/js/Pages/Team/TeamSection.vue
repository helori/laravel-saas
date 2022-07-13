<template>
    <form-section>
        <template #title>
            Équipe courante
        </template>

        <template #description>
            Équipe à laquelle vous êtes actuellement connecté
        </template>

        <template #form>

            <div class="col-span-6 sm:col-span-4" v-if="team">

                <div v-if="team.pivot.role !== 'owner'"
                    class="text-lg font-semibold mb-3">
                    {{ team.name }}
                </div>

                <div v-if="team.pivot.role === 'owner'"
                    class="mb-3">
                    
                    <label for="name" class="label">
                        Nom de mon équipe
                    </label>

                    <input 
                        id="name"
                        type="text"
                        name="name"
                        class="input mb-2 mt-1 block w-full"
                        :class="{
                            'invalid': updateError
                        }"
                        placeholder="Nom de mon équipe..."
                        autocomplete="team_name"
                        v-model="updateData.name">

                </div>

                <div class="text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Votre rôle : </span>
                    <span class="font-semibold" 
                        :class="(team.pivot.role === 'owner' ? 'text-green-500' : '')">
                        {{ nameForRole(team.pivot.role) }}
                    </span>
                </div>

                <div class="text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Nombre de membres : </span>
                    <span class="font-semibold">{{ team.users.length }}</span>
                </div>

           </div>
        </template>

        <template #actions>

            <request-error :error="readTeamError" class="inline-block" />
            <request-error :error="updateError" class="inline-block" />
            <request-error :error="readTeamsError" class="inline-block" />

            <div class="alert alert-green" 
                v-if="updateStatus === 'success'">
                Enregistré !
            </div>

            <button 
                v-if="team && team.pivot.role === 'owner'"
                type="button"
                class="btn btn-primary"
                :class="{ 'opacity-25': (updateStatus === 'pending') }"
                :disabled="updateStatus === 'pending'"
                @click="updateTeam">
                Enregistrer
            </button>

            <button 
                type="button"
                class="btn btn-white"
                @click="switchOpen">
                Changer d'équipe
            </button>

        </template>
    </form-section>

    <dialog-modal 
        :show="switching" 
        @close="switchClose" 
        max-width-class="max-w-screen-sm">
        <template #title>
            Changer d'équipe
        </template>

        <template #content>

            <div class="text-gray-500 dark:text-gray-400 font-sm mb-3">
                Choisissez une équipe à laquelle vous appartenez :
            </div>

            <div v-for="t in teams" :key="t.id">
                <label class="inline-flex items-center">
                    <input 
                        type="radio" 
                        class="form-radio" 
                        name="team" 
                        v-model="teamSelectedId"
                        :value="t.id">
                    <div>
                        <span class="ml-2">{{ t.name }}</span>
                        <span class="ml-2" 
                            :class="(t.pivot.role === 'owner' ? 'text-green-500' : 'text-gray-500')">
                            ({{ nameForRole(t.pivot.role) }})
                        </span>
                    </div>
                </label>
            </div>

        </template>

        <template #footer>

            <request-error :error="switchError" class="inline-block mr-3" />

            <button 
                type="button"
                class="btn btn-white"
                @click="switchClose">
                Annuler
            </button>

            <button 
                type="button"
                class="btn btn-primary ml-2"
                :class="{ 'opacity-25': (switchStatus === 'pending') }"
                :disabled="switchStatus === 'pending'"
                @click="switchTeam">
                Changer d'équipe
            </button>

        </template>
    </dialog-modal>
</template>

<script>
    import { defineComponent, ref, onMounted } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import FormSection from '../../Components/FormSection'
    
    export default defineComponent({
        components: {
            FormSection,
        },

        props: {
            user: {
                required: true,
            }
        },

        setup(props) {

            // ---------------------------------------------------
            //  Current user's team
            // ---------------------------------------------------
            const team = ref(null);

            const { 
                status: readTeamStatus,
                error: readTeamError,
                send: readTeamSend,
            } = useForm();

            function readTeam()
            {
                readTeamSend('get', '/team').then(r => {
                    team.value = r.data;
                    updateData.value.name = r.data.name;
                });
            }

            onMounted(readTeam)

            // ---------------------------------------------------
            //  Teams the user belongs to
            // ---------------------------------------------------
            const teams = ref(null);

            const { 
                status: readTeamsStatus,
                error: readTeamsError,
                send: readTeamsSend,
            } = useForm();

            function readTeams()
            {
                readTeamsSend('get', '/teams').then(r => {
                    teams.value = r.data;
                });
            }

            onMounted(readTeams)

            // ---------------------------------------------------
            //  Update team
            // ---------------------------------------------------
            const { 
                data: updateData,
                status: updateStatus,
                error: updateError,
                send: updateSend,
            } = useForm();

            function updateTeam()
            {
                updateSend('put', '/team').then(r => {
                    readTeam();
                });
            }

            // ---------------------------------------------------
            //  Switch team
            // ---------------------------------------------------
            const switching = ref(false);
            const teamSelectedId = ref(null);

            const { 
                status: switchStatus,
                error: switchError,
                send: switchSend,
            } = useForm();

            function switchTeam()
            {
                switchSend('post', '/team/switch/' + teamSelectedId.value).then(r => {
                    
                    //switchClose();
                    //team.value = r.data;
                    //this.emitter.emit("team-updated", r.data);
                    window.location.reload();
                    // refresh page ?
                });
            }

            function switchOpen(){
                teamSelectedId.value = team.value.id;
                switching.value = true;
            }

            function switchClose(){
                switching.value = false;
            }

            function nameForRole(role){
                if(role === 'owner') { return 'Propriétaire'; }
                if(role === 'member') { return 'Membre'; }
                return '';
            }

            // ---------------------------------------------------
            //  Return
            // ---------------------------------------------------
            return {
                team,
                readTeamStatus,
                readTeamError,
                readTeamSend,
                readTeam,

                teams,
                readTeamsStatus,
                readTeamsError,
                readTeamsSend,
                readTeams,

                switching,
                teamSelectedId,
                switchStatus,
                switchError,
                switchSend,
                switchTeam,
                switchOpen,
                switchClose,

                updateData,
                updateStatus,
                updateError,
                updateSend,
                updateTeam,

                nameForRole,
            }
        }
    })
</script>
