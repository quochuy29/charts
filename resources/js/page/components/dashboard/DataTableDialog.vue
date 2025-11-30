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
                        :disabled="loading || tableData.length === 0"
                        class="flex items-center gap-2 px-4 py-2 bg-[hsl(195,85%,45%)] text-white text-sm font-semibold rounded-md shadow-sm hover:bg-[hsl(195,85%,40%)] transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Download class="h-4 w-4" />
                        Export CSV
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-auto bg-white relative">
                <div
                    v-if="loading"
                    class="absolute inset-0 flex items-center justify-center bg-white/80 z-10"
                >
                    <span
                        class="text-gray-500 font-medium flex items-center gap-2"
                    >
                        Loading data...
                    </span>
                </div>

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
                        <tr v-if="!loading && tableData.length === 0">
                            <td
                                colspan="4"
                                class="py-12 text-center text-gray-500"
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
import { ref, computed, watch } from "vue";
import axios from "axios";
import { Download, X } from "lucide-vue-next";

// --- PROPS ---
const props = defineProps({
    open: Boolean,
    viewMode: String,
    unitType: String,
    selectedNode: Object,
    selectedDate: Date,
});

const emit = defineEmits(["update:open"]);

// --- STATE ---
const tableData = ref([]);
const loading = ref(false);

// --- HELPERS ---
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

const viewModeLabel = computed(() => {
    return props.viewMode?.replace("-", " ") || "current";
});

// Tiêu đề cột động
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
    return {
        col1: "Time",
        col2: `Consumption ${unit}`,
        col3: `Target ${unit}`,
    };
});

// Format số
const formatNumber = (num) => {
    if (num === null || num === undefined) return "-";
    return parseFloat(num).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatDateLocal = (date) => {
    if (!date) return null;
    const offset = date.getTimezoneOffset();
    const localDate = new Date(date.getTime() - (offset * 60 * 1000));
    return localDate.toISOString().split('T')[0];
};

// --- API FETCH LOGIC ---
const fetchTableData = async () => {
    if (!props.selectedNode) return;

    loading.value = true;
    try {
        // Gọi API giống ChartArea
        const response = await axios.get("/api/chart-data", {
            params: {
                node_id: props.selectedNode.id,
                view_mode: props.viewMode,
                date: formatDateLocal(props.selectedDate),
                unit_type: props.unitType,
            },
        });

        const apiData = response.data;

        // --- MAP DỮ LIỆU API SANG DẠNG BẢNG ---
        // API trả về: { labels: [], nodeConsumptionData: [], factoryTotalData: [], ... }
        if (apiData && apiData.labels && apiData.labels.length > 0) {
            tableData.value = apiData.labels.map((label, index) => {
                let val1 = 0;
                let val2 = 0;

                // Lấy đúng trường dữ liệu tùy theo ViewMode
                if (props.viewMode === "comparison") {
                    val1 = apiData.period1Data?.[index] ?? 0;
                    val2 = apiData.period2Data?.[index] ?? 0;
                } else if (props.viewMode === "shop-comparison") {
                    val1 = apiData.actualData?.[index] ?? 0;
                    val2 = apiData.targetData?.[index] ?? 0;
                } else {
                    // Daily & Period
                    val1 = apiData.nodeConsumptionData?.[index] ?? 0;
                    val2 = apiData.factoryTotalData?.[index] ?? 0;
                }

                return {
                    label: label,
                    value1: val1,
                    value2: val2,
                };
            });
        } else {
            tableData.value = [];
        }
    } catch (e) {
        console.error("Failed to fetch table data:", e);
        tableData.value = [];
    } finally {
        loading.value = false;
    }
};

// Gọi API khi mở Modal hoặc thay đổi Props
watch(
    () => [props.open, props.viewMode, props.unitType, props.selectedDate],
    ([isOpen]) => {
        if (isOpen) {
            fetchTableData();
        }
    },
    { immediate: true }
);

// --- CALCULATIONS ---
const calculateVariance = (row) => {
    const val1 = parseFloat(row.value1);
    const val2 = parseFloat(row.value2);
    if (val2 === 0) return "0.0";
    return (((val1 - val2) / val2) * 100).toFixed(1);
};

const getVarianceClass = (row) => {
    const variance = parseFloat(calculateVariance(row));
    return variance > 0 ? "text-red-600" : "text-emerald-600";
};

// --- EXPORT CSV ---
const handleExportCSV = () => {
    const headers = [
        columnHeaders.value.col1,
        columnHeaders.value.col2,
        columnHeaders.value.col3,
        "Variance (%)",
    ];
    const rows = tableData.value.map(
        (row) =>
            `${row.label},${row.value1},${row.value2},${calculateVariance(row)}`
    );

    const csvContent = "\uFEFF" + [headers.join(","), ...rows].join("\n"); // BOM for UTF-8
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
