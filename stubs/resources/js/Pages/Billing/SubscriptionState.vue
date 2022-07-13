<template>
    <div v-if="subscription">
        <alert type="warning" v-if="subscription.is_on_trial">
            Votre période d'essai se terminera le {{ $filters.date(subscription.trial_ends_at, 'DD/MM/YYYY') }}
        </alert>
        <alert type="success" v-if="subscription.is_recurring">
            Votre abonnement est actif depuis le {{ $filters.date(subscription.created_at, 'DD/MM/YYYY') }}
        </alert>
        <alert type="warning" v-if="subscription.is_on_grace_period">
            Votre abonnement se terminera le {{ $filters.date(subscription.ends_at, 'DD/MM/YYYY') }}
        </alert>
        <alert type="danger" v-if="subscription.is_ended">
            Votre abonnement est terminé depuis le {{ $filters.date(subscription.ends_at, 'DD/MM/YYYY') }}
        </alert>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    export default defineComponent({
        props: {
            subscription: {
                required: true,
                type: Object,
            }
        }
    })
</script>
