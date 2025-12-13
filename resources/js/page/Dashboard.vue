<template>
    <div class="p-6 space-y-4 bg-gray-50 min-h-full font-sans text-gray-800">

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 leading-tight">グラフ表示</h1>
                <div class="text-sm font-medium text-gray-500 mt-1">{{ processName }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col overflow-hidden">
            <div class="p-6">
                <div class="space-y-4">
                    <div
                        class="grid w-full grid-cols-3 h-10 items-center justify-center rounded-md bg-slate-100 p-1 text-slate-500">
                        <button v-for="tab in tabs" :key="tab.value" @click="activeTab = tab.value"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-white transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                            :class="activeTab === tab.value ? 'bg-white text-slate-950 shadow-sm' : 'hover:text-slate-900'">
                            {{ tab.label }}
                        </button>
                    </div>

                    <div class="">

                        <div class="flex flex-wrap items-center justify-between gap-4">

                            <div class="flex flex-wrap items-center gap-4">

                                <template v-if="activeTab === 'period'">
                                    <div class="flex bg-gray-100 p-1 rounded-lg">
                                        <button v-for="p in periods" :key="p.value" @click="periodType = p.value"
                                            class="px-3 py-1.5 text-xs font-bold rounded-md transition-all"
                                            :class="periodType === p.value ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-900'">
                                            {{ p.label }}
                                        </button>
                                    </div>

                                    <div class="relative group w-[200px]">
                                        <div
                                            class="flex items-center border border-gray-300 rounded-md px-3 py-1.5 bg-white shadow-sm h-[38px] hover:bg-gray-50 transition-colors">
                                            <component :is="CalendarIcon" class="w-4 h-4 text-gray-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700 truncate flex-1">{{
                                                getDateDisplay(date, periodType) }}</span>
                                        </div>
                                        <input :type="getInputType(periodType)" :value="pickerValue"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            @input="updateDate($event.target.value, 'date', periodType)"
                                            @click="$event.target.showPicker && $event.target.showPicker()" />
                                    </div>

                                    <div class="relative group w-[200px]">
                                        <div class="flex items-center border border-gray-300 rounded-md px-3 py-1.5 bg-white shadow-sm h-[38px] hover:bg-gray-50 transition-colors"
                                            :class="compareDate ? 'text-gray-700' : 'text-gray-400'">
                                            <component :is="CalendarIcon" class="w-4 h-4 mr-2"
                                                :class="compareDate ? 'text-gray-500' : 'text-gray-400'" />
                                            <span class="text-sm font-medium truncate flex-1">{{ compareDate ?
                                                getDateDisplay(compareDate, periodType) : '比較期間を選択' }}</span>
                                            <button v-if="compareDate" @click.stop="clearCompare('period')"
                                                class="ml-2 p-0.5 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-600 relative z-20">
                                                <component :is="XIcon" class="w-3 h-3" />
                                            </button>
                                        </div>
                                        <input :type="getInputType(periodType)" :value="comparePickerValue"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                            @input="updateDate($event.target.value, 'compareDate', periodType)"
                                            @click="$event.target.showPicker && $event.target.showPicker()" />
                                    </div>
                                </template>

                                <template v-if="activeTab === 'comparison'">
                                    <div class="flex bg-gray-100 p-1 rounded-lg">
                                        <button v-for="p in periods" :key="p.value"
                                            @click="comparisonPeriodType = p.value"
                                            class="px-3 py-1.5 text-xs font-bold rounded-md transition-all"
                                            :class="comparisonPeriodType === p.value ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-900'">
                                            {{ p.label }}
                                        </button>
                                    </div>

                                    <div class="relative group w-[200px]">
                                        <div
                                            class="flex items-center border border-gray-300 rounded-md px-3 py-1.5 bg-white shadow-sm h-[38px] hover:bg-gray-50 transition-colors">
                                            <component :is="CalendarIcon" class="w-4 h-4 text-gray-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700 truncate flex-1">{{
                                                getDateDisplay(comparisonDate, comparisonPeriodType) }}</span>
                                        </div>
                                        <input :type="getInputType(comparisonPeriodType)" :value="comparisonPickerValue"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            @input="updateDate($event.target.value, 'comparisonDate', comparisonPeriodType)"
                                            @click="$event.target.showPicker && $event.target.showPicker()" />
                                    </div>
                                </template>

                                <template v-if="activeTab === 'shop'">
                                    <div class="flex bg-gray-100 p-1 rounded-lg">
                                        <button v-for="p in periods" :key="p.value" @click="shopPeriodType = p.value"
                                            class="px-3 py-1.5 text-xs font-bold rounded-md transition-all"
                                            :class="shopPeriodType === p.value ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-900'">
                                            {{ p.label }}
                                        </button>
                                    </div>

                                    <div class="relative group w-[200px]">
                                        <div
                                            class="flex items-center border border-gray-300 rounded-md px-3 py-1.5 bg-white shadow-sm h-[38px] hover:bg-gray-50 transition-colors">
                                            <component :is="CalendarIcon" class="w-4 h-4 text-gray-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700 truncate flex-1">{{
                                                getDateDisplay(shopDate, shopPeriodType) }}</span>
                                        </div>
                                        <input :type="getInputType(shopPeriodType)" :value="shopPickerValue"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            @input="updateDate($event.target.value, 'shopDate', shopPeriodType)"
                                            @click="$event.target.showPicker && $event.target.showPicker()" />
                                    </div>

                                    <div class="relative group w-[200px]">
                                        <div class="flex items-center border border-gray-300 rounded-md px-3 py-1.5 bg-white shadow-sm h-[38px] hover:bg-gray-50 transition-colors"
                                            :class="shopCompareDate ? 'text-gray-700' : 'text-gray-400'">
                                            <component :is="CalendarIcon" class="w-4 h-4 mr-2"
                                                :class="shopCompareDate ? 'text-gray-500' : 'text-gray-400'" />
                                            <span class="text-sm font-medium truncate flex-1">{{ shopCompareDate ?
                                                getDateDisplay(shopCompareDate, shopPeriodType) : '比較期間を選択' }}</span>
                                            <button v-if="shopCompareDate" @click.stop="clearCompare('shop')"
                                                class="ml-2 p-0.5 rounded-full hover:bg-gray-200 text-gray-400 hover:text-gray-600 relative z-20">
                                                <component :is="XIcon" class="w-3 h-3" />
                                            </button>
                                        </div>
                                        <input :type="getInputType(shopPeriodType)" :value="shopComparePickerValue"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                            @input="updateDate($event.target.value, 'shopCompareDate', shopPeriodType)"
                                            @click="$event.target.showPicker && $event.target.showPicker()" />
                                    </div>
                                </template>

                            </div>

                            <div class="flex items-center gap-6">
                                <label class="flex items-center gap-2 cursor-pointer select-none group">
                                    <input type="checkbox" v-model="showTarget"
                                        class="rounded text-red-600 focus:ring-red-500">
                                    <span
                                        class="text-sm font-bold text-gray-700 group-hover:text-gray-900 transition-colors">目標表示</span>
                                </label>
                            </div>
                        </div>

                        <div v-if="activeTab === 'shop'" class="pt-4 border-t border-gray-100">
                            <div class="flex flex-wrap gap-6">
                                <label v-for="type in shopDisplayTypes" :key="type.value"
                                    class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" v-model="shopDisplayType" :value="type.value"
                                        class="text-blue-600 focus:ring-blue-500">
                                    <span
                                        class="text-sm font-medium text-gray-700 group-hover:text-blue-700 transition-colors">{{
                                            type.label
                                        }}</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm h-[600px] flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-800 text-lg flex items-center">
                    <span class="w-1.5 h-6 rounded-full bg-blue-600 mr-3"></span>
                    {{ getChartTitle() }}
                </h3>
            </div>

            <div class="flex-1 relative w-full min-h-0">
                <BarChart v-if="activeTab === 'comparison' && chartData" :data="chartData" :options="chartOptions" />
                <LineChart v-else-if="chartData" :data="chartData" :options="chartOptions" />
            </div>
        </div>
        <div class="flex gap-2 items-center justify-center">
            <button
                class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 rounded-md hover:bg-gray-50 text-xs font-bold text-gray-600 transition-colors shadow-sm">
                <component :is="SettingsIcon" class="w-3.5 h-3.5" /> 軸設定
            </button>
            <button
                class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 rounded-md hover:bg-gray-50 text-xs font-bold text-gray-600 transition-colors shadow-sm">
                データ表
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { format, parseISO, startOfWeek } from 'date-fns';
import { ja } from 'date-fns/locale';
import { Calendar as CalendarIcon, X as XIcon, Settings as SettingsIcon, Download as DownloadIcon } from 'lucide-vue-next';
import LineChart from './components/charts/type/Line.vue';
import BarChart from './components/charts/type/Bar.vue';
import { mockTreeData } from '../services/mockData';

const route = useRoute();

// --- CONFIG ---
const tabs = [
    { label: '使用量推移', value: 'period' },
    { label: '設備比較', value: 'comparison' },
    { label: 'コスト/CO2', value: 'shop' }
];
const periods = [
    { label: '日', value: 'day' }, { label: '週', value: 'week' },
    { label: '月', value: 'month' }, { label: '年', value: 'year' }
];
const units = [
    { label: 'kWh/m³', value: 'kwh' }, { label: 'コスト', value: 'cost' }, { label: 'CO2', value: 'co2' }
];
const shopDisplayTypes = [
    { label: 'コスト', value: 'cost' }, { label: 'CO2排出量', value: 'co2' },
    { label: '台当たりコスト', value: 'cost_per_unit' }, { label: '台当たりCO2排出量', value: 'co2_per_unit' }
];
const yearOptions = Array.from({ length: 10 }, (_, i) => new Date().getFullYear() - 5 + i);

// --- STATE ---
const activeTab = ref('period');

// Period Mode
const periodType = ref('day');
const date = ref(new Date());
const pickerValue = ref(format(new Date(), 'yyyy-MM-dd'));
const compareDate = ref(null);
const comparePickerValue = ref('');
const displayUnit = ref('kwh');
const showTarget = ref(true);

// Comparison Mode
const comparisonPeriodType = ref('day');
const comparisonDate = ref(new Date());
const comparisonPickerValue = ref(format(new Date(), 'yyyy-MM-dd'));

// Shop Mode
const shopPeriodType = ref('day');
const shopDate = ref(new Date());
const shopPickerValue = ref(format(new Date(), 'yyyy-MM-dd'));
const shopCompareDate = ref(null);
const shopComparePickerValue = ref('');
const shopDisplayType = ref('cost');

const chartData = ref(null);

// --- HELPERS ---
const getInputType = (type) => {
    if (type === 'week') return 'week';
    if (type === 'month') return 'month';
    if (type === 'year') return 'month';
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

const updateDate = (val, target, type) => {
    let d = new Date();
    // Parse value based on type
    if (type === 'week') {
        const [year, week] = val.split('-W');
        // Simple logic to get date from week (can be improved)
        d = parseISO(val.substring(0, 4) + '-01-01');
    }
    else if (type === 'year') {
        d = new Date(parseInt(val), 0, 1);
    }
    else if (val) {
        d = new Date(val);
    } else {
        d = null;
    }

    if (target === 'date') { date.value = d; pickerValue.value = val; }
    if (target === 'compareDate') { compareDate.value = d; comparePickerValue.value = val; }
    if (target === 'comparisonDate') { comparisonDate.value = d; comparisonPickerValue.value = val; }
    if (target === 'shopDate') { shopDate.value = d; shopPickerValue.value = val; }
    if (target === 'shopCompareDate') { shopCompareDate.value = d; shopComparePickerValue.value = val; }
};

const clearCompare = (mode) => {
    if (mode === 'period') { compareDate.value = null; comparePickerValue.value = ''; }
    if (mode === 'shop') { shopCompareDate.value = null; shopComparePickerValue.value = ''; }
};

// --- DATA GENERATION ---
const generateData = () => {
    let labels = [];
    let datasets = [];
    let count = 24;

    // 1. PERIOD & SHOP (Line Chart)
    if (activeTab.value === 'period' || activeTab.value === 'shop') {
        const pType = activeTab.value === 'period' ? periodType.value : shopPeriodType.value;
        const compDate = activeTab.value === 'period' ? compareDate.value : shopCompareDate.value;
        const isShop = activeTab.value === 'shop';

        // Labels
        if (pType === 'day') { count = 24; labels = Array.from({ length: 24 }, (_, i) => `${i}:00`); }
        else if (pType === 'week') { count = 7; labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']; }
        else if (pType === 'month') { count = 31; labels = Array.from({ length: 31 }, (_, i) => `${i + 1}`); }
        else { count = 12; labels = Array.from({ length: 12 }, (_, i) => `${i + 1}月`); }

        // Color & Name
        const mainColor = '#2563eb'; // Blue
        const compColor = '#16a34a'; // Green
        const targetColor = '#ef4444'; // Red

        // Data 1 (Main)
        datasets.push({
            label: isShop ? (compDate ? getDateDisplay(shopDate.value, pType) : '実績') : '実績',
            borderColor: mainColor,
            backgroundColor: mainColor,
            data: Array.from({ length: count }, () => Math.floor(Math.random() * 100) + 50),
            tension: 0.3,
            fill: false
        });

        // Data 2 (Comparison)
        if (compDate) {
            datasets.push({
                label: isShop ? getDateDisplay(shopCompareDate.value, pType) : '比較対象',
                borderColor: compColor,
                backgroundColor: compColor,
                data: Array.from({ length: count }, () => Math.floor(Math.random() * 100) + 40),
                tension: 0.3,
                fill: false
            });
        }

        // Data 3 (Target)
        if (showTarget.value) {
            datasets.push({
                label: '目標',
                borderColor: targetColor,
                borderDash: [5, 5],
                data: Array.from({ length: count }, () => 120),
                pointRadius: 0,
                borderWidth: 2,
                fill: false
            });
        }
    }
    // 2. COMPARISON (Bar Chart)
    else if (activeTab.value === 'comparison') {
        labels = ['設備 A', '設備 B', '設備 C', '設備 D', '設備 E'];
        datasets.push({
            label: '実績',
            backgroundColor: '#2563eb',
            data: Array.from({ length: 5 }, () => Math.floor(Math.random() * 500) + 100)
        });

        // Fake Target Line for Bar Chart (Mixed chart)
        if (showTarget.value) {
            datasets.push({
                type: 'line',
                label: '目標',
                borderColor: '#ef4444',
                borderDash: [5, 5],
                data: Array.from({ length: 5 }, () => 450),
                pointRadius: 0,
                borderWidth: 2
            });
        }
    }

    chartData.value = { labels, datasets };
};

// --- COMPUTED ---
const processName = computed(() => {
    const { facility, utility, equipment } = route.query;
    const findName = (nodes, id) => {
        for (const node of nodes) {
            if (node.id === id) return node.name;
            if (node.children) {
                const found = findName(node.children, id);
                if (found) return found;
            }
        }
        return null;
    };

    if (equipment) return findName(mockTreeData, equipment) || 'Equipment';
    if (utility) return findName(mockTreeData, utility) || 'Utility';
    if (facility) return findName(mockTreeData, facility) || 'Facility';
    return '全工程';
});

const getUnitLabel = () => {
    if (activeTab.value === 'shop') return shopDisplayType.value.includes('cost') ? '円' : 'kg';
    return displayUnit.value === 'cost' ? '円' : (displayUnit.value === 'co2' ? 'kg' : 'kWh/m³');
};

const getChartTitle = () => {
    if (activeTab.value === 'comparison') return `設備比較 (${getDateDisplay(comparisonDate.value, comparisonPeriodType.value)})`;
    if (activeTab.value === 'shop') return 'コスト/CO2';
    // Period tab title logic
    if (compareDate.value) return `期報: ${getDateDisplay(date.value, periodType.value)} vs ${getDateDisplay(compareDate.value, periodType.value)}`;
    return `期報 (${periodType.value === 'day' ? '日' : periodType.value === 'week' ? '週' : periodType.value === 'month' ? '月' : '年'})`;
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' }
    },
    scales: {
        y: { beginAtZero: true }
    }
};

// --- WATCHERS ---
watch([activeTab, periodType, comparisonPeriodType, shopPeriodType,
    date, compareDate, comparisonDate, shopDate, shopCompareDate,
    displayUnit, shopDisplayType, showTarget, () => route.query],
    generateData, { immediate: true });

onMounted(generateData);
</script>