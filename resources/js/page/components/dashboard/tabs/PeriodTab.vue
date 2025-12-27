<template>
    <LineChart :data="chartData" :options="chartOptions" class="w-full h-full"/>
</template>

<script setup>
import { computed } from 'vue';
import LineChart from '../../charts/type/Line.vue';

const props = defineProps({
    chartData: Object,
    axisSettings: Object,
    unit: String,
    periodType: String
});

const chartOptions = computed(() => {
    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { 
                position: 'bottom',
                labels: { usePointStyle: true, boxWidth: 8 }
            },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                titleColor: '#1e293b', bodyColor: '#475569', borderColor: '#e2e8f0',
                borderWidth: 1, padding: 10,
                callbacks: {
                    label: function(context) {
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
                    callback: function(val, index) {
                        // Logic hiển thị nhãn trục X cho Week (168 điểm)
                        if (props.periodType === 'week') {
                            return index % 24 === 0 ? this.getLabelForValue(val) : '';
                        }
                        return this.getLabelForValue(val);
                    }
                }
            },
            y: {
                beginAtZero: true,
                title: { display: true, text: props.unit, font: { weight: 'bold' } },
                min: props.axisSettings.yLeftMin ? Number(props.axisSettings.yLeftMin) : undefined,
                max: props.axisSettings.yLeftMax ? Number(props.axisSettings.yLeftMax) : undefined,
                ticks: { stepSize: props.axisSettings.yLeftStepSize ? Number(props.axisSettings.yLeftStepSize) : undefined },
                grid: { color: '#f1f5f9' }
            }
        }
    };
});
</script>