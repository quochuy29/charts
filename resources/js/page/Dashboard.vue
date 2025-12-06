<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">Energy Dashboard</h1>
    
    <div class="flex gap-4 mb-6">
      <button @click="period = 'day'" :class="{'bg-red-600 text-white': period==='day'}" class="px-4 py-2 border rounded">Day</button>
      <button @click="period = 'month'" :class="{'bg-red-600 text-white': period==='month'}" class="px-4 py-2 border rounded">Month</button>
      <button @click="refreshData" class="ml-auto px-4 py-2 bg-gray-200 rounded">Refresh Data</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-4">Total Consumption (kWh)</h3>
        <BarChart v-if="chartData" :data="chartData" :options="chartOptions" />
      </div>
      
       <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-4">Cost Trend (¥)</h3>
        <LineChart v-if="chartData" :data="chartData" :options="chartOptions" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { generateChartData } from '../services/mockData';

// Import các component chart có sẵn trong source charts
import BarChart from './components/charts/type/Bar.vue'; 
import LineChart from './components/charts/type/Line.vue';

const period = ref('day');
const chartData = ref(null);

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
};

const refreshData = () => {
  // Lấy dữ liệu từ hàm mock
  chartData.value = generateChartData(period.value);
};

// Theo dõi thay đổi period để cập nhật data
watch(period, () => {
  refreshData();
});

onMounted(() => {
  refreshData();
});
</script>