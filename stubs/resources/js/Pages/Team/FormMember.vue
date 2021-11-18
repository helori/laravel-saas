<template>
    <div>

        <div class="grid grid-cols-2 gap-2">
            <input 
                required
                type="text"
                name="firstname"
                placeholder="Prénom..."
                class="input"
                data-validation="required text"
                v-model="item.firstname">

            <input 
                required
                type="text"
                name="lastname"
                placeholder="Nom de naissance..."
                class="input"
                data-validation="required text"
                v-model="item.lastname">
        </div>
        <div class="grid grid-cols-2 gap-2 mt-2">
            <input 
                type="email" 
                id="email"
                placeholder="Email..." 
                required="" 
                class="input" 
                name="email"
                v-model="item.email">
            <input 
                type="text" 
                id="phone"
                placeholder="Téléphone..." 
                required="" 
                class="input" 
                name="phone"
                v-model="item.phone">
        </div>

    </div>
</template>

<script>

import { defineComponent, watch } from "vue"

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
