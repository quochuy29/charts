<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
    >
        <div
            class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 animate-in fade-in zoom-in-95 duration-200"
        >
            <div
                class="flex flex-col space-y-1.5 text-center sm:text-left mb-4"
            >
                <h3 class="text-lg font-semibold leading-none tracking-tight">
                    Y-Axis Scale Settings
                </h3>
                <p class="text-sm text-muted-foreground">
                    Configure the vertical axis range. Leave empty for
                    auto-scale.
                </p>
            </div>

            <div class="space-y-4 py-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium">Minimum Value</label>
                    <input
                        type="number"
                        v-model.number="localMin"
                        class="flex h-10 w-full rounded-md border border-input px-3"
                        placeholder="Auto"
                    />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">Maximum Value</label>
                    <input
                        type="number"
                        v-model.number="localMax"
                        class="flex h-10 w-full rounded-md border border-input px-3"
                        placeholder="Auto"
                    />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">Interval (Step)</label>
                    <input
                        type="number"
                        v-model.number="localInterval"
                        class="flex h-10 w-full rounded-md border border-input px-3"
                        placeholder="Auto"
                    />
                </div>
            </div>

            <div
                class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 gap-2"
            >
                <button @click="handleReset" class="btn-outline...">
                    Reset
                </button>
                <button @click="handleApply" class="btn-primary...">
                    Apply Settings
                </button>
            </div>
        </div>
        <div
            class="absolute inset-0 -z-10"
            @click="$emit('update:open', false)"
        ></div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { useToast } from "../../../src/composables/useToast.js";

const props = defineProps(["open", "currentConfig"]);
const emit = defineEmits(["update:open", "apply-settings"]);

const { toast } = useToast();

const localMin = ref(null);
const localMax = ref(null);
const localInterval = ref(null);

// Sync prop vào local state khi mở dialog
watch(
    () => props.open,
    (newVal) => {
        if (newVal) {
            localMin.value = props.currentConfig?.min ?? null;
            localMax.value = props.currentConfig?.max ?? null;
            localInterval.value = props.currentConfig?.interval ?? null;
        }
    }
);

const handleApply = () => {
    emit("apply-settings", {
        min: localMin.value === "" ? null : localMin.value,
        max: localMax.value === "" ? null : localMax.value,
        interval: localInterval.value === "" ? null : localInterval.value,
    });

    toast({ title: "Settings Applied", description: "Chart Y-axis updated." });
    emit("update:open", false);
};

const handleReset = () => {
    localMin.value = null;
    localMax.value = null;
    localInterval.value = null;
    handleApply(); // Reset luôn
};
</script>
