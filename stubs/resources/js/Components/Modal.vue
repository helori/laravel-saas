<template>
    <teleport to="body">
        <transition leave-active-class="duration-200">
            <div v-show="show" class="fixed inset-0 overflow-y-auto px-4 py-6 z-50" scroll-region>
                <transition enter-active-class="ease-out duration-300"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="ease-in duration-200"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0">
                    <div 
                        v-show="show" 
                        class="fixed inset-0 transform transition-all" 
                        @click="close">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                </transition>

                <transition enter-active-class="ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-active-class="ease-in duration-200"
                        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div v-show="show" 
                        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto" 
                        :class="maxWidthClass">
                        <slot v-if="show"></slot>
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>

<script>
import { defineComponent, onMounted, onUnmounted } from "vue";
export default defineComponent({
        emits: ['close'],
        props: {
            show: {
                default: false
            },
            maxWidthClass: {
                default: 'max-w-screen-lg'
            },
            closeable: {
                default: true
            },
        },
        watch: {
            show: {
                immediate: true,
                handler: (show) => {
                    if (show) {
                        document.body.style.overflow = 'hidden'
                    } else {
                        document.body.style.overflow = null
                    }
                }
            }
        },
        setup(props, {emit}) {
            const close = () => {
                if (props.closeable) {
                    emit('close')
                }
            }
            const closeOnEscape = (e) => {
                if (e.key === 'Escape' && props.show) {
                    close()
                }
            }
            onMounted(() => document.addEventListener('keydown', closeOnEscape))
            onUnmounted(() => {
                document.removeEventListener('keydown', closeOnEscape)
                document.body.style.overflow = null
            })
            return {
                close,
            }
        },
    })
</script>
