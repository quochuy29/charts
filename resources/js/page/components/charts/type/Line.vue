<script setup>
defineProps({
    chartId: {
        type: String,
        default: 'line-chart'
    },
    width: {
        type: Number,
        default: 400
    },
    height: {
        type: Number,
        default: 400
    },
    cssClasses: {
        default: '',
        type: String
    },
    styles: {
        type: Object,
        default: () => {}
    },
    plugins: {
        type: Object,
        default: () => {}
    },
    chartData: {
        type: Object,
        default: () => {}
    },
    chartOptions: {
        type: Object,
        default: () => {
            return {
                responsive: true,
                maintainAspectRatio: false
            }
        }
    }
})
</script>

<template>
    <LineChart
        :options="chartOptions"
        :data="chartData"
        :chart-id="chartId"
        :width="width"
        :height="height"
        :css-classes="cssClasses"
        :styles="styles"
        :plugins="plugins"
    />
</template>