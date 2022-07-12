<template>
    <dialog-modal
        ref="dialog"
        :max-width-class="maxWidthClass">
        
        <template #title>
            {{ title }}
        </template>

        <template #content>
            <slot name="content"></slot>
            <error :errors="error" class="mt-2" />
        </template>
        
        <template #footer>
            <button 
                type="button"
                class="btn btn-white"
                @click="close">
                {{ cancelText }}
            </button>

            <button 
                id="card-button" 
                class="btn ml-3"
                :class="{
                    'btn-primary': (type === 'primary'),
                    'btn-red': (type === 'danger'),
                }"
                :disabled="status === 'pending'"
                @click="request">
                {{ button }}
            </button>
        </template>

    </dialog-modal>
</template>

<script>
    import { defineComponent, ref } from 'vue'
    import { useForm } from '../Functions/useForm'
    import DialogModal from './DialogModal'
    import Error from './Error'

    export default defineComponent({
        
        emits: ['open', 'close'],
        
        components: {
            DialogModal,
            Error,
        },

        props: {
            maxWidthClass: {
                default: 'max-w-screen-lg'
            },
            type: {
                type: String,
                required: false,
                default: 'primary',
            },
            title: {
                type: String,
                required: true,
            },
            button: {
                type: String,
                required: false,
                default: 'Enregistrer',
            },
            cancelText: {
                type: String,
                required: false,
                default: 'Annuler',
            },
            callback: {
                required: true,
            },
        },

        setup(props, { emit })
        {
            const dialog = ref(null);

            const { 
                status,
                error,
                send,
                data,
            } = useForm();

            function request(){
                props.callback();
            }

            function open() {
                dialog.value.open();
                //this.$refs.input.focus();
                emit('open');
            }

            function close() {
                dialog.value.close();
                emit('close');
            }

            return {
                status,
                error,
                send,
                data,
                request,

                dialog,
                open,
                close,
            }
        },
    })
</script>
