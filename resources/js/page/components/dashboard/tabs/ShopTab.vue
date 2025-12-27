<template>
    <LineChart v-if="isLevel4" :data="chartData" :options="lineChartOptions" class="w-full h-full" />

    <BarChart v-else :data="chartData" :options="barChartOptions" class="w-full h-full" />
</template>

<script setup>
import { computed } from 'vue';
import BarChart from '../../charts/type/Bar.vue';
import LineChart from '../../charts/type/Line.vue'; // Import thêm LineChart

const props = defineProps({
    chartData: Object,
    axisSettings: Object,
    unit: String,
    shopDisplayType: String,
    isLevel4: Boolean // Nhận prop mới
});

// Options cho Bar Chart (Logic cũ - Stacked)
const barChartOptions = computed(() => {
    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                titleColor: '#1e293b', bodyColor: '#475569', borderColor: '#e2e8f0', borderWidth: 1, padding: 10
            }
        },
        scales: {
            x: { stacked: true, grid: { display: false }, ticks: { font: { size: 11 } } },
            y: {
                stacked: true, beginAtZero: true, position: 'left',
                title: { display: true, text: props.unit, font: { weight: 'bold' } },
                min: props.axisSettings.yLeftMin ? Number(props.axisSettings.yLeftMin) : undefined,
                max: props.axisSettings.yLeftMax ? Number(props.axisSettings.yLeftMax) : undefined,
                ticks: { stepSize: props.axisSettings.yLeftStepSize ? Number(props.axisSettings.yLeftStepSize) : undefined },
                grid: { color: '#f1f5f9' }
            },
            y1: {
                display: !props.shopDisplayType.includes('per_unit'), position: 'right', beginAtZero: true,
                grid: { drawOnChartArea: false },
                title: { display: true, text: '生産台数 (台)', font: { weight: 'bold' } },
                min: props.axisSettings.yRightMin ? Number(props.axisSettings.yRightMin) : undefined,
                max: props.axisSettings.yRightMax ? Number(props.axisSettings.yRightMax) : undefined,
                ticks: { stepSize: props.axisSettings.yRightStepSize ? Number(props.axisSettings.yRightStepSize) : undefined }
            }
        }
    };
});

// Options cho Line Chart (Logic mới - Level 4)
const lineChartOptions = computed(() => {
    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                titleColor: '#1e293b', bodyColor: '#475569', borderColor: '#e2e8f0', borderWidth: 1, padding: 10,
                callbacks: {
                    label: function (context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
                        if (context.parsed.y !== null) label += Math.round(context.parsed.y).toLocaleString();
                        return label;
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    font: { size: 11 },
                    autoSkip: false,
                    maxRotation: 0,
                    // Xử lý hiển thị 168 điểm cho Week (giống Period Tab)
                    callback: function (val, index) {
                        // Kiểm tra nếu dữ liệu là 168 điểm (tuần) thì chỉ hiện nhãn ngày
                        if (props.chartData.labels && props.chartData.labels.length === 168) {
                            return index % 24 === 0 ? this.getLabelForValue(val) : '';
                        }
                        return this.getLabelForValue(val);
                    }
                }
            },
            y: {
                beginAtZero: true, position: 'left',
                title: { display: true, text: props.unit, font: { weight: 'bold' } },
                min: props.axisSettings.yLeftMin ? Number(props.axisSettings.yLeftMin) : undefined,
                max: props.axisSettings.yLeftMax ? Number(props.axisSettings.yLeftMax) : undefined,
                ticks: { stepSize: props.axisSettings.yLeftStepSize ? Number(props.axisSettings.yLeftStepSize) : undefined },
                grid: { color: '#f1f5f9' }
            },
            y1: {
                display: !props.shopDisplayType.includes('per_unit'), position: 'right', beginAtZero: true,
                grid: { drawOnChartArea: false },
                title: { display: true, text: '生産台数 (台)', font: { weight: 'bold' } },
                min: props.axisSettings.yRightMin ? Number(props.axisSettings.yRightMin) : undefined,
                max: props.axisSettings.yRightMax ? Number(props.axisSettings.yRightMax) : undefined,
                ticks: { stepSize: props.axisSettings.yRightStepSize ? Number(props.axisSettings.yRightStepSize) : undefined }
            }
        }
    };
});
</script>