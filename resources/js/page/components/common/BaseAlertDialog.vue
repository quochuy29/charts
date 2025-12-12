<template>
    <Teleport to="body">
        <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>
            <div class="relative bg-white p-6 rounded-lg shadow-xl w-[400px] transform transition-all scale-100">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>
                    <div class="mt-2 text-sm text-gray-500 leading-relaxed">
                        <slot></slot>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button @click="$emit('close')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        キャンセル
                    </button>
                    <button @click="$emit('confirm')"
                        class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 shadow-sm transition-colors">
                        削除する
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