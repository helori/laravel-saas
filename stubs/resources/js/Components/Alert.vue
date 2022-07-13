<template>
    <div class="py-3 px-4 rounded-md font-medium border"
        :class="{
            'bg-primary-100 border-primary-500 text-primary-700': (type === 'primary'),
            'bg-green-100 border-green-500 text-green-800': (type === 'success'),
            'bg-yellow-100 border-yellow-500 text-yellow-700': (type === 'warning'),
            'bg-red-100 border-red-600 text-red-800': (type === 'danger'),
            'bg-blue-100 border-primary-500 text-blue-700': (type === 'info'),
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
