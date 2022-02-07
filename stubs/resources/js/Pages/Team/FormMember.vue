<template>
    <div>

        <div class="grid grid-cols-1 gap-6">

            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <!-- Column 1 -->
            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
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

    </div>
</template>

<script>

import { defineComponent, ref, watch, onMounted } from "vue"
import { useForm } from '../../Functions/useForm'

export default defineComponent({

    emits: ['update:member'],

    props: {
        user: {
            required: true,
        },
        member: {
            required: true,
        },
    },

    setup(props, { emit }){

        const item = props.member;

        watch(() => item, (newValue, oldValue) => {
            emit('update:member', newValue);
        })

        return {
            item,
        }
    }
})
</script>
