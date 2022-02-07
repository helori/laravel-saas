<template>
    <modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="px-6 py-4 bg-gray-100 dark:bg-gray-800 dark:text-white text-lg font-semibold">
            <slot name="title"></slot>
        </div>
        <div class="px-6 py-4 dark:bg-gray-900 dark:text-gray-200">
            <slot name="content"></slot>
        </div>
        <div class="px-6 py-4 bg-gray-100 dark:bg-gray-800 dark:text-gray-200 text-right">
            <slot name="footer"></slot>
        </div>
    </modal>
</template>

<script>
    import { defineComponent, ref } from 'vue'
    import Modal from './Modal'

    export default defineComponent({
        
        emits: ['open', 'close'],
        
        components: {
            Modal,
        },

        props: {
            maxWidth: {
                default: '2xl'
            },
            closeable: {
                default: true
            },
        },

        setup(props, { emit }){
            const show = ref(false);

            function close() {
                show.value = false;
                emit('close');
            }

            function open() {
                show.value = true;
                emit('open');
            }

            return {
                show,
                open,
                close,
            }
        },
    })
</script>
