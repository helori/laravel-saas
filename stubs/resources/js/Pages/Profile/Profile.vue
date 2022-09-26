<template>
    <div>
        <div class="max-w-7xl mx-auto sm:py-10 sm:px-6 lg:px-8">

            <update-profile-information-form :user="user" />

            <separator />

            <update-password-form :user="user" />

            <separator />

            <two-factor-authentication-form :user="user" />

            <separator />

            <logout-other-browser-session-form :user="user" />

            <separator />

            <manage-api-token-form :user="user" v-if="user.has_api" />

            <separator v-if="user.has_api" />

            <delete-user-account :user="user" />

        </div>
    </div>
</template>

<script>

import { defineComponent, ref } from "vue";
import Separator from '../../Components/Separator.vue'
import UpdateProfileInformationForm from './UpdateProfileInformationForm.vue'
import UpdatePasswordForm from './UpdatePasswordForm.vue'
import TwoFactorAuthenticationForm from './TwoFactorAuthenticationForm.vue'
import LogoutOtherBrowserSessionForm from './LogoutOtherBrowserSessionForm.vue'
import ManageApiTokenForm from './ManageApiTokenForm.vue'
import DeleteUserAccount from './DeleteUserAccount.vue'

export default defineComponent({

    components: {
        UpdateProfileInformationForm,
        UpdatePasswordForm,
        TwoFactorAuthenticationForm,
        LogoutOtherBrowserSessionForm,
        ManageApiTokenForm,
        DeleteUserAccount,
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
