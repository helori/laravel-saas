<template>
    <form-section>
        <template #title>
            Membres de l'équipe
        </template>

        <template #description>
            Gérer les membres de votre équipe
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">

                <table class="w-100 table-fixed border-collapse">
                    <thead>
                        <tr>
                            <th class="w-1/2 py-2 px-3 bg-gray-100 border border-gray-300">Membre</th>
                            <th class="w-1/4 py-2 px-3 bg-gray-100 border border-gray-300">Rôle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users"
                            :key="user.id">
                            <td class="py-2 px-3 bg-gray-050 border border-gray-300">{{ user.firstname }} {{ user.lastname }}</td>
                            <td class="py-2 px-3 bg-gray-050 border border-gray-300">{{ nameForRole(user.pivot.role) }}</td>
                        </tr>
                    </tbody>
                </table>

           </div>
        </template>

    </form-section>
</template>

<script>
    import { defineComponent, ref, onMounted } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import FormSection from '../../Components/FormSection'
    import Error from '../../Components/Error'
    
    export default defineComponent({
        components: {
            FormSection,
            Error,
        },

        props: {
            user: {
                required: true,
            }
        },

        setup(props) {

            const users = ref(null);

            const { 
                status: readStatus,
                error: readError,
                send: readSend,
            } = useForm();

            function read()
            {
                readSend('get', '/team/' + props.user.current_team.id + '/user').then(r => {
                    users.value = r.data;
                });
            }

            onMounted(read)

            function nameForRole(role){
                if(role === 'owner') { return 'Propriétaire'; }
                if(role === 'member') { return 'Membre'; }
                return '';
            }

            return {
                users,
                readStatus,
                readError,
                readSend,
                read,

                nameForRole,
            }
        }
    })
</script>
