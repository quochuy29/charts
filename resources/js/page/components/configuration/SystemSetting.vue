<template>
    <div
        class="bg-white rounded-lg border border-gray-200 shadow-sm h-full flex flex-col overflow-hidden"
    >
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900">システム設定</h2>
            <p class="text-sm text-gray-500 mt-1">
                計算式、パラメータ、およびデータの再計算を管理します。
            </p>
        </div>

        <div class="border-b border-gray-200 bg-gray-50/50 px-6">
            <div class="flex gap-6">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="currentTab = tab.id"
                    class="relative py-4 text-sm font-medium transition-colors focus:outline-none"
                    :class="
                        currentTab === tab.id
                            ? 'text-[hsl(195,85%,45%)]'
                            : 'text-gray-500 hover:text-gray-700'
                    "
                >
                    {{ tab.label }}
                    <span
                        v-if="currentTab === tab.id"
                        class="absolute bottom-0 left-0 w-full h-0.5 bg-[hsl(195,85%,45%)]"
                    ></span>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-auto p-6 bg-gray-50/30">
            <CalcTagSetting v-if="currentTab === 'calc-tag'" />

            <ParameterSetting v-else-if="currentTab === 'parameter'" />

            <DataRecovery v-else-if="currentTab === 'recovery'" />
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import CalcTagSetting from "./CalcTagSetting.vue";
import ParameterSetting from "./ParameterSetting.vue";
import DataRecovery from "./DataRecovery.vue";

const currentTab = ref("calc-tag");
const tabs = [
    { id: "calc-tag", label: "演算タグ設定" },
    { id: "parameter", label: "パラメータ設定" },
    { id: "recovery", label: "データ再計算" },
];
</script>
