<template>
    <div class="max-w-3xl mx-auto">
        <div class="bg-white p-8 rounded-xl border border-gray-200 shadow-sm">
            <div
                class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-100"
            >
                <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                    <RefreshCw class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">
                        データ再計算
                    </h3>
                    <p class="text-sm text-gray-500">
                        指定期間のデータを再集計します。処理には時間がかかる場合があります。
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700"
                        >開始日時 (From)</label
                    >
                    <input
                        type="datetime-local"
                        v-model="form.from"
                        class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[hsl(195,85%,45%)]"
                    />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700"
                        >終了日時 (To)</label
                    >
                    <input
                        type="datetime-local"
                        v-model="form.to"
                        class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[hsl(195,85%,45%)]"
                    />
                </div>
            </div>

            <div class="flex justify-center mb-6">
                <button
                    @click="handleRecalculate"
                    :disabled="isRecalculating"
                    class="flex items-center gap-2 px-8 py-3 bg-[hsl(195,85%,45%)] text-white text-base font-semibold rounded-md shadow-md hover:bg-[hsl(195,85%,40%)] transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <Play class="h-4 w-4" v-if="!isRecalculating" />
                    <Loader2 class="h-4 w-4 animate-spin" v-else />
                    {{ isRecalculating ? "処理中..." : "再計算を実行" }}
                </button>
            </div>

            <div
                v-if="isRecalculating || progress > 0"
                class="space-y-2 animate-in fade-in slide-in-from-bottom-4 duration-300"
            >
                <div class="flex justify-between text-sm text-gray-600">
                    <span>進捗状況</span>
                    <span class="font-mono font-medium">{{ progress }}%</span>
                </div>
                <div
                    class="h-2.5 w-full bg-gray-100 rounded-full overflow-hidden"
                >
                    <div
                        class="h-full bg-[hsl(195,85%,45%)] transition-all duration-300 ease-out"
                        :style="{ width: `${progress}%` }"
                    ></div>
                </div>
                <p
                    v-if="progress === 100"
                    class="text-center text-sm text-green-600 font-medium mt-2"
                >
                    再計算が正常に完了しました。
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import { RefreshCw, Play, Loader2 } from "lucide-vue-next";
import { useToast } from "../../../src/composables/useToast";

const { toast } = useToast();
const form = reactive({ from: "", to: "" });
const isRecalculating = ref(false);
const progress = ref(0);

const handleRecalculate = () => {
    if (!form.from || !form.to) {
        toast({
            title: "Error",
            description: "開始日時と終了日時を選択してください。",
            variant: "destructive",
        });
        return;
    }
    isRecalculating.value = true;
    progress.value = 0;

    // Giả lập tiến trình chạy
    const interval = setInterval(() => {
        progress.value += 5;
        if (progress.value >= 100) {
            clearInterval(interval);
            isRecalculating.value = false;
            toast({
                title: "完了",
                description: "データの再計算が完了しました。",
                variant: "default",
            });
        }
    }, 150);
};
</script>
