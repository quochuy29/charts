<template>
    <Teleport to="body">
        <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>

            <div class="relative bg-white p-6 rounded-lg shadow-xl transform transition-all scale-100"
                :class="widthClass">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900">{{ title }}</h2>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <slot></slot>

                <div class="mt-6 flex justify-end gap-3">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
    defineProps({
        isOpen: Boolean,
        title: String,
        widthClass: {
            type: String,
            default: 'w-[450px]'
        }
    });
    defineEmits(['close']);
</script>