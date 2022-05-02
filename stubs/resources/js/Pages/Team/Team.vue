<template>
    <div>
        <div class="max-w-7xl mx-auto sm:py-10 sm:px-6 lg:px-8">

            <team-section :user="user" />

            <separator />

            <template v-if="user.is_owner">
                <members-section :user="user" />
                <separator />
            </template>

        </div>
    </div>
</template>

<script>

import { defineComponent } from "vue";
import Separator from '../../Components/Separator.vue'
import TeamSection from './TeamSection.vue'
import MembersSection from './MembersSection.vue'

export default defineComponent({

    components: {
        Separator,
        TeamSection,
        MembersSection,
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
