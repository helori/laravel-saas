<template>
    <form autocomplete="off">

        <!--div class="font-semibold">
            Votre abonnement dépend du nombre de membres de votre équipe.
            L'ajout d'un membre sera pris en compte à votre prochaine échéance de facturation.
        </div>

        <div class="h-px my-4 bg-gray-200"></div-->

        <div class="grid md:grid-cols-2 gap-2 md:gap-6">
            <div>
                <div class="text-xs mb-1 uppercase text-gray-500">
                    Prénom :
                </div>
                <input 
                    required
                    autocomplete="off"
                    type="text"
                    placeholder="Prénom..."
                    class="input w-full mb-2"
                    data-validation="required text"
                    v-model="item.firstname">
            </div>
            <div>
                <div class="text-xs mb-1 uppercase text-gray-500">
                    Nom :
                </div>
                <input 
                    required
                    autocomplete="off"
                    type="text"
                    placeholder="Nom de naissance..."
                    class="input w-full mb-2"
                    data-validation="required text"
                    v-model="item.lastname">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-2 md:gap-6">
            <div>
                <div class="text-xs mb-1 uppercase text-gray-500">
                    Email :
                </div>
                <input 
                    ref="inputEmail"
                    autocomplete="off"
                    type="email" 
                    placeholder="Email..." 
                    required="" 
                    class="input w-full mb-2" 
                    v-model="item.email">
            </div>
            <div>
                <div class="text-xs mb-1 uppercase text-gray-500">
                    Rôle :
                </div>
                <select
                    v-model="item.role"
                    class="input w-full mb-2">
                    <option value="member">Membre</option>
                    <option value="owner">Propriétaire</option>
                </select>
            </div>
        </div>

        <div>
            <label class="inline-flex items-center gap-2"
                for="activated">
                <input 
                    id="activated"
                    type="checkbox" 
                    class="form-checkbox" 
                    v-model="item.activated">
                <div>
                    Activer le compte
                </div>
            </label>
        </div>

        <div class="h-px my-4 bg-gray-200"></div>

        <div>
            <label class="inline-flex items-center gap-2"
                for="invite-member">
                <input 
                    id="invite-member"
                    type="checkbox" 
                    class="form-checkbox" 
                    v-model="item.has_password">
                <div>
                    Définir un mot de passe
                </div>
            </label>
        </div>

        <div v-show="item.has_password"
            class="mt-2">
            <div class="grid md:grid-cols-2 gap-2 md:gap-6">
                <div>
                    <div class="text-xs mb-1 uppercase text-gray-500">
                        Mot de passe :
                    </div>
                    <input 
                        autocomplete="off"
                        type="password" 
                        placeholder="Mot de passe..." 
                        required="" 
                        class="input w-full mb-2" 
                        v-model="item.password">
                </div>
                <div>
                    <div class="text-xs mb-1 uppercase text-gray-500">
                        Confirmation du mot de passe :
                    </div>
                    <input 
                        autocomplete="off"
                        type="password" 
                        placeholder="Confirmation..." 
                        required="" 
                        class="input w-full mb-2" 
                        v-model="item.password_confirmation">
                </div>
            </div>
        </div>

        <div class="mt-1 text-gray-500">
            Vous n'êtes pas obligé de définir un mot de passe: après avoir créé le membre,
            vous pourrez lui envoyer une invitation à créer son mot de passe lui-même
            lors de sa première connexion (recommandé).
        </div>

    </form>
</template>

<script>

import { defineComponent, ref, watch, onMounted } from "vue"
import { useForm } from '../../Functions/useForm'

export default defineComponent({

    emits: ['update:member'],

    props: {
        member: {
            required: true,
        },
    },

    setup(props, { emit }){

        const item = props.member;
        const createPassword = ref(false);

        watch(() => item, (newValue, oldValue) => {
            emit('update:member', newValue);
        })

        const inputEmail = ref(null);

        onMounted(function(){
            inputEmail.value.value = '';
        })

        return {
            item,
            createPassword,

            inputEmail,
        }
    }
})
</script>
