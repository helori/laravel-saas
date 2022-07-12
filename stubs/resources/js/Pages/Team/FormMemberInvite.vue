<template>
    <div>

        <div class="mb-4">
            <div class="mb-4">Envoyer une invitation permet au membre de définir lui-même son mot de passe et de se connecter à l'application.</div>
            <div class="mb-4">C'est le moyen recommandé d'inviter les membres de votre équipe à se connecter pour la première fois (plus sécurisé que définir vous même leur mot de passe et le leur envoyer).</div>
            <div class="mb-4">Si vos membres perdent leur mot de passe, vous pouvez leur renvoyer une invitation (ils peuvent aussi utiliser eux-mêmes la procédure de récupération de mot de passe).</div>
            
            <div class="font-bold">Envoyer une invitation à {{ item.firstname }} {{ item.lastname }} ?</div>
            <div class="text-gray-500 italic text-sm">L'email sera envoyé à : {{ item.email }}</div>
        </div>

    </div>
</template>

<script>

import { defineComponent, ref, watch } from "vue"
import { useForm } from '../../Functions/useForm'

export default defineComponent({

    emits: ['update:member'],

    props: {
        member: {
            required: true,
        },
    },

    setup(props, { emit }){

        const item = ref(props.member);

        watch(() => item, (newValue, oldValue) => {
            emit('update:member', newValue);
        })

        return {
            item,
        }
    }
})
</script>
