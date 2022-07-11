<template>
    <div>

        <div class="grid grid-cols-2 gap-6">
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

        <div class="grid grid-cols-2 gap-6">
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
                    Téléphone :
                </div>
                <input 
                    type="text" 
                    id="phone"
                    placeholder="Téléphone..." 
                    required="" 
                    class="input w-full mb-2" 
                    name="phone"
                    v-model="item.phone">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
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
                    Confirmation :
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
                
        <div class="text-xs mb-1 uppercase text-gray-500">
            Rôle :
        </div>
        <select
            v-model="item.role"
            class="input w-full mb-2">
            <option value="member">Membre</option>
            <option value="owner">Propriétaire</option>
        </select>

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

        <div>
            <label class="inline-flex items-center gap-2"
                for="invite-member">
                <input 
                    id="invite-member"
                    type="checkbox" 
                    class="form-checkbox" 
                    name="team"
                    v-model="invite">
                <div>
                    Envoyer une invitation
                </div>
            </label>
        </div>

        <div v-if="invite">
            <input 
                type="email" 
                placeholder="Email d'invitation..." 
                required="" 
                class="input w-full mb-2" 
                name="invitation_email"
                v-model="item.invitation_email">
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
        const invite = ref(true);

        watch(() => item, (newValue, oldValue) => {
            emit('update:member', newValue);
        })

        return {
            item,
            invite,
        }
    }
})
</script>
