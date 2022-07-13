<template>
    <alert type="info" v-if="status === 'pending'">
        <loader
            :icon-classes="iconClasses"
            :message-classes="messageClasses"
            :message="message" />
    </alert>
</template>

<script>
    import { defineComponent } from 'vue'
    export default defineComponent({
        props: {
            status: {
                required: true
            },
            message: {
                type: String,
                required: false,
                default: "Chargement des données...",
            },
            iconClasses: {
                default: "w-6 h-6 text-gray-900",
            },
            messageClasses: {
                default: "",
            },
        }
    })
</script>
