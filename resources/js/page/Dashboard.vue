<template>
  <div class="p-6 space-y-4 bg-gray-50 min-h-full font-sans text-gray-800">
    
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 leading-tight">ダッシュボード</h1>
        <div class="text-sm font-medium text-gray-500 mt-1">塗装工程</div>
      </div>

      <div class="flex flex-col sm:flex-row items-center gap-4">
        
        <div class="relative group w-[220px] h-[40px]">
           <div class="absolute inset-0 flex items-center border border-gray-300 rounded-md px-4 bg-white shadow-sm group-hover:border-blue-500 transition-colors pointer-events-none z-0">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 text-gray-500"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
              <span class="text-sm font-medium text-gray-700 tracking-wide truncate flex-1 text-center">
                 {{ dateDisplay }}
              </span>
              <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
           </div>

           <select 
              v-if="selectedPeriod === 'year'"
              v-model="pickerValue"
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
           >
              <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}年</option>
           </select>

           <input 
              v-else
              :type="inputType" 
              v-model="pickerValue"
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
              @click="$event.target.showPicker ? $event.target.showPicker() : null"
           />
        </div>
        <div class="flex bg-gray-200 p-1 rounded-lg">
           <button @click="activeTab = 'period'" :class="activeTab === 'period' ? 'bg-white text-gray-900 shadow font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="px-4 py-2 text-sm rounded-md transition-all">期報</button>
           <button @click="activeTab = 'comparison'" :class="activeTab === 'comparison' ? 'bg-white text-gray-900 shadow font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="px-4 py-2 text-sm rounded-md transition-all">比較</button>
           <button @click="activeTab = 'shop'" :class="activeTab === 'shop' ? 'bg-white text-gray-900 shadow font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="px-4 py-2 text-sm rounded-md transition-all">工程比較</button>
        </div>
      </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col">
          
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            
            <h3 class="font-bold text-gray-800 text-lg flex items-center shrink-0">
              <span class="w-1.5 h-6 rounded-full mr-3" :class="getUnitColor()"></span>
              {{ getChartTitle() }}
            </h3>
            
            <div class="flex flex-wrap items-center gap-4">
              <div class="relative">
                <select v-model="selectedPeriod" class="appearance-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-32 p-2.5 pr-8 font-bold cursor-pointer">
                  <option value="day">日別</option>
                  <option value="week">週別</option>
                  <option value="month">月別</option>
                  <option value="year">年別</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                  <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
              </div>

              <div class="relative">
                <select v-model="selectedUnit" class="appearance-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-36 p-2.5 pr-8 font-bold cursor-pointer">
                  <option value="kwh">kWh/m³</option>
                  <option value="cost">コスト</option>
                  <option value="co2">CO2</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                  <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
              </div>

              <label class="flex items-center gap-2 cursor-pointer select-none ml-2 border-l pl-4 border-gray-200 h-full">
                <input type="checkbox" v-model="showTarget" class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer">
                <span class="text-sm font-bold text-gray-700">目標表示</span>
              </label>
            </div>
        </div>

        <div class="h-[500px] w-full relative">
            <LineChart v-if="chartData" :data="chartData" :options="chartOptions" />
        </div>

        <div class="mt-4 flex items-center justify-center gap-8 border-t border-gray-100 pt-4">
           <div class="flex items-center gap-2">
             <span class="w-3 h-3 rounded-full" :class="getUnitColor()"></span>
             <span class="text-sm font-bold text-gray-600">実績 (Actual)</span>
           </div>
           <div v-if="showTarget" class="flex items-center gap-2">
             <span class="w-8 h-0.5 bg-blue-500 border-t-2 border-dashed border-blue-500"></span>
             <span class="text-sm font-bold text-gray-600">目標 (Target)</span>
           </div>
        </div>
    </div>

    <div class="flex justify-center gap-3">
        <button class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm transition-all active:scale-95">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
          軸設定
        </button>
        <button class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm transition-all active:scale-95">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7-4h14M4 6h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z" /></svg>
          データ表
        </button>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { generateChartData } from '../services/mockData';
import LineChart from './components/charts/type/Line.vue';
import { format, parseISO, startOfWeek } from 'date-fns';
import { ja } from 'date-fns/locale';

// --- State ---
const activeTab = ref('period');
const selectedPeriod = ref('day');
const selectedUnit = ref('kwh');
const showTarget = ref(true);
const chartData = ref(null);

// State cho Input (Picker Value)
const pickerValue = ref(format(new Date(), 'yyyy-MM-dd'));

// Danh sách năm cho dropdown Year
const yearOptions = Array.from({length: 10}, (_, i) => new Date().getFullYear() - 5 + i);

// --- 1. Xử lý Input Type động (Quan trọng) ---
const inputType = computed(() => {
    switch (selectedPeriod.value) {
        case 'day': return 'date';   
        case 'week': return 'week';  
        case 'month': return 'month';
        case 'year': return 'number';
        default: return 'date';
    }
});

// --- 2. Xử lý Hiển thị Text (Format theo yêu cầu) ---
const dateDisplay = computed(() => {
    if (!pickerValue.value) return '日付を選択';
    
    try {
        if (selectedPeriod.value === 'day') {
            return format(parseISO(pickerValue.value), 'yyyy/MM/dd', { locale: ja });
        } 
        else if (selectedPeriod.value === 'week') {
            if (pickerValue.value.includes('W')) {
                // Cách đơn giản để hiển thị (thực tế có thể dùng parseISO của date-fns)
                return pickerValue.value; 
            }
            return pickerValue.value; 
        } 
        else if (selectedPeriod.value === 'month') {
            return pickerValue.value.replace('-', '/');
        } 
        else if (selectedPeriod.value === 'year') {
            return `${pickerValue.value}年`;
        }
    } catch (e) {
        return pickerValue.value;
    }
    return pickerValue.value;
});

// --- Watcher: Reset giá trị input khi đổi loại kỳ ---
watch(selectedPeriod, (newVal) => {
    const now = new Date();
    if (newVal === 'day') pickerValue.value = format(now, 'yyyy-MM-dd');
    else if (newVal === 'week') pickerValue.value = `${format(now, 'yyyy')}-週${format(now, 'ww')}`;
    else if (newVal === 'month') pickerValue.value = format(now, 'yyyy-MM');
    else if (newVal === 'year') pickerValue.value = format(now, 'yyyy');
    
    refreshData();
});

// --- Helpers ---
const getUnitColor = () => {
  if (selectedUnit.value === 'cost') return 'bg-yellow-500';
  if (selectedUnit.value === 'co2') return 'bg-green-600';
  return 'bg-blue-600';
};

const getChartTitle = () => {
  if (selectedUnit.value === 'cost') return 'コスト推移';
  if (selectedUnit.value === 'co2') return 'CO2排出量推移';
  return '消費電力推移';
};

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
     y: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false } },
     x: { grid: { display: false }, border: { display: false } }
  },
  elements: { line: { tension: 0.3, borderWidth: 3 }, point: { radius: 0, hoverRadius: 6 } }
};

const refreshData = () => {
  chartData.value = generateChartData(selectedPeriod.value, selectedUnit.value, showTarget.value);
};

watch([selectedUnit, showTarget, activeTab], () => { refreshData(); });

onMounted(() => { refreshData(); });
</script>