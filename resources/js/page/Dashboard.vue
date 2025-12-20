<template>
    <div class="p-6 space-y-4 bg-gray-50 min-h-screen font-sans text-gray-800">

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 leading-tight">グラフ表示 (Biểu đồ)</h1>
                <div class="text-sm font-medium text-gray-500 mt-1 flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">Process</span>
                    {{ processName }}
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col overflow-visible z-10 relative">
            <div class="p-4 md:p-6">
                <div class="space-y-4">
                    <div class="grid w-full grid-cols-3 h-10 items-center justify-center rounded-md bg-slate-100 p-1 text-slate-500">
                        <button v-for="tab in tabs" :key="tab.value" @click="activeTab = tab.value"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-white transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                            :class="activeTab === tab.value ? 'bg-white text-slate-950 shadow-sm' : 'hover:text-slate-900'">
                            {{ tab.label }}
                        </button>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button v-for="p in periods" :key="p.value" @click="currentPeriodType = p.value"
                                    class="px-3 py-1.5 text-xs font-bold rounded-md transition-all"
                                    :class="currentPeriodType === p.value ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-900'">
                                    {{ p.label }}
                                </button>
                            </div>

                            <div class="relative group w-[160px]">
                                <div class="flex items-center border border-gray-300 rounded-md px-3 py-1.5 bg-white shadow-sm h-[36px] hover:bg-gray-50 transition-colors cursor-pointer">
                                    <component :is="CalendarIcon" class="w-4 h-4 text-gray-500 mr-2" />
                                    <span class="text-sm font-medium text-gray-700 truncate flex-1">
                                        {{ getDateDisplay(currentDate, currentPeriodType) }}
                                    </span>
                                </div>
                                <input :type="getInputType(currentPeriodType)" :value="pickerValue"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    @input="updateDate($event.target.value, 'date')"
                                    @click="$event.target.showPicker && $event.target.showPicker()" />
                            </div>

                            <div v-if="activeTab !== 'comparison'" class="relative group w-[160px]">
                                <div class="flex items-center border border-gray-300 rounded-md px-3 py-1.5 bg-white shadow-sm h-[36px] hover:bg-gray-50 transition-colors cursor-pointer"
                                    :class="compareDate ? 'text-gray-700' : 'text-gray-400 border-dashed'">
                                    <component :is="CalendarIcon" class="w-4 h-4 mr-2" :class="compareDate ? 'text-gray-500' : 'text-gray-400'" />
                                    <span class="text-sm font-medium truncate flex-1">
                                        {{ compareDate ? getDateDisplay(compareDate, currentPeriodType) : '比較期間を選択' }}
                                    </span>
                                    <button v-if="compareDate" @click.stop="clearCompare"
                                        class="ml-1 p-0.5 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-600 relative z-20">
                                        <component :is="XIcon" class="w-3 h-3" />
                                    </button>
                                </div>
                                <input :type="getInputType(currentPeriodType)" :value="comparePickerValue"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    @input="updateDate($event.target.value, 'compareDate')"
                                    @click="$event.target.showPicker && $event.target.showPicker()" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <label v-if="activeTab !== 'comparison'" class="flex items-center gap-2 cursor-pointer select-none group">
                                <input type="checkbox" v-model="showTarget" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">目標表示</span>
                            </label>
                        </div>
                    </div>

                    <div v-if="activeTab === 'shop'" class="pt-3 border-t border-gray-100 animate-in fade-in slide-in-from-top-1">
                        <div class="flex flex-wrap gap-x-6 gap-y-2">
                            <label v-for="type in shopDisplayTypes" :key="type.value" class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" v-model="shopDisplayType" :value="type.value" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="text-sm text-gray-700 group-hover:text-blue-700">{{ type.label }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm min-h-[500px] flex flex-col relative z-0">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-bold text-gray-800 text-lg flex items-center">
                    <span class="w-1.5 h-6 rounded-full bg-blue-600 mr-3"></span>
                    {{ getChartTitle }}
                </h3>
                <div v-if="isLoading" class="text-sm text-blue-500 animate-pulse">
                    読み込み中...
                </div>
            </div>

            <div class="flex-1 relative w-full h-full min-h-[400px]">
                <BarChart v-if="shouldUseBarChart" :data="chartData" :options="chartOptions" />
                <LineChart v-else :data="chartData" :options="chartOptions" />
            </div>
        </div>

        <div class="flex gap-3 items-center justify-center">
            <button @click="isAxisModalOpen = true"
                class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700 shadow-sm transition-all">
                <component :is="SettingsIcon" class="w-4 h-4" /> 軸設定 (Axis)
            </button>
            <button @click="isDataTableOpen = true"
                class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700 shadow-sm transition-all">
                <component :is="TableIcon" class="w-4 h-4" /> データ表 (Table)
            </button>
        </div>

        <div v-if="isAxisModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
             <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">Y軸スケール設定</h3>
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Y1軸 (左):</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input v-model="axisSettings.yLeftMin" placeholder="最小値 (Min)" class="border rounded px-3 py-2 text-sm">
                            <input v-model="axisSettings.yLeftMax" placeholder="最大値 (Max)" class="border rounded px-3 py-2 text-sm">
                        </div>
                    </div>
                    <div v-if="activeTab === 'shop'" class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Y2軸 (右):</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input v-model="axisSettings.yRightMin" placeholder="最小値 (Min)" class="border rounded px-3 py-2 text-sm">
                            <input v-model="axisSettings.yRightMax" placeholder="最大値 (Max)" class="border rounded px-3 py-2 text-sm">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button @click="isAxisModalOpen = false" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">キャンセル</button>
                    <button @click="applyAxisSettings" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">適用</button>
                </div>
            </div>
        </div>

        <div v-if="isDataTableOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
                <div class="p-6 border-b flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold">データ詳細</h3>
                        <p class="text-sm text-gray-500">{{ getDateDisplay(currentDate, currentPeriodType) }}</p>
                    </div>
                    <button @click="isDataTableOpen = false" class="text-gray-400 hover:text-gray-600">
                        <component :is="XIcon" class="w-6 h-6" />
                    </button>
                </div>
                <div class="p-0 overflow-auto flex-1">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-6 py-3">時間/項目</th>
                                <th v-for="ds in chartData?.datasets" :key="ds.label" class="px-6 py-3 text-right">
                                    {{ ds.label }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(label, idx) in chartData?.labels" :key="idx" class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ label }}</td>
                                <td v-for="ds in chartData?.datasets" :key="ds.label" class="px-6 py-4 text-right">
                                    {{ ds.data[idx]?.toLocaleString() || '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t bg-gray-50 flex justify-end">
                    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded hover:bg-gray-100 text-sm font-medium">
                        <component :is="DownloadIcon" class="w-4 h-4" /> CSV出力
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import { format, parseISO, startOfWeek } from 'date-fns';
import { ja } from 'date-fns/locale';
import axios from 'axios'; // Import Axios
import { 
    Calendar as CalendarIcon, 
    X as XIcon, 
    Settings as SettingsIcon, 
    Table as TableIcon,
    Download as DownloadIcon 
} from 'lucide-vue-next';

import LineChart from './components/charts/type/Line.vue';
import BarChart from './components/charts/type/Bar.vue';
import { fetchDashboardChartData } from '../services/mockData';

const route = useRoute();

// --- STATE ---
const treeData = ref([]); // State lưu trữ cây thiết bị từ API
const activeTab = ref('period');
const currentPeriodType = ref('day');
const currentDate = ref(new Date());
const pickerValue = ref(format(new Date(), 'yyyy-MM-dd'));
const compareDate = ref(null);
const comparePickerValue = ref('');
const showTarget = ref(true);
const shopDisplayType = ref('cost');
const isLoading = ref(false);

// Constants
const tabs = [
    { label: '使用量推移', value: 'period' },
    { label: '設備比較', value: 'comparison' },
    { label: 'コスト/CO2', value: 'shop' }
];
const periods = [
    { label: '日', value: 'day' }, { label: '週', value: 'week' },
    { label: '月', value: 'month' }, { label: '年', value: 'year' }
];
const shopDisplayTypes = [
    { label: 'コスト', value: 'cost' }, 
    { label: 'CO2排出量', value: 'co2' },
    { label: '台当たりコスト', value: 'cost_per_unit' }, 
    { label: '台当たりCO2排出量', value: 'co2_per_unit' }
];

// Modals State
const isAxisModalOpen = ref(false);
const isDataTableOpen = ref(false);
const axisSettings = reactive({ yLeftMin: '', yLeftMax: '', yRightMin: '', yRightMax: '' });
const chartData = ref({ labels: [], datasets: [] });

// --- API: FETCH TREE DATA ---
const fetchTreeData = async () => {
    try {
        // Gọi API lấy cây thiết bị
        const response = await axios.get('/api/equipments/tree');
        treeData.value = response.data; // Giả sử API trả về mảng root nodes
    } catch (error) {
        console.error('Failed to load equipment tree:', error);
    }
};

// --- COMPUTED: PROCESS NAME (RECURSIVE PATH) ---
// Hàm đệ quy tìm đường dẫn đến node có ID cho trước
const findPathToNode = (nodes, targetId, currentPath = []) => {
    for (const node of nodes) {
        // Tạo đường dẫn mới bao gồm node hiện tại
        const newPath = [...currentPath, node.name];
        
        // Nếu tìm thấy ID (so sánh lỏng lẻo vì query param là string)
        if (node.id == targetId) {
            return newPath;
        }
        
        // Nếu có con, tìm tiếp
        if (node.children && node.children.length > 0) {
            const foundPath = findPathToNode(node.children, targetId, newPath);
            if (foundPath) return foundPath;
        }
    }
    return null;
};

const processName = computed(() => {
    // 1. Xác định ID cần tìm từ URL (ưu tiên equipment > utility > facility)
    const targetId = route.query.equipment || route.query.utility || route.query.facility;
    
    // Nếu không có ID hoặc chưa tải xong Tree -> Mặc định
    if (!targetId || treeData.value.length === 0) return '全工程 (All Process)';

    // 2. Tìm đường dẫn tên
    const pathArray = findPathToNode(treeData.value, targetId);
    
    // 3. Ghép chuỗi với dấu gạch dưới
    if (pathArray) {
        return pathArray.join('_'); // Ví dụ: "1ライン_工場_電気"
    }
    
    return 'Unknown Process'; // Trường hợp có ID nhưng không tìm thấy trong Tree
});

// --- CHART LOGIC & HELPERS (Giữ nguyên) ---
const shouldUseBarChart = computed(() => {
    return activeTab.value === 'comparison' || activeTab.value === 'shop';
});

const getChartTitle = computed(() => {
    const periodLabel = getDateDisplay(currentDate.value, currentPeriodType.value);
    if (activeTab.value === 'comparison') return `設備比較 (${periodLabel})`;
    if (activeTab.value === 'shop') {
        const typeLabel = shopDisplayTypes.find(t => t.value === shopDisplayType.value)?.label;
        return `${typeLabel} (${periodLabel})`;
    }
    let title = `期報: ${periodLabel}`;
    if (compareDate.value) title += ` vs ${getDateDisplay(compareDate.value, currentPeriodType.value)}`;
    return title;
});

const chartOptions = computed(() => {
    const isShop = activeTab.value === 'shop';
    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
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
            x: { stacked: isShop, grid: { display: false } },
            y: {
                stacked: isShop,
                beginAtZero: true,
                position: 'left',
                title: { display: true, text: getUnitLabel() },
                min: axisSettings.yLeftMin ? Number(axisSettings.yLeftMin) : undefined,
                max: axisSettings.yLeftMax ? Number(axisSettings.yLeftMax) : undefined,
            },
            y1: {
                display: isShop && !shopDisplayType.value.includes('per_unit'), 
                position: 'right',
                beginAtZero: true,
                grid: { drawOnChartArea: false },
                title: { display: true, text: '生産台数 (Units)' },
                min: axisSettings.yRightMin ? Number(axisSettings.yRightMin) : undefined,
                max: axisSettings.yRightMax ? Number(axisSettings.yRightMax) : undefined,
            }
        }
    };
});

const getInputType = (type) => {
    if (type === 'week') return 'week';
    if (type === 'month') return 'month';
    if (type === 'year') return 'number';
    return 'date';
};

const getDateDisplay = (dateObj, type) => {
    if (!dateObj) return '';
    try {
        if (type === 'day') return format(dateObj, 'yyyy/MM/dd', { locale: ja });
        if (type === 'week') return format(startOfWeek(dateObj, { weekStartsOn: 1 }), 'yyyy/MM/dd') + '週';
        if (type === 'month') return format(dateObj, 'yyyy/MM', { locale: ja });
        if (type === 'year') return format(dateObj, 'yyyy年', { locale: ja });
    } catch (e) { return ''; }
    return '';
};

const getUnitLabel = () => {
    if (activeTab.value === 'shop') return shopDisplayType.value.includes('cost') ? '円 (JPY)' : 'kg-CO2';
    return 'kWh';
};

const updateDate = (val, target) => {
    let d = new Date();
    if (currentPeriodType.value === 'week' && val.includes('-W')) {
        d = parseISO(val.substring(0, 4) + '-01-01');
    } else if (currentPeriodType.value === 'year') {
        d = new Date(parseInt(val.substring(0, 4)), 0, 1); 
    } else if (val) {
        d = new Date(val);
    } else {
        d = null;
    }
    if (target === 'date') { currentDate.value = d; pickerValue.value = val; }
    else { compareDate.value = d; comparePickerValue.value = val; }
};

const clearCompare = () => { compareDate.value = null; comparePickerValue.value = ''; };
const applyAxisSettings = () => { isAxisModalOpen.value = false; };

const generateChartData = async () => {
    isLoading.value = true;
    try {
        const data = await fetchDashboardChartData({
            activeTab: activeTab.value,
            periodType: currentPeriodType.value,
            currentDate: currentDate.value,
            compareDate: compareDate.value,
            showTarget: showTarget.value,
            shopDisplayType: shopDisplayType.value
        });
        chartData.value = data;
    } catch (error) {
        console.error("Error fetching dashboard data", error);
    } finally {
        isLoading.value = false;
    }
};

watch([activeTab, currentPeriodType, currentDate, compareDate, showTarget, shopDisplayType, () => route.query], () => {
    if (activeTab.value === 'comparison') compareDate.value = null;
    generateChartData();
}, { deep: true });

onMounted(() => {
    // Gọi cả 2 API khi mount
    fetchTreeData(); 
    generateChartData();
});
</script>

<style scoped>
::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-track { background: #f1f1f1; }
::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: #999; }
</style>