<template>
    <div class="py-2 px-3 rounded-md font-semibold border"
        :class="{
            'bg-blue-100 border-primary-300 text-blue-600': (type === 'primary'),
            'bg-green-100 border-green-300 text-green-600': (type === 'success'),
            'bg-yellow-100 border-yellow-300 text-yellow-600': (type === 'warning'),
            'bg-red-100 border-red-300 text-red-700': (type === 'danger'),
            'bg-blue-100 border-blue-300 text-blue-600': (type === 'info'),
        }">
        <slot></slot>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: {
            type: {
                type: String,
                required: true,
            }
        }
    })
</script>
