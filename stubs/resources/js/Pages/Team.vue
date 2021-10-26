<template>
    <div>
        <div class="max-w-7xl mx-auto sm:py-10 sm:px-6 lg:px-8">

            <team-update-form :user="user" />

            <separator />

            <template v-if="user.current_team.pivot.role === 'owner'">
                <team-members-form :user="user" />
                <separator />
            </template>

        </div>
    </div>
</template>

<script>

import { defineComponent } from "vue";

import TeamUpdateForm from './Team/TeamUpdateForm.vue'
import TeamMembersForm from './Team/TeamMembersForm.vue'
import Separator from '../Components/Separator.vue'

export default defineComponent({

    components: {
        TeamUpdateForm,
        TeamMembersForm,
        Separator,
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
