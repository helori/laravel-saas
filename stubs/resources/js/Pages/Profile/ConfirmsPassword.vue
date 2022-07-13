<template>
    <span>
        <span @click="checkConfirm">
            <slot />
        </span>

        <dialog-modal 
            :show="confirming" 
            @close="closeModal" 
            max-width-class="max-w-screen-sm">
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

                <request-error :error="confirmError" class="inline-block mr-3" />

                <button 
                    type="button"
                    class="btn btn-white"
                    @click="closeModal">
                    Annuler
                </button>

                <button 
                    type="button"
                    class="btn btn-primary ml-3"
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
    
    export default defineComponent({
        
        emits: ['confirmed'],

        props: {
            title: {
                default: 'Confirmer le mot de passe',
            },
            content: {
                default: 'Par sécurité, merci de confirmer votre mot de passe pour continuer.',
            },
            button: {
                default: 'Confirmer',
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
