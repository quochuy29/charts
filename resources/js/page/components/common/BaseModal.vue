<template>
    <Teleport to="body">
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
            <div 
                class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" 
                @click="$emit('close')"
            ></div>
            
            <div 
                class="bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all z-10 flex flex-col max-h-[90vh]"
            >
                <div class="px-6 py-4 border-b flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
                    <button 
                        @click="$emit('close')" 
                        class="text-gray-400 hover:text-gray-500 focus:outline-none p-1"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-grow">
                    <slot></slot>
                </div>

                <div 
                    v-if="$slots.footer" 
                    class="px-6 py-4 border-t flex justify-end gap-3 shrink-0 bg-gray-50 rounded-b-lg"
                >
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { X } from 'lucide-vue-next';

defineProps({
    isOpen: Boolean,
    title: { type: String, default: '' }
});

defineEmits(['close']);
</script>