<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0" @click.self="close">
        <div class="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border bg-white p-6 shadow-lg duration-200 sm:rounded-lg">
            <div class="flex flex-col space-y-1.5 text-center sm:text-left">
                <h2 class="text-lg font-semibold leading-none tracking-tight">Y軸スケール設定</h2>
            </div>
            
            <div class="space-y-6 py-4">
                <div class="space-y-3">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Y1軸 設定:</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <span class="text-xs text-muted-foreground text-slate-500">最小値:</span>
                            <input v-model="localSettings.yLeftMin" class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950" placeholder="0">
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs text-muted-foreground text-slate-500">最大値:</span>
                            <input v-model="localSettings.yLeftMax" class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950" placeholder="自動">
                        </div>
                    </div>
                     <div class="space-y-1">
                        <span class="text-xs text-muted-foreground text-slate-500">間隔:</span>
                        <input v-model="localSettings.yLeftStepSize" class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950" placeholder="自動">
                    </div>
                </div>

                <div class="space-y-3 transition-opacity duration-200" :class="{ 'opacity-50': activeTab !== 'shop' }">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Y2軸 設定:</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <span class="text-xs text-muted-foreground text-slate-500">最小値:</span>
                            <input v-model="localSettings.yRightMin" :disabled="activeTab !== 'shop'" class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950 disabled:cursor-not-allowed disabled:bg-slate-50" placeholder="0">
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs text-muted-foreground text-slate-500">最大値:</span>
                            <input v-model="localSettings.yRightMax" :disabled="activeTab !== 'shop'" class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950 disabled:cursor-not-allowed disabled:bg-slate-50" placeholder="自動">
                        </div>
                    </div>
                     <div class="space-y-1">
                        <span class="text-xs text-muted-foreground text-slate-500">間隔:</span>
                        <input v-model="localSettings.yRightStepSize" :disabled="activeTab !== 'shop'" class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950 disabled:cursor-not-allowed disabled:bg-slate-50" placeholder="自動">
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2">
                <button @click="close" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 h-10 px-4 py-2">キャンセル</button>
                <button @click="apply" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors bg-slate-900 text-slate-50 hover:bg-slate-900/90 h-10 px-4 py-2">適用</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, watch } from 'vue';

const props = defineProps({
    isOpen: Boolean,
    settings: Object,
    activeTab: String
});

const emit = defineEmits(['close', 'apply']);

// Local state để edit, tránh mutate props trực tiếp
const localSettings = reactive({ ...props.settings });

// Sync khi props thay đổi
watch(() => props.settings, (newVal) => {
    Object.assign(localSettings, newVal);
}, { deep: true });

const close = () => emit('close');
const apply = () => emit('apply', localSettings);
</script>