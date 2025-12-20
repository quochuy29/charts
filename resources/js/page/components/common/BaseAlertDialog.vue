<template>
    <Teleport to="body">
        <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center">
            <div 
                class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity" 
                @click="$emit('close')"
            ></div>
            
            <div 
                class="relative bg-white p-6 rounded-lg shadow-lg w-full max-w-[512px] transform transition-all scale-100 border border-gray-200"
            >
                <div class="flex flex-col space-y-2 text-center sm:text-left">
                    <h3 class="text-lg font-semibold text-gray-900 leading-none tracking-tight">
                        {{ title }}
                    </h3>
                    <div class="text-sm text-gray-500">
                        <slot></slot>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 gap-2 sm:gap-0">
                    <button 
                        @click="$emit('close')"
                        class="px-4 py-2 border border-gray-200 rounded-md text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-gray-900 transition-colors focus:outline-none ring-offset-white focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2"
                    >
                        キャンセル
                    </button>
                    <button 
                        @click="$emit('confirm')"
                        class="px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-900/90 transition-colors shadow-sm focus:outline-none ring-offset-white focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2"
                    >
                        削除
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
    defineProps({
        isOpen: Boolean,
        title: { type: String, default: '削除の確認' }
    });
    defineEmits(['close', 'confirm']);
</script>