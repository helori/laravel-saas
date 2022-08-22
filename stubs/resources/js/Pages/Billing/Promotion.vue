<template>
    <div v-if="promotion" class="flex gap-2 items-center text-green-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>Remise de</div> 
        <div v-if="promotion.coupon.amount_off">
            {{ $filters.number(promotion.coupon.amount_off, 0) }} €
        </div>
        <div v-if="promotion.coupon.percent_off">
            {{ $filters.number(promotion.coupon.percent_off, 0) }} %
        </div>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    export default defineComponent({
        props: {
            promotion: {
                
            }
        }
    })
</script>
