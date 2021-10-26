<template>
    <span>
        <span @click="checkConfirm">
            <slot />
        </span>

        <dialog-modal :show="confirming" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>

                {{ content }}

                <div class="mt-4">

                    <input
                        ref="password"
                        id="password"
                        type="password"
                        class="input w-3/4"
                        placeholder="Mot de passe"
                        v-model="confirmData.password"
                        @keyup.enter="confirm">

                </div>
            </template>

            <template #footer>

                <error :errors="confirmError" class="inline-block mr-3" />

                <button 
                    type="button"
                    class="btn btn-white"
                    @click="closeModal">
                    Cancel
                </button>

                <button 
                    type="button"
                    class="btn btn-blue ml-3"
                    :class="{ 'opacity-25': (confirmStatus === 'pending') }"
                    :disabled="confirmStatus === 'pending'"
                    @click="confirm">
                    {{ button }}
                </button>

            </template>
        </dialog-modal>
    </span>
</template>

<script>
    import { defineComponent, ref, nextTick } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import DialogModal from '../../Components/DialogModal.vue'
    import Error from '../../Components/Error.vue'
    
    export default defineComponent({
        
        emits: ['confirmed'],

        components: {
            DialogModal,
            Error,
        },

        props: {
            title: {
                default: 'Confirm Password',
            },
            content: {
                default: 'For your security, please confirm your password to continue.',
            },
            button: {
                default: 'Confirm',
            }
        },

        setup(){
            const confirming = ref(false);

            const { 
                status: checkConfirmStatus,
                error: checkConfirmError,
                send: checkConfirmSend,
            } = useForm();

            function checkConfirm(){
                checkConfirmSend('get', '/user/confirmed-password-status').then(r => {
                    console.log(r.data)
                    if (r.data.confirmed) {
                        this.$emit('confirmed', confirmData.value.password)
                    }else{
                        confirming.value = true;
                        setTimeout(() => this.$refs.password.focus(), 250)
                    }
                })
            }

            const { 
                data: confirmData,
                status: confirmStatus,
                error: confirmError,
                send: confirmSend,
            } = useForm();

            confirmData.value = {
                password: '',
            }

            function confirm(){
                confirmSend('post', '/user/confirm-password').then(r => {
                    //await nextTick()
                    //$emit('confirmed')
                    this.$nextTick(() => this.$emit('confirmed', confirmData.value.password));
                    //closeModal()
                })
            }

            function closeModal(){
                confirming.value = false;
                //confirmData.value.password = '';
            }

            return {
                confirming,

                checkConfirmStatus,
                checkConfirmError,
                checkConfirmSend,
                checkConfirm,

                confirmData,
                confirmStatus,
                confirmError,
                confirmSend,
                confirm,

                closeModal,
            };
        },
    })
</script>
