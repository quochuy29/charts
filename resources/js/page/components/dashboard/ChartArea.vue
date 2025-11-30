<template>
    <div class="bg-white p-6 rounded-lg border border-[#dce0e5] shadow-sm h-full flex flex-col">
        <div class="mb-4 shrink-0">
            <h2 class="text-2xl font-semibold">{{ selectedNode.name }}</h2>
            <p class="text-sm text-gray-500">
                {{ getDescription }}
            </p>
        </div>

        <div class="flex-1 relative min-h-0">
            <Line
                v-if="chartType === 'line'"
                :data="chartData"
                :options="chartOptions"
            />
            <Bar
                v-else-if="chartType === 'bar'"
                :data="chartData"
                :options="chartOptions"
            />
            <div
                v-else
                class="flex items-center justify-center h-full text-gray-500"
            >
                Select a valid view mode or node to see the chart.
            </div>
        </div>

        <div
            class="mt-4 flex flex-wrap items-center justify-between gap-4 shrink-0 border-t pt-4 border-gray-100"
        >
            <div class="flex items-center gap-6 text-sm">
                <template v-if="viewMode === 'daily' || viewMode === 'period'">
                    <div class="flex items-center gap-2">
                        <span class="flex items-center">
                            <span
                                class="w-2 h-2 rounded-full bg-[hsl(195,85%,45%)]"
                            ></span>
                            <span
                                class="w-3 h-0.5 bg-[hsl(195,85%,45%)] -ml-1"
                            ></span>
                            <span
                                class="w-2 h-2 rounded-full bg-[hsl(195,85%,45%)] -ml-1"
                            ></span>
                        </span>
                        <span class="font-medium text-[hsl(195,85%,45%)]"
                            >Consumption</span
                        >
                    </div>

                    <div v-if="showTarget" class="flex items-center gap-2">
                        <span class="flex items-center">
                            <span
                                class="w-2 h-2 rounded-full border border-[hsl(35,90%,55%)] bg-white"
                            ></span>
                            <span
                                class="w-3 h-0.5 border-t border-dashed border-[hsl(35,90%,55%)] -ml-1"
                            ></span>
                            <span
                                class="w-2 h-2 rounded-full border border-[hsl(35,90%,55%)] bg-white -ml-1"
                            ></span>
                        </span>
                        <span class="font-medium text-[hsl(35,90%,55%)]"
                            >Target</span
                        >
                    </div>
                </template>

                <template v-else-if="viewMode === 'comparison'">
                    <div class="flex items-center gap-2">
                        <span class="flex items-center">
                            <span
                                class="w-2 h-2 rounded-full bg-[hsl(195,85%,45%)]"
                            ></span>
                            <span
                                class="w-3 h-0.5 bg-[hsl(195,85%,45%)] -ml-1"
                            ></span>
                            <span
                                class="w-2 h-2 rounded-full bg-[hsl(195,85%,45%)] -ml-1"
                            ></span>
                        </span>
                        <span class="font-medium text-[hsl(195,85%,45%)]"
                            >Period 1</span
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="flex items-center">
                            <span
                                class="w-2 h-2 rounded-full bg-[hsl(180,75%,50%)]"
                            ></span>
                            <span
                                class="w-3 h-0.5 bg-[hsl(180,75%,50%)] -ml-1"
                            ></span>
                            <span
                                class="w-2 h-2 rounded-full bg-[hsl(180,75%,50%)] -ml-1"
                            ></span>
                        </span>
                        <span class="font-medium text-[hsl(180,75%,50%)]"
                            >Period 2</span
                        >
                    </div>
                </template>

                <template v-else-if="viewMode === 'shop-comparison'">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-3 h-3 rounded-sm bg-[hsl(195,85%,45%)]"
                        ></span>
                        <span class="font-medium text-[hsl(195,85%,45%)]"
                            >Actual</span
                        >
                    </div>
                    <div v-if="showTarget" class="flex items-center gap-2">
                        <span
                            class="w-3 h-3 rounded-sm bg-[hsl(180,75%,50%)]"
                        ></span>
                        <span class="font-medium text-[hsl(180,75%,50%)]"
                            >Target</span
                        >
                    </div>
                </template>
            </div>

            <div class="flex items-center gap-3">
                <slot name="actions"></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
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
import { generateChartDataByNode } from "../../../src/data/mock.js";

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

const getDescription = computed(() => {
    const map = {
        daily: "Daily energy consumption (05:00 - 29:00)",
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
    if (props.viewMode === "daily" || props.viewMode === "comparison")
        return "line";
    if (props.viewMode === "period" || props.viewMode === "shop-comparison")
        return "bar";
    return "line";
});

const chartData = computed(() => {
    const nodeId = props.selectedNode?.id || "factory-1";

    // TRUYỀN UNIT TYPE VÀ DATE VÀO HÀM GENERATE
    if (props.viewMode === "daily") {
        const { labels, nodeConsumptionData, factoryTotalData } =
            generateChartDataByNode(
                nodeId,
                "daily",
                props.selectedDate,
                props.unitType
            );

        return {
            labels,
            datasets: [
                {
                    label: `Consumption`,
                    data: nodeConsumptionData,
                    borderColor: "hsl(195, 85%, 45%)",
                    backgroundColor: "rgba(19, 137, 187, 0.2)",
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    order: 2,
                },
                props.showTarget && {
                    label: "Target",
                    data: factoryTotalData,
                    borderColor: "hsl(35, 90%, 55%)",
                    borderDash: [5, 5],
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    borderWidth: 2,
                    order: 1,
                },
            ].filter(Boolean),
        };
    }

    if (props.viewMode === "comparison") {
        const { labels, period1Data, period2Data } = generateChartDataByNode(
            nodeId,
            "comparison",
            props.selectedDate,
            props.unitType
        );
        return {
            labels,
            datasets: [
                {
                    label: "Period 1",
                    data: period1Data,
                    borderColor: "hsl(195, 85%, 45%)",
                    backgroundColor: "transparent",
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: "hsl(195, 85%, 45%)",
                },
                {
                    label: "Period 2",
                    data: period2Data,
                    borderColor: "hsl(180, 75%, 50%)",
                    backgroundColor: "transparent",
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: "hsl(180, 75%, 50%)",
                },
            ],
        };
    }

    if (props.viewMode === "period") {
        const { labels, nodeConsumptionData, factoryTotalData } =
            generateChartDataByNode(
                nodeId,
                "period",
                props.selectedDate,
                props.unitType
            );
        return {
            labels,
            datasets: [
                {
                    label: `Consumption`,
                    data: nodeConsumptionData,
                    backgroundColor: "hsl(195, 85%, 45%)",
                    order: 2,
                },
                props.showTarget && {
                    type: "line",
                    label: "Target",
                    data: factoryTotalData,
                    borderColor: "hsl(35, 90%, 55%)",
                    borderDash: [5, 5],
                    pointRadius: 0,
                    borderWidth: 2,
                    order: 1,
                },
            ].filter(Boolean),
        };
    }

    // --- LOGIC XỬ LÝ ĐẶC BIỆT CHO SHOP COMPARISON ---
    if (props.viewMode === "shop-comparison") {
        const data = generateChartDataByNode(
            nodeId,
            "shop-comparison",
            props.selectedDate,
            props.unitType
        );
        if (!data.labels || data.labels.length === 0)
            return { labels: ["N/A"], datasets: [] };

        let actualData = data.actualData;
        let targetData = data.targetData;

        // ÁP DỤNG BỘ LỌC Y-AXIS (MIN/MAX) ĐỂ GÁN VỀ 0
        const { min, max } = props.yAxisConfig || {};
        if (min !== null || max !== null) {
            const lowerBound = min ?? -Infinity;
            const upperBound = max ?? Infinity;

            // Hàm kiểm tra và gán về 0 nếu vi phạm
            const filterValue = (val) =>
                val < lowerBound || val > upperBound ? 0 : val;

            actualData = actualData.map(filterValue);
            targetData = targetData.map(filterValue);
        }

        return {
            labels: data.labels,
            datasets: [
                {
                    label: "Actual",
                    data: actualData,
                    backgroundColor: "hsl(195, 85%, 45%)",
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                },
                props.showTarget && {
                    label: "Target",
                    data: targetData,
                    backgroundColor: "hsl(180, 75%, 50%)",
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                },
            ].filter(Boolean),
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
            title: { display: false },
            tooltip: {
                enabled: true,
                backgroundColor: "rgba(255, 255, 255, 0.95)",
                titleColor: "#000",
                bodyColor: "#333",
                borderColor: "#e5e7eb",
                borderWidth: 1,
                padding: 10,
                boxPadding: 4,
                usePointStyle: true,
                callbacks: {
                    label: function (context) {
                        let label = context.dataset.label || "";
                        if (label) {
                            label += ": ";
                        }
                        if (context.raw !== null && context.raw !== undefined) {
                            label +=
                                context.raw.toLocaleString() +
                                " " +
                                getUnitLabel.value;
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
                // Chỉ áp dụng Min/Max cho trục X nếu là biểu đồ ngang
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
                // Chỉ áp dụng Min/Max cho trục Y nếu là biểu đồ dọc
                ...(!isHorizontal && {
                    min: min ?? undefined,
                    max: max ?? undefined,
                    ticks: { stepSize: interval ?? undefined },
                }),
            },
        },
    };
});
</script>
