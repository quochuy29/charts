<script setup>
import { computed } from 'vue'

const props = defineProps({
    chartId: {
        type: String,
        default: 'horizontal-bar-chart'
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

const options = computed(() => {
    return {
        ...props.chartOptions,
        indexAxis: 'y',
    }
})
</script>

<template>
    <BarChart
        :options="options"
        :data="chartData"
        :chart-id="chartId"
        :width="width"
        :height="height"
        :css-classes="cssClasses"
        :styles="styles"
        :plugins="plugins"
    />
</template>
