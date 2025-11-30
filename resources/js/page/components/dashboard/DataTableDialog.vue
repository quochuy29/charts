<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity"
    >
        <div
            class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col m-4 animate-in fade-in zoom-in-95 duration-200 overflow-hidden"
        >
            <div class="flex flex-col gap-4 p-6 border-b border-gray-100">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">
                            Data Table - {{ selectedNode.name }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Detailed numerical data for
                            <span class="font-medium text-gray-700">{{
                                viewModeLabel
                            }}</span>
                        </p>
                    </div>

                    <button
                        @click="$emit('update:open', false)"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1 rounded-full transition-colors"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div class="flex justify-end">
                    <button
                        @click="handleExportCSV"
                        class="flex items-center gap-2 px-4 py-2 bg-[hsl(195,85%,45%)] text-white text-sm font-semibold rounded-md shadow-sm hover:bg-[hsl(195,85%,40%)] transition-all active:scale-95"
                    >
                        <Download class="h-4 w-4" />
                        Export CSV
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-auto bg-white">
                <table class="w-full text-sm text-left">
                    <thead
                        class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10"
                    >
                        <tr>
                            <th
                                class="py-3 px-6 font-semibold text-gray-600 w-1/4"
                            >
                                {{ columnHeaders.col1 }}
                            </th>
                            <th
                                class="py-3 px-6 font-semibold text-gray-600 text-right"
                            >
                                {{ columnHeaders.col2 }}
                            </th>
                            <th
                                class="py-3 px-6 font-semibold text-gray-600 text-right"
                            >
                                {{ columnHeaders.col3 }}
                            </th>
                            <th
                                class="py-3 px-6 font-semibold text-gray-600 text-right"
                            >
                                Variance
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="(row, idx) in tableData"
                            :key="idx"
                            class="hover:bg-gray-50/80 transition-colors"
                        >
                            <td class="py-4 px-6 font-medium text-gray-900">
                                {{ row.label }}
                            </td>
                            <td
                                class="py-4 px-6 text-right text-gray-700 font-mono"
                            >
                                {{ formatNumber(row.value1) }}
                            </td>
                            <td
                                class="py-4 px-6 text-right text-gray-700 font-mono"
                            >
                                {{ formatNumber(row.value2) }}
                            </td>
                            <td
                                class="py-4 px-6 text-right font-bold"
                                :class="getVarianceClass(row)"
                            >
                                {{ calculateVariance(row) }}%
                            </td>
                        </tr>
                        <tr v-if="tableData.length === 0">
                            <td
                                colspan="4"
                                class="py-8 text-center text-gray-500"
                            >
                                No data available for the current selection.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 text-center"
            >
                Showing {{ tableData.length }} records based on current
                selection.
            </div>
        </div>

        <div
            class="absolute inset-0 -z-10"
            @click="$emit('update:open', false)"
        ></div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { Download, X } from "lucide-vue-next";
// QUAN TRỌNG: Import hàm logic chung từ mockData
import { generateChartDataByNode } from "../../../src/data/mock.js";

// Nhận props
const props = defineProps({
    open: Boolean,
    viewMode: String,
    unitType: String,
    selectedNode: Object,
    selectedDate: Date,
});

const emit = defineEmits(["update:open"]);

// Helper: Label đơn vị
const getUnitLabel = computed(() => {
    switch (props.unitType) {
        case "energy":
            return "kWh";
        case "cost":
            return "USD";
        case "co2":
            return "kg CO₂";
        default:
            return "";
    }
});

// Helper: Tên chế độ xem
const viewModeLabel = computed(() => {
    const map = {
        daily: "daily",
        period: "period",
        comparison: "comparison",
        "shop-comparison": "shop comparison",
    };
    return map[props.viewMode] || "current";
});

// Logic Tiêu đề Cột (Dynamic Headers)
const columnHeaders = computed(() => {
    const unit = `(${getUnitLabel.value})`;

    if (props.viewMode === "comparison") {
        return {
            col1: "Time",
            col2: `Period 1 ${unit}`,
            col3: `Period 2 ${unit}`,
        };
    }
    if (props.viewMode === "shop-comparison") {
        return {
            col1: "Shop / Item",
            col2: `Actual ${unit}`,
            col3: `Target ${unit}`,
        };
    }
    // Daily & Period
    return {
        col1: "Time",
        col2: `Consumption ${unit}`,
        col3: `Target ${unit}`,
    };
});

// Format số đẹp
const formatNumber = (num) => {
    if (num === null || num === undefined) return "-";
    return parseFloat(num).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

// --- LOGIC DỮ LIỆU ĐỒNG BỘ VỚI BIỂU ĐỒ ---
const tableData = computed(() => {
    const nodeId = props.selectedNode?.id || "factory-1";

    // Gọi hàm generate giống hệt ChartArea
    const data = generateChartDataByNode(
        nodeId,
        props.viewMode,
        props.selectedDate,
        props.unitType
    );

    // Nếu không có labels, trả về rỗng
    if (!data.labels || data.labels.length === 0 || data.labels[0] === "N/A")
        return [];

    return data.labels.map((label, index) => {
        let value1 = 0;
        let value2 = 0;

        if (props.viewMode === "comparison") {
            value1 = data.period1Data?.[index] || 0;
            value2 = data.period2Data?.[index] || 0;
        } else if (props.viewMode === "shop-comparison") {
            value1 = data.actualData?.[index] || 0;
            value2 = data.targetData?.[index] || 0;
        } else {
            // Daily & Period
            value1 = data.nodeConsumptionData?.[index] || 0;
            value2 = data.factoryTotalData?.[index] || 0;
        }

        return {
            label: label,
            value1: value1,
            value2: value2,
        };
    });
});

// Tính chênh lệch %
const calculateVariance = (row) => {
    const val1 = parseFloat(row.value1);
    const val2 = parseFloat(row.value2);
    if (val2 === 0) return "0.0";
    return (((val1 - val2) / val2) * 100).toFixed(1);
};

// Màu sắc Variance
const getVarianceClass = (row) => {
    const variance = parseFloat(calculateVariance(row));
    // Nếu là comparison: chênh lệch dương có thể là tốt hoặc xấu tùy ngữ cảnh,
    // nhưng thường vượt target/period cũ là cảnh báo (Đỏ)
    return variance > 0 ? "text-red-600" : "text-emerald-600";
};

// Chức năng Export CSV
const handleExportCSV = () => {
    // 1. Tạo Header từ dynamic headers
    const headers = [
        columnHeaders.value.col1,
        columnHeaders.value.col2,
        columnHeaders.value.col3,
        "Variance (%)",
    ];

    // 2. Map dữ liệu
    const rows = tableData.value.map(
        (row) =>
            `${row.label},${row.value1},${row.value2},${calculateVariance(row)}`
    );

    // 3. Ghép chuỗi CSV
    const csvContent = [headers.join(","), ...rows].join("\n");

    // 4. Tạo Blob và Download
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");

    const dateStr = props.selectedDate
        ? props.selectedDate.toISOString().split("T")[0]
        : "date";
    const nodeName = props.selectedNode?.name.replace(/\s+/g, "_") || "Node";

    link.setAttribute("href", url);
    link.setAttribute(
        "download",
        `Data_${nodeName}_${props.viewMode}_${dateStr}.csv`
    );
    link.style.visibility = "hidden";

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
};
</script>
