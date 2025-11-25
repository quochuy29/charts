<template>
    <div>
        <div class="chart-type-selector">
            <button @click="setChartType('Bar')" :class="{ active: currentChartType === 'Bar' }">Bar</button>
            <button @click="setChartType('HorizontalBar')" :class="{ active: currentChartType === 'HorizontalBar' }">Horizontal Bar</button>
            <button @click="setChartType('Line')" :class="{ active: currentChartType === 'Line' }">Line</button>
            <button @click="setChartType('Pie')" :class="{ active: currentChartType === 'Pie' }">Pie</button>
            <button @click="setChartType('Doughnut')" :class="{ active: currentChartType === 'Doughnut' }">Doughnut</button>
        </div>
        <div class="chart-container">
            <component :is="chartComponent" :chart-data="chartData" :chart-options="chartOptions" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Bar from './type/Bar.vue';
import Line from './type/Line.vue';
import Pie from './type/Pie.vue';
import Doughnut from './type/Doughnut.vue';
import HorizontalBar from './type/HorizontalBar.vue';

const chartComponents = {
    Bar,
    Line,
    Pie,
    Doughnut,
    HorizontalBar
};

const currentChartType = ref('Line');

const chartComponent = computed(() => {
    return chartComponents[currentChartType.value];
});

function setChartType(type) {
    currentChartType.value = type;
}

const chartData = {
    labels: ['Monssssss', 'Tusses', 'Wed'],
    datasets: [{
        label: 'Sales',
        data: [12, 19, 3],
        cubicInterpolationMode: 'monotone',
        tension: 0.4
    }]
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false
};
</script>

<style lang="scss" scoped>
.chart-container {
  position: relative; /* Bắt buộc để Chart.js tính toán đúng */
  
  /* Kích thước "vừa đủ nhìn" */
  width: 100%; 
  max-width: 800px;  /* Giới hạn chiều rộng tối đa, không cho tràn màn hình to */
  height: 400px;     /* Chiều cao cố định để biểu đồ không bị bẹp hoặc quá dài */
  
  /* Căn giữa và làm đẹp */
  margin: 20px auto; /* Căn giữa màn hình */
  padding: 20px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ cho nổi bật */
}

.chart-type-selector {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
    gap: 10px;

    button {
        padding: 10px 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f0f0f0;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;

        &:hover {
            background-color: #e0e0e0;
        }

        &.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
    }
}
</style>