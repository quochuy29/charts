<template>
    <div
        class="bg-white rounded-lg border border-gray-200 shadow-sm h-full flex flex-col overflow-hidden"
    >
        <div
            class="p-6 border-b border-gray-100 flex justify-between items-center"
        >
            <div>
                <h2 class="text-2xl font-bold text-gray-900">構成ツリータグ</h2>
                <p class="text-sm text-gray-500 mt-1">
                    階層ルール: Factory > Line > Shop > Equipment。<br />
                    ドラッグ＆ドロップは同じ階層レベル間でのみ可能です。
                </p>
            </div>

            <button
                @click="saveChanges"
                class="flex items-center gap-2 px-4 py-2 bg-[hsl(195,85%,45%)] text-white text-sm font-semibold rounded-md shadow-sm hover:bg-[hsl(195,85%,40%)] transition-all active:scale-95"
            >
                <Save class="h-4 w-4" />
                変更を保存
            </button>
        </div>

        <div class="flex-1 overflow-auto p-6 bg-gray-50/50">
            <div
                class="max-w-4xl mx-auto bg-white p-6 rounded-xl border border-gray-200 shadow-sm min-h-[500px]"
            >
                <ConfigTreeItem :list="treeData" parentType="root" />

                <div
                    v-if="treeData.length === 0"
                    class="flex flex-col items-center justify-center h-64 text-gray-400"
                >
                    <p>データがありません。</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Save } from "lucide-vue-next";
import ConfigTreeItem from "./ConfigTreeItem.vue";
import { initialTreeData } from "../../../src/data/mock.js";
import { useToast } from "../../../src/composables/useToast.js";

const { toast } = useToast();
const treeData = ref(JSON.parse(JSON.stringify(initialTreeData)));

const saveChanges = () => {
    console.log("Saving Tree Data:", JSON.stringify(treeData.value, null, 2));
    toast({
        title: "保存完了",
        description: "ツリー構成が正常に更新されました。",
        variant: "default",
    });
};
</script>
