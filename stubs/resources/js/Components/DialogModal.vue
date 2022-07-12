<template>
    <modal :show="show" :max-width-class="maxWidthClass" :closeable="closeable" @close="close">
        <div class="px-6 py-4 text-lg font-semibold"
            :class="headerClass">
            <slot name="title"></slot>
        </div>
        <div class="px-2 py-2 sm:px-6 sm:py-4 dark:bg-gray-900 dark:text-gray-200">
            <slot name="content"></slot>
        </div>
        <div class="px-6 py-4 text-right"
            :class="headerClass">
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
            maxWidthClass: {
                default: 'max-w-screen-lg'
            },
            closeable: {
                default: true
            },
            headerClass: {
                default: 'bg-gray-100 dark:bg-gray-800 dark:text-white'
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
