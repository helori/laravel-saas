<template>
    <form-section>
        <template #title>
            Mon équipe
        </template>

        <template #description>
            Informations et paramètres de votre équipe
        </template>

        <template #form>

            <div class="col-span-6 sm:col-span-4" v-if="team">

                <div v-if="user.role !== 'owner'"
                    class="text-lg font-semibold mb-3">
                    {{ team.name }}
                </div>

                <div v-if="user.role === 'owner'"
                    class="mb-3">

                    <label for="name" class="label">
                        Nom de mon équipe :
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        class="input mb-2 mt-1 block w-full"
                        :class="{ 'invalid': updateError }"
                        placeholder="Nom de mon équipe..."
                        autocomplete="team_name"
                        v-model="updateData.name">

                </div>

                <div class="text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Votre rôle : </span>
                    <span class="font-semibold"
                        :class="(user.role === 'owner' ? 'text-green-500' : '')">
                        {{ nameForRole(user.role) }}
                    </span>
                </div>

                <div class="text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Nombre de membres : </span>
                    <span class="font-semibold">{{ team.users.length }}</span>
                </div>

                <div v-if="user.role === 'owner'">
                    <div class="my-4 h-px bg-gray-200 dark:bg-gray-600"></div>

                    <label for="name" class="label mb-2">
                        Logo de mon équipe :
                    </label>

                    <input-file
                        v-if="!team.logo"
                        @files-selected="setLogo"
                        :multiple="false">
                        <template #content>
                            <div v-if="updateData.logo">
                                Fichier choisi : <strong>{{ updateData.logo.name }}</strong>
                            </div>
                            <div v-else>
                                Choisir un fichier de logo...
                            </div>
                        </template>
                    </input-file>

                    <template v-if="team.logo">
                        <img :src="'storage/' + team.logo"
                            :alt="Logo"
                            class="h-20 mt-2" />
                    </template>
                </div>
           </div>
        </template>

        <template #actions>

            <request-error :error="readTeamError" class="inline-block" />
            <request-error :error="updateError" class="inline-block" />
            <request-error :error="deleteLogoError" class="inline-block" />

            <div class="alert alert-green"
                v-if="updateStatus === 'success'">
                Enregistré !
            </div>

            <request-button
                v-if="team && team.logo && user.role === 'owner'"
                class="btn btn-red"
                :status="deleteLogoStatus"
                :error="deleteLogoError"
                @click="deleteLogo">
                Supprimer le logo
            </request-button>

            <button
                v-if="team && user.role === 'owner'"
                type="button"
                class="btn btn-primary"
                :class="{ 'opacity-25': (updateStatus === 'pending') }"
                :disabled="updateStatus === 'pending'"
                @click="updateTeam">
                Enregistrer
            </button>

        </template>
    </form-section>
</template>

<script>
    import { defineComponent, ref, onMounted } from 'vue'
    import { functions } from 'helorui'
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
            } = functions.useRequest();

            function readTeam()
            {
                readTeamSend('get', '/team').then(r => {
                    team.value = r.data;
                    updateData.value.name = r.data.name;
                });
            }

            onMounted(readTeam)

            // ---------------------------------------------------
            //  Update team
            // ---------------------------------------------------
            const {
                data: updateData,
                status: updateStatus,
                error: updateError,
                send: updateSend,
            } = functions.useRequest();

            function setLogo(e)
            {
                updateData.value.logo = e.files[0];
            }

            function updateTeam()
            {
                let data = updateData.value;

                let formData = new FormData();
                formData.append('name', updateData.value.name)

                if(updateData.value.logo)
                {
                    formData.append('logo', updateData.value.logo, updateData.value.logo.name);
                }
                else if(updateData.value.logo === null)
                {
                    formData.append('logo', null);
                }

                updateData.value = formData;

                updateSend('post', '/api/team-update').then(r => {
                    updateData.value = data;
                    if(updateData.value.logo){
                        window.location.reload();
                    }else{
                        readTeam();
                    }
                }).catch(r => {
                    updateData.value = data;
                });
            }

            const {
                status: deleteLogoStatus,
                error: deleteLogoError,
                data: deleteLogoData,
                send: deleteLogoSend,
            } = functions.useRequest();

            function deleteLogo()
            {
                deleteLogoData.value.logo = null;

                deleteLogoSend('post', '/api/team-update').then(r => {
                    window.location.reload();
                });
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
                readTeam,

                updateData,
                updateStatus,
                updateError,
                updateSend,
                updateTeam,

                setLogo,
                deleteLogo,
                deleteLogoStatus,
                deleteLogoError,

                nameForRole,
            }
        }
    })
</script>
