<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Kế hoạch sản xuất</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex gap-4 mb-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Năm tài chính</label>
          <select v-model="selectedYear" class="border rounded px-3 py-2 w-40">
            <option :value="2024">2024</option>
            <option :value="2025">2025</option>
            <option :value="2026">2026</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nhà máy</label>
          <select v-model="selectedFactoryId" class="border rounded px-3 py-2 w-60">
            <option v-for="f in factories" :key="f.id" :value="f.id">{{ f.name }}</option>
          </select>
        </div>
      </div>

      <table class="min-w-full divide-y divide-gray-200 border">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tháng</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kế hoạch (Unit)</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="month in months" :key="month">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Tháng {{ month }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="number" 
                v-model="currentPlan[month].units" 
                class="border rounded px-2 py-1 w-32 text-right"
                placeholder="0">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="text" 
                v-model="currentPlan[month].notes" 
                class="border rounded px-2 py-1 w-full"
                placeholder="Nhập ghi chú...">
            </td>
          </tr>
        </tbody>
      </table>
      
      <div class="mt-6 flex justify-end">
        <button @click="save" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Lưu kế hoạch</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue';
import { mockMenuData, mockProductionPlans } from '../services/mockData';

const selectedYear = ref(2025);
const selectedFactoryId = ref('f1');
const factories = computed(() => mockMenuData.filter(m => m.type === 'factory'));

// Thứ tự tháng tài chính: 4,5,6...12,1,2,3
const months = [4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3];

// Dữ liệu hiển thị tạm thời
const currentPlan = reactive({});

// Khởi tạo dữ liệu trống
const initEmptyPlan = () => {
  months.forEach(m => {
    currentPlan[m] = { units: 0, notes: '' };
  });
};

// Load dữ liệu khi thay đổi filter
const loadData = () => {
  initEmptyPlan();
  const factoryData = mockProductionPlans[selectedFactoryId.value];
  if (factoryData && factoryData[selectedYear.value]) {
    const yearData = factoryData[selectedYear.value];
    Object.keys(yearData).forEach(m => {
      currentPlan[m] = { ...yearData[m] };
    });
  }
};

watch([selectedYear, selectedFactoryId], loadData, { immediate: true });

const save = () => {
  // Logic lưu fake vào mockData
  if (!mockProductionPlans[selectedFactoryId.value]) mockProductionPlans[selectedFactoryId.value] = {};
  if (!mockProductionPlans[selectedFactoryId.value][selectedYear.value]) mockProductionPlans[selectedFactoryId.value][selectedYear.value] = {};
  
  // Deep copy currentPlan vào mock storage
  Object.keys(currentPlan).forEach(m => {
      mockProductionPlans[selectedFactoryId.value][selectedYear.value][m] = { ...currentPlan[m] };
  });
  
  alert('Đã lưu thành công!');
};
</script>