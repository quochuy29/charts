<template>
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3
                    class="text-lg font-semibold text-gray-800 flex items-center gap-2"
                >
                    <Calculator class="h-5 w-5 text-[hsl(195,85%,45%)]" />
                    演算タグ CSV アップロード
                </h3>
                <button
                    @click="downloadTemplate"
                    class="text-sm text-[hsl(195,85%,45%)] hover:underline flex items-center gap-1 font-medium hover:text-[hsl(195,85%,40%)] transition-colors"
                >
                    <Download class="h-4 w-4" /> テンプレートをダウンロード
                </button>
            </div>

            <div class="flex items-center gap-3">
                <input
                    type="file"
                    accept=".csv"
                    ref="fileInput"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[hsl(195,85%,45%)] hover:file:bg-blue-100 border border-gray-300 rounded-md cursor-pointer bg-white h-10 pt-1.5 pl-2"
                />
                <button
                    @click="handleUpload"
                    class="flex items-center gap-2 px-4 py-2 bg-[hsl(195,85%,45%)] text-white text-sm font-semibold rounded-md shadow-sm hover:bg-[hsl(195,85%,40%)] transition-all active:scale-95 whitespace-nowrap"
                >
                    <UploadCloud class="h-4 w-4" />
                    アップロード
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-2">
                ※ CSVファイルを選択してください (最大 5MB)
            </p>
        </div>

        <div
            class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden"
        >
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h4 class="font-semibold text-gray-700">アップロード履歴</h4>
            </div>
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-6 font-semibold text-gray-600 w-48">
                            日時
                        </th>
                        <th class="py-3 px-6 font-semibold text-gray-600">
                            実行者
                        </th>
                        <th class="py-3 px-6 font-semibold text-gray-600">
                            ファイル名
                        </th>
                        <th
                            class="py-3 px-6 font-semibold text-gray-600 text-center w-32"
                        >
                            ステータス
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="(log, idx) in logs"
                        :key="idx"
                        class="hover:bg-gray-50/80"
                    >
                        <td class="py-3 px-6 text-gray-500">{{ log.date }}</td>
                        <td class="py-3 px-6 text-gray-900 font-medium">
                            {{ log.user }}
                        </td>
                        <td
                            class="py-3 px-6 text-gray-600 flex items-center gap-2"
                        >
                            <FileSpreadsheet class="h-4 w-4 text-green-600" />
                            {{ log.file }}
                        </td>
                        <td class="py-3 px-6 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="
                                    log.status === 'Success'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                "
                            >
                                {{ log.status === "Success" ? "完了" : "失敗" }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import {
    Calculator,
    Download,
    UploadCloud,
    FileSpreadsheet,
} from "lucide-vue-next";
import { useToast } from "../../../src/composables/useToast";

const { toast } = useToast();
const fileInput = ref(null);

const logs = ref([
    {
        date: "2025/11/29 10:30",
        user: "admin_01",
        file: "calc_formulas_v2.csv",
        status: "Success",
    },
    {
        date: "2025/11/28 15:45",
        user: "manager_opt",
        file: "calc_formulas_v1.csv",
        status: "Error",
    },
]);

// Hàm Download Template
const downloadTemplate = () => {
    // Định nghĩa Header theo yêu cầu
    const headers = ["エネルギー種別", "値", "有効日"];

    // Tạo nội dung CSV, thêm BOM (\uFEFF) để Excel hiển thị đúng tiếng Nhật
    const csvContent = "\uFEFF" + headers.join(",") + "\n";

    // Tạo Blob và link download
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", "template_calc_tag.csv"); // Tên file
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url); // Dọn dẹp memory
};

const handleUpload = () => {
    if (
        !fileInput.value ||
        !fileInput.value.files ||
        fileInput.value.files.length === 0
    ) {
        toast({
            title: "エラー",
            description: "ファイルを選択してください。",
            variant: "destructive",
        });
        return;
    }
    const file = fileInput.value.files[0];
    toast({
        title: "アップロード完了",
        description: `${file.name} が正常にアップロードされました。`,
        variant: "default",
    });
    fileInput.value.value = "";
};
</script>
