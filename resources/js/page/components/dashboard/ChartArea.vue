<template>
    <div class="bg-white p-6 rounded-lg border border-[#dce0e5] shadow-sm h-full flex flex-col">
        <div class="mb-4 shrink-0">
            <h2 class="text-2xl font-semibold">{{ selectedNode.name }}</h2>
            <p class="text-sm text-gray-500">
                {{ getDescription }}
            </p>
        </div>

        <div class="flex-1 relative min-h-0">
            <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
                <span class="text-gray-500">Loading data...</span>
            </div>

            <Line
                v-if="chartType === 'line' && chartData.labels.length > 0"
                :data="chartData"
                :options="chartOptions"
            />
            <Bar
                v-else-if="chartType === 'bar' && chartData.labels.length > 0"
                :data="chartData"
                :options="chartOptions"
            />
            <div
                v-else
                class="flex items-center justify-center h-full text-gray-500"
            >
                {{ loading ? '' : 'No data available for this selection.' }}
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-4 shrink-0 border-t pt-4 border-gray-100">
             <div class="flex items-center gap-6 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[hsl(195,85%,45%)]"></span>
                    <span class="font-medium text-[hsl(195,85%,45%)]">
                        {{ viewMode === 'shop-comparison' ? 'Actual' : 'Consumption' }}
                    </span>
                </div>
                <div v-if="showTarget" class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full border border-[hsl(35,90%,55%)] bg-white"></span>
                    <span class="font-medium text-[hsl(35,90%,55%)]">Target</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <slot name="actions"></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import axios from 'axios';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from "chart.js";
import { Line, Bar } from "vue-chartjs";

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

const props = defineProps({
    viewMode: String,
    unitType: String,
    showTarget: Boolean,
    selectedNode: Object,
    selectedDate: Date,
    yAxisConfig: Object,
});

// State
const loading = ref(false);
const rawApiData = ref([]); // Lưu dữ liệu thô từ API

const getDescription = computed(() => {
    const map = {
        daily: "Daily energy consumption",
        period: "Weekly period report",
        comparison: "Period comparison",
        "shop-comparison": "Shop-level comparison",
    };
    return map[props.viewMode] || "Energy monitoring dashboard";
});

const getUnitLabel = computed(() => {
    if (props.unitType === "cost") return "USD";
    if (props.unitType === "co2") return "kg CO₂";
    return "kWh";
});

const chartType = computed(() => {
    if (props.viewMode === "daily" || props.viewMode === "comparison") return "line";
    return "bar";
});

const formatDateLocal = (date) => {
    if (!date) return null;
    const offset = date.getTimezoneOffset();
    const localDate = new Date(date.getTime() - (offset * 60 * 1000));
    return localDate.toISOString().split('T')[0];
};

// --- FETCH DATA FROM API ---
const fetchData = async () => {
    if (!props.selectedNode) return;
    
    loading.value = true;
    try {
        // Gọi API Laravel
        const response = await axios.get('/api/chart-data', {
            params: {
                node_id: props.selectedNode.id,
                view_mode: props.viewMode,
                date: formatDateLocal(props.selectedDate),
                unit_type: props.unitType
            }
        });
        rawApiData.value = response.data;
    } catch (e) {
        console.error("API Error:", e);
        rawApiData.value = [];
    } finally {
        loading.value = false;
    }
};

// Watch các thay đổi để gọi lại API
watch(
    () => [props.selectedNode, props.viewMode, props.selectedDate, props.unitType], 
    fetchData, 
    { immediate: true, deep: true }
);

// --- CHART DATA COMPUTED ---
const chartData = computed(() => {
    if (!rawApiData.value || rawApiData.value.length === 0) {
        return { labels: [], datasets: [] };
    }

    const data = rawApiData.value;
    const labels = data.map(item => item.label);

    // 1. DAILY (Giữ nguyên)
    if (props.viewMode === 'daily') {
        const values = data.map(item => item.value);
        const targets = data.map(item => item.target);
        return {
            labels,
            datasets: [
                {
                    label: `Consumption`,
                    data: values,
                    borderColor: "hsl(195, 85%, 45%)",
                    backgroundColor: "rgba(19, 137, 187, 0.2)",
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    order: 2,
                },
                props.showTarget && {
                    label: "Target",
                    data: targets,
                    borderColor: "hsl(35, 90%, 55%)",
                    borderDash: [5, 5],
                    pointRadius: 0,
                    borderWidth: 2,
                    order: 1,
                }
            ].filter(Boolean)
        };
    }

    // 2. COMPARISON (SỬA LẠI PHẦN NÀY)
    if (props.viewMode === 'comparison') {
        // --- QUAN TRỌNG: Map đúng key từ API trả về ---
        // Backend trả về: period1Data, period2Data (như trong ảnh)
        const p1 = data.map(item => item.period1Data); 
        const p2 = data.map(item => item.period2Data);
        
        return {
            labels,
            datasets: [
                {
                    label: "Period 1", // Năm nay
                    data: p1,
                    borderColor: "hsl(195, 85%, 45%)", // Màu Xanh
                    backgroundColor: "transparent",
                    tension: 0.4,
                    pointRadius: 4,
                },
                {
                    label: "Period 2", // Năm ngoái
                    data: p2,
                    borderColor: "hsl(180, 75%, 50%)", // Màu Cyan
                    backgroundColor: "transparent",
                    tension: 0.4,
                    pointRadius: 4,
                }
            ]
        };
    }

    // 3. PERIOD (Giữ nguyên)
    if (props.viewMode === 'period') {
        const values = data.map(item => item.value);
        const targets = data.map(item => item.target);
        return {
            labels,
            datasets: [
                {
                    label: `Consumption`,
                    data: values,
                    backgroundColor: "hsl(195, 85%, 45%)",
                    order: 2,
                },
                props.showTarget && {
                    type: 'line',
                    label: "Target",
                    data: targets,
                    borderColor: "hsl(35, 90%, 55%)",
                    borderDash: [5, 5],
                    pointRadius: 0,
                    borderWidth: 2,
                    order: 1,
                }
            ].filter(Boolean)
        };
    }

    // 4. SHOP COMPARISON (Giữ nguyên)
    if (props.viewMode === 'shop-comparison') {
        let values = data.map(item => item.value);
        let targets = data.map(item => item.target);

        // Filter Y-Axis
        const { min, max } = props.yAxisConfig || {};
        if (min !== null || max !== null) {
            const lower = min ?? -Infinity;
            const upper = max ?? Infinity;
            const filterFn = (val) => (val < lower || val > upper) ? 0 : val;
            values = values.map(filterFn);
            targets = targets.map(filterFn);
        }

        return {
            labels,
            datasets: [
                {
                    label: "Actual",
                    data: values,
                    backgroundColor: "hsl(195, 85%, 45%)",
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                },
                props.showTarget && {
                    label: "Target",
                    data: targets,
                    backgroundColor: "hsl(180, 75%, 50%)",
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                }
            ].filter(Boolean)
        };
    }

    return { labels: [], datasets: [] };
});

const chartOptions = computed(() => {
    const isHorizontal = props.viewMode === "shop-comparison";
    const { min, max, interval } = props.yAxisConfig || {};

    return {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: isHorizontal ? "y" : "x",
        interaction: {
            mode: "index",
            intersect: false,
            axis: isHorizontal ? "y" : "x",
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                enabled: true,
                callbacks: {
                    label: (context) => {
                        let label = context.dataset.label || "";
                        if (label) label += ": ";
                        if (context.raw !== null) {
                            label += context.raw.toLocaleString() + " " + getUnitLabel.value;
                        }
                        return label;
                    },
                },
            },
        },
        scales: {
            x: {
                grid: { color: "#f3f4f6" },
                title: { display: isHorizontal, text: getUnitLabel.value },
                stacked: false,
                ...(isHorizontal && {
                    min: min ?? undefined,
                    max: max ?? undefined,
                    ticks: { stepSize: interval ?? undefined },
                }),
            },
            y: {
                grid: { color: "#f3f4f6" },
                title: { display: !isHorizontal, text: getUnitLabel.value },
                beginAtZero: true,
                stacked: false,
                ...(!isHorizontal && {
                    min: min ?? undefined,
                    max: max ?? undefined,
                    suggestedMax: 100,
                    ticks: { stepSize: interval ?? undefined },
                }),
            },
        },
    };
});
</script>