<template>
    <div>

        <div class="grid md:grid-cols-2 gap-2 md:gap-6">
            <div>
                <div class="text-xs mb-1 uppercase text-gray-500">
                    Prénom :
                </div>
                <input 
                    required
                    type="text"
                    name="firstname"
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
                    type="text"
                    name="lastname"
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
                    type="email" 
                    id="email"
                    placeholder="Email..." 
                    required="" 
                    class="input w-full mb-2" 
                    name="email"
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
                    name="activated"
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
                    name="team"
                    v-model="createPassword">
                <div>
                    Définir un mot de passe
                </div>
            </label>
        </div>

        <div v-show="createPassword"
            class="mt-2">
            <div class="grid md:grid-cols-2 gap-2 md:gap-6">
                <div>
                    <div class="text-xs mb-1 uppercase text-gray-500">
                        Mot de passe :
                    </div>
                    <input 
                        type="password" 
                        id="password"
                        placeholder="Mot de passe..." 
                        required="" 
                        class="input w-full mb-2" 
                        name="password"
                        v-model="item.password">
                </div>
                <div>
                    <div class="text-xs mb-1 uppercase text-gray-500">
                        Confirmation du mot de passe :
                    </div>
                    <input 
                        type="password" 
                        id="password-confirm"
                        placeholder="Confirmation..." 
                        required="" 
                        class="input w-full mb-2" 
                        name="password_confirmation"
                        v-model="item.password_confirmation">
                </div>
            </div>
        </div>

        <div class="mt-1 text-gray-500">
            Vous n'êtes pas obligé de définir un mot de passe: après avoir créé le membre,
            vous pourrez lui envoyer une invitation à créer son mot de passe lui-même
            lors de sa première connexion (recommandé).
        </div>

    </div>
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

        return {
            item,
            createPassword,
        }
    }
})
</script>
