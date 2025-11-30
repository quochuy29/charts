<template>
    <div
        class="fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]"
    >
        <transition-group name="toast">
            <div
                v-for="t in toasts"
                :key="t.id"
                class="group pointer-events-auto relative flex w-full items-center justify-between space-x-4 overflow-hidden rounded-md border p-6 pr-8 shadow-lg transition-all bg-white border-gray-200 mb-2"
            >
                <div class="grid gap-1">
                    <div v-if="t.title" class="text-sm font-semibold">
                        {{ t.title }}
                    </div>
                    <div v-if="t.description" class="text-sm opacity-90">
                        {{ t.description }}
                    </div>
                </div>
                <button
                    @click="dismiss(t.id)"
                    class="absolute right-2 top-2 rounded-md p-1 text-foreground/50 opacity-0 transition-opacity group-hover:opacity-100 hover:text-foreground focus:opacity-100 focus:outline-none focus:ring-2 group-hover:text-black"
                >
                    ✕
                </button>
            </div>
        </transition-group>
    </div>
</template>

<script setup>
import { useToast } from "../../../src/composables/useToast.js";
const { toasts, dismiss } = useToast();
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
</style>
