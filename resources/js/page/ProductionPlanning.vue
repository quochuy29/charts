<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">生産計画設定</h1>
    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
      <div class="flex flex-wrap items-center gap-6 mb-6">
        <div>
          <div class="relative">
            <span class="text-sm font-medium">
              会計年度を選択:
            </span>
          </div>
        </div>

        <div>
          <div class="relative">
            <select v-model="selectedYear" class="appearance-none border border-gray-300 rounded-lg px-4 py-2 w-40 font-medium text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
              <option :value="2024">2024年度</option>
              <option :value="2025">2025年度</option>
              <option :value="2026">2026年度</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </div>
          </div>
        </div>

        <div>
          <div class="relative">
            <select v-model="selectedFactoryId" class="appearance-none border border-gray-300 rounded-lg px-4 py-2 w-32 font-medium text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
              <option v-for="f in factories" :key="f.id" :value="f.id">{{ f.name }}</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </div>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <p class="text-sm font-medium">生産台数を入力してください (台):</p>
      </div>

      <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-32">月</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-48">計画台数 (台)</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">備考</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="month in months" :key="month" class="group hover:bg-gray-50 transition-colors">
              <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-gray-700 group-hover:bg-gray-50">{{ month }}月</td>
              <td class="px-6 py-3 whitespace-nowrap">
                <input type="number" 
                  v-model="currentPlan[month].units" 
                  class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm w-[150px]">
              </td>
              <td class="px-6 py-3 whitespace-nowrap">
                <input type="text" 
                  v-model="currentPlan[month].notes" 
                  class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                  placeholder="備考を入力...">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div class="mt-6 flex justify-end">
        <button @click="save" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700 shadow-sm transition-all active:scale-95 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          計画保存
        </button>
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

const months = [4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3];
const currentPlan = reactive({});

const initEmptyPlan = () => {
  months.forEach(m => {
    currentPlan[m] = { units: '', notes: '' };
  });
};

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
  if (!mockProductionPlans[selectedFactoryId.value]) mockProductionPlans[selectedFactoryId.value] = {};
  if (!mockProductionPlans[selectedFactoryId.value][selectedYear.value]) mockProductionPlans[selectedFactoryId.value][selectedYear.value] = {};
  
  Object.keys(currentPlan).forEach(m => {
      mockProductionPlans[selectedFactoryId.value][selectedYear.value][m] = { ...currentPlan[m] };
  });
  
  alert('保存しました！');
};
</script>