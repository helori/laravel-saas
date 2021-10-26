<template>
    <div v-if="errors" 
        class="alert alert-red">

        <!--div v-if="request.name !== null">
            Erreur : {{ request.name }}
        </div-->

        <div v-if="errors.response && errors.response.data">
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <!-- Validation error list -->
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <div v-if="errors.response.data.errors">
                <div v-if="firstErrorOnly">
                    {{ errors.response.data.errors[Object.keys(errors.response.data.errors)[0]][0] }}
                </div>
                <div v-else>
                    <div v-for="(errorsGroup, field) in errors.response.data.errors">
                        <div v-for="error in errorsGroup">
                            {{ error }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <!-- Other custom error message -->
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <div v-else-if="errors.response.data.message">
                {{ errors.response.data.message }}
            </div>
        </div>

        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
        <!-- Generic error message for the error code -->
        <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
        <div v-else-if="errors.message">
            {{ errors.message }}
        </div>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: {
            errors: {
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
