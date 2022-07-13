<template>
    <alert type="danger" v-if="error">

        <!--div v-if="request.name !== null">
            Erreur : {{ request.name }}
        </div-->

        <div v-if="error.response && error.response.data">
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <!-- Validation error list -->
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <div v-if="error.response.data.errors">
                <div v-if="firstErrorOnly">
                    {{ error.response.data.errors[Object.keys(error.response.data.errors)[0]][0] }}
                </div>
                <div v-else>
                    <div v-for="(errorsGroup, field) in error.response.data.errors">
                        <div v-for="err in errorsGroup">
                            {{ err }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <!-- Other custom error message -->
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <div v-else-if="error.response.data.message">
                {{ error.response.data.message }}
            </div>
        </div>

        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
        <!-- Generic error message for the error code -->
        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
        <div v-else-if="error.message">
            {{ error.message }}
        </div>
    </alert>
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: {
            error: {
                required: true
            },
            firstErrorOnly: {
                type: Boolean,
                default: true,
                required: false,
            }
        }
    })
</script>
