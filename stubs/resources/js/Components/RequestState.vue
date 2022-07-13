<template>
    <div>
        <request-status :status="status" :message="message" :icon-classes="iconClasses" :message-classes="messageClasses" />
        <request-error :error="error" />
    </div>
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: {
            status: {
                required: true,
                default: null,
            },
            error: {
                required: false,
                default: null,
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
