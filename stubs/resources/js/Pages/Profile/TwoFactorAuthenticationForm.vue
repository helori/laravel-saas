<template>
    <form-section>
        <template #title>
            Double authentification
        </template>

        <template #description>
            Sécurisez votre compte grâce à l'authentification à 2 facteurs.
        </template>

        <template #content>

            <h3 class="text-lg font-medium text-green-500" v-if="twoFactorEnabled">
                Vous avez activé la double authentification.
            </h3>

            <h3 class="text-lg font-medium text-red-500" v-else>
                Vous n'avez pas activé la double authentification.
            </h3>

            <div class="mt-3 max-w-xl text-sm text-gray-600 dark:text-gray-200">
                <p>
                    Quand la double authentification est activée, un code de sécurité vous sera demandé lors de vos connexions.
                    Les codes de sécurité ont une durée de vie limitée et sont générés par une application de type Google Authenticator.
                </p>
            </div>

            <div v-if="twoFactorEnabled">
                
                <div v-if="qrCode">
                    <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-200">
                        <p class="font-semibold">
                            La double authentification est à présent activée.
                            Scannez le QR Code avec l'application générateur de code de votre téléphone (par exemple Google Authenticator)
                        </p>
                    </div>

                    <div class="mt-4" v-html="qrCode">
                    </div>
                </div>

                <div v-if="recoveryCodes.length > 0">
                    <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-200">
                        <p class="font-semibold">
                            Enregistez ces codes de secours en lieu sûr, de préférence dans un gestionnaire de mots de passe.
                            Ils servent à restaurer l'accès à votre compte si vous perdez l'accès à votre générateur de codes de sécurité (votre téléphone).
                        </p>
                    </div>

                    <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-800 rounded-lg">
                        <div v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

        </template>

        <template #actions>

            <template v-if="!twoFactorEnabled">
                <confirms-password @confirmed="enable">
                    <button
                        type="button" 
                        class="btn btn-primary"
                        :class="{ 
                            'opacity-25': enabling
                        }" 
                        :disabled="enabling">
                        Activer la double authentification
                    </button>
                </confirms-password>
            </template>

            <template v-else>

                <confirms-password @confirmed="regenerateRecoveryCodes"
                    v-if="recoveryCodes.length > 0">
                    <button
                        type="button" 
                        class="btn btn-white"
                        :class="{ 
                            'opacity-25': regenerating
                        }" 
                        :disabled="regenerating">
                        Regénérer les codes de secours
                    </button>
                </confirms-password>

                <confirms-password @confirmed="showRecoveryCodes"
                    v-if="recoveryCodes.length === 0">
                    <button
                        type="button" 
                        class="btn btn-white"
                        :class="{ 
                            'opacity-25': recovering
                        }" 
                        :disabled="recovering">
                        Afficher les codes de secours
                    </button>
                </confirms-password>

                <confirms-password @confirmed="disable">
                    <button
                        type="button" 
                        class="btn btn-red"
                        :class="{ 
                            'opacity-25': disabling
                        }" 
                        :disabled="disabling">
                        Désactiver
                    </button>
                </confirms-password>

            </template>

        </template>

    </form-section>
</template>

<script>
    import { defineComponent, ref, computed } from 'vue'
    import { useForm } from '../../Functions/useForm'
    import FormSection from '../../Components/FormSection'
    import ConfirmsPassword from './ConfirmsPassword'
    
    export default defineComponent({
        components: {
            FormSection,
            ConfirmsPassword,
        },

        props: {
            user: {
                required: true,
            }
        },

        setup(props) {

            const twoFactorEnabled = ref(props.user.two_factor_enabled);
            const qrCode = ref(null);
            const recoveryCodes = ref([]);
            
            const { 
                status: enableStatus,
                error: enableError,
                send: enableSend,
            } = useForm();

            function enable(){
                enableSend('post', '/user/two-factor-authentication').then(r => {
                    twoFactorEnabled.value = true;
                    showQrCode();
                    showRecoveryCodes();
                });
            }

            const enabling = computed(() => {
                return (enableStatus.value === 'pending');
            })



            const { 
                status: disableStatus,
                error: disableError,
                send: disableSend,
            } = useForm();

            function disable(){
                disableSend('delete', '/user/two-factor-authentication').then(r => {
                    twoFactorEnabled.value = false;
                });
            }

            const disabling = computed(() => {
                return (disableStatus.value === 'pending');
            })



            const { 
                status: showQrCodeStatus,
                error: showQrCodeError,
                send: showQrCodeSend,
            } = useForm();

            function showQrCode(){
                showQrCodeSend('get', '/user/two-factor-qr-code').then(r => {
                    qrCode.value = r.data.svg;
                });
            }


            const { 
                status: showRecoveryCodesStatus,
                error: showRecoveryCodesError,
                send: showRecoveryCodesSend,
            } = useForm();

            function showRecoveryCodes() {
                showRecoveryCodesSend('get', '/user/two-factor-recovery-codes').then(r => {
                    recoveryCodes.value = r.data;
                });
            }

            const recovering = computed(() => {
                return (showRecoveryCodesStatus.value === 'pending');
            })


            const { 
                status: regenerateRecoveryCodesStatus,
                error: regenerateRecoveryCodesError,
                send: regenerateRecoveryCodesSend,
            } = useForm();

            function regenerateRecoveryCodes() {
                regenerateRecoveryCodesSend('post', '/user/two-factor-recovery-codes').then(r => {
                    showRecoveryCodes()
                });
            }

            const regenerating = computed(() => {
                return (regenerateRecoveryCodesStatus.value === 'pending');
            })

            return {
                twoFactorEnabled,
                recoveryCodes,
                qrCode,
                
                enableStatus,
                enableError,
                enableSend,
                enable,
                enabling,

                disableStatus,
                disableError,
                disableSend,
                disable,
                disabling,

                showQrCodeStatus,
                showQrCodeError,
                showQrCodeSend,
                showQrCode,
                
                showRecoveryCodesStatus,
                showRecoveryCodesError,
                showRecoveryCodesSend,
                showRecoveryCodes,
                recovering,
                
                regenerateRecoveryCodesStatus,
                regenerateRecoveryCodesError,
                regenerateRecoveryCodesSend,
                regenerateRecoveryCodes,
                regenerating,
            }
        },
    })
</script>
