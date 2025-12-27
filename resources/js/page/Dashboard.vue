<template>
    <div class="p-6 space-y-6 bg-gray-50/50 min-h-screen font-sans text-gray-800">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">グラフ表示</h1>
                <p class="text-sm text-muted-foreground mt-1 text-gray-500 font-medium">{{ processName }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 space-y-4">

                <div
                    class="grid w-full grid-cols-3 h-10 items-center justify-center rounded-md bg-slate-100 p-1 text-slate-500">
                    <button v-for="tab in tabs" :key="tab.value" @click="activeTab = tab.value"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-white transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                        :class="activeTab === tab.value ? 'bg-white text-slate-950 shadow-sm' : 'hover:text-slate-900'">
                        {{ tab.label }}
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-4">

                    <div class="flex items-center gap-2">
                        <div
                            class="inline-flex h-9 items-center justify-center rounded-lg bg-slate-100 p-1 text-slate-500">
                            <button v-for="p in periods" :key="p.value" @click="currentPeriodType = p.value"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1 text-xs font-medium ring-offset-white transition-all"
                                :class="currentPeriodType === p.value ? 'bg-white text-slate-950 shadow-sm' : 'hover:text-slate-900'">
                                {{ p.label }}
                            </button>
                        </div>

                        <div class="relative group">
                            <button
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 h-9 px-4 py-2 w-[160px] text-left font-normal">
                                <component :is="CalendarIcon" class="mr-2 h-4 w-4 opacity-50" />
                                <span class="flex-1 truncate">{{ getDateDisplay(currentDate, currentPeriodType)
                                }}</span>
                            </button>

                            <select v-if="currentPeriodType === 'year'"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 appearance-none"
                                :value="currentDate ? currentDate.getFullYear() : ''"
                                @change="updateDate($event.target.value, 'date')">
                                <option v-for="y in yearRange" :key="y" :value="y">{{ y }}年</option>
                            </select>
                            <input v-else ref="dateInput" :type="getInputType(currentPeriodType)" :value="pickerValue"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                @input="updateDate($event.target.value, 'date')"
                                @click="$event.target.showPicker && $event.target.showPicker()" />
                        </div>

                        <div v-if="activeTab !== 'comparison'" class="relative group">
                            <button
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 h-9 px-4 py-2 w-[160px] text-left font-normal">
                                <component :is="CalendarIcon" class="mr-2 h-4 w-4 opacity-50" />
                                <span v-if="compareDate" class="flex-1 truncate">{{ getDateDisplay(compareDate,
                                    currentPeriodType) }}</span>
                                <span v-else class="text-muted-foreground opacity-50">比較期間を選択</span>
                            </button>
                            <button v-if="compareDate" @click.stop="clearCompare"
                                class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-full hover:bg-slate-200 text-slate-500 z-20">
                                <component :is="XIcon" class="w-3 h-3" />
                            </button>

                            <select v-if="currentPeriodType === 'year'"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 appearance-none"
                                :value="compareDate ? compareDate.getFullYear() : ''"
                                @change="updateDate($event.target.value, 'compareDate')">
                                <option value="" disabled>選択</option>
                                <option v-for="y in yearRange" :key="y" :value="y">{{ y }}年</option>
                            </select>
                            <input v-else ref="compareInput" :type="getInputType(currentPeriodType)"
                                :value="comparePickerValue"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                @input="updateDate($event.target.value, 'compareDate')"
                                @click="$event.target.showPicker && $event.target.showPicker()" />
                        </div>
                    </div>

                    <div class="flex-1"></div>

                    <div v-if="activeTab !== 'comparison' && shouldShowTarget" class="flex items-center space-x-2">
                        <input type="checkbox" id="showTarget" v-model="showTarget"
                            class="peer h-4 w-4 shrink-0 rounded-sm border border-slate-900 ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-slate-900 data-[state=checked]:text-slate-50 accent-black">
                        <label for="showTarget"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer select-none">
                            目標表示
                        </label>
                    </div>
                </div>

                <div v-if="activeTab === 'shop'"
                    class="pt-4 border-t border-slate-100 flex flex-wrap gap-6 animate-in fade-in slide-in-from-top-2">
                    <label v-for="type in shopDisplayTypes" :key="type.value"
                        class="flex items-center space-x-2 cursor-pointer group">
                        <input type="radio" v-model="shopDisplayType" :value="type.value" name="shopType"
                            class="aspect-square h-4 w-4 rounded-full border border-slate-900 text-slate-900 ring-offset-white focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 accent-black">
                        <span
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 group-hover:text-black text-slate-700">
                            {{ type.label }}
                        </span>
                    </label>
                </div>

            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 h-[550px] flex flex-col">
            <div class="flex justify-between items-start mb-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">{{ getChartTitle }}</h3>
                <div v-if="isLoading" class="flex items-center text-sm text-blue-600">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-current mr-2"></div>
                    読み込み中...
                </div>
            </div>

            <div class="flex-1 w-full relative h-[450px]">
                <div v-if="isLoading"
                    class="flex flex-col items-center justify-center h-full w-full absolute inset-0 z-10 bg-white/80">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-900"></div>
                    <span class="mt-2 text-sm text-slate-500">データ読み込み中...</span>
                </div>

                <template v-if="!isLoading && chartVisibility.show">
                    <PeriodTab v-if="activeTab === 'period'" :chartData="chartData" :axisSettings="axisSettings"
                        :unit="getCurrentUnit" :periodType="currentPeriodType" />
                    <ComparisonTab v-else-if="activeTab === 'comparison'" :chartData="chartData"
                        :axisSettings="axisSettings" :unit="getCurrentUnit" :periodType="currentPeriodType" />
                    <ShopTab v-else-if="activeTab === 'shop'" :chartData="chartData" :axisSettings="axisSettings"
                        :unit="getCurrentUnit" :shopDisplayType="shopDisplayType" :isLevel4="nodeLevel === 4" />
                </template>

                <div v-if="!isLoading && !chartVisibility.show"
                    class="flex flex-col items-center justify-center h-full text-slate-500 bg-slate-50 rounded-lg border border-dashed border-slate-200">
                    <span class="text-sm font-medium">{{ chartVisibility.msg }}</span>
                </div>
            </div>
        </div>

        <div class="flex justify-center gap-4">
            <button @click="isAxisModalOpen = true" :disabled="!chartVisibility.show"
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 h-10 px-4 py-2 gap-2">
                <component :is="SettingsIcon" class="h-4 w-4" /> 軸設定
            </button>
            <button @click="isDataTableOpen = true" :disabled="!chartVisibility.show"
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 h-10 px-4 py-2 gap-2">
                <component :is="TableIcon" class="h-4 w-4" /> データ表
            </button>
        </div>

        <AxisSettingsModal :isOpen="isAxisModalOpen" :settings="axisSettings" :activeTab="activeTab"
            @close="isAxisModalOpen = false" @apply="applyAxisSettings" />

        <DataTableModal :isOpen="isDataTableOpen"
            :chartData="activeTab === 'comparison' ? comparisonDetailData : chartData" :activeTab="activeTab"
            :currentPeriodType="currentPeriodType" :processName="processName" :unit="getCurrentUnit"
            :currentDate="currentDate" :compareDate="compareDate" @close="isDataTableOpen = false" />
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import { format, parseISO, startOfWeek, endOfWeek, getWeek, addDays } from 'date-fns';
import { ja } from 'date-fns/locale';
import {
    Calendar as CalendarIcon,
    X as XIcon,
    Settings as SettingsIcon,
    Table as TableIcon
} from 'lucide-vue-next';

// --- IMPORT SUB-COMPONENTS ---
import PeriodTab from './components/dashboard/tabs/PeriodTab.vue';
import ComparisonTab from './components/dashboard/tabs/ComparisonTab.vue';
import ShopTab from './components/dashboard/tabs/ShopTab.vue';
import AxisSettingsModal from './components/dashboard/modal/AxisSettingsModal.vue';
import DataTableModal from './components/dashboard/modal/DataTableModal.vue';

// --- PROPS ---
const props = defineProps({
    treeData: {
        type: Array,
        default: () => []
    }
});

const route = useRoute();

// --- STATE MANAGEMENT ---
const activeTab = ref('period');
const currentPeriodType = ref('day');
const currentDate = ref(new Date());
const pickerValue = ref(format(new Date(), 'yyyy-MM-dd'));
const compareDate = ref(null);
const comparePickerValue = ref('');
const showTarget = ref(true);
const shopDisplayType = ref('cost');
const isLoading = ref(false);

// Modal States
const isAxisModalOpen = ref(false);
const isDataTableOpen = ref(false);

// Chart Data & Settings
const axisSettings = reactive({
    yLeftMin: '', yLeftMax: '', yLeftStepSize: '',
    yRightMin: '', yRightMax: '', yRightStepSize: ''
});
const chartData = ref({ labels: [], datasets: [] });

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

// --- LOGIC: PROCESS NAME (Tree Traversal) ---
const findPathToNode = (nodes, targetId, currentPath = []) => {
    for (const node of nodes) {
        const newPath = [...currentPath, node.name];
        if (node.id == targetId) return newPath;
        if (node.children && node.children.length > 0) {
            const foundPath = findPathToNode(node.children, targetId, newPath);
            if (foundPath) return foundPath;
        }
    }
    return null;
};

// --- LOGIC TÌM NODE & LEVEL ---
const currentPathArray = computed(() => {
    const targetId = route.query.equipment || route.query.utility || route.query.facility;
    if (!targetId || props.treeData.length === 0) return [];
    return findPathToNode(props.treeData, targetId) || [];
});

const processName = computed(() => {
    return currentPathArray.value.length > 0 ? currentPathArray.value.join('_') : '全工程';
});

// Tính Level của Node (Dựa vào độ sâu của cây: Root=1, Factory=2, Line=3, Equipment=4)
const nodeLevel = computed(() => currentPathArray.value.length);

// --- LOGIC: CHART VISIBILITY & UNIT ---
const chartVisibility = computed(() => {
    const hasFacility = !!route.query.facility;
    const hasUtility = !!route.query.utility;
    const hasEquipment = !!route.query.equipment;

    if (activeTab.value === 'period') return (hasUtility || hasEquipment) ? { show: true } : { show: false, msg: '使用量推移を表示するには、サイドバーからユーティリティまたは設備を選択してください。' };
    if (activeTab.value === 'comparison') return hasUtility ? { show: true } : { show: false, msg: '設備比較を表示するには、サイドバーからユーティリティを選択してください。' };
    if (activeTab.value === 'shop') return (hasFacility || hasUtility || hasEquipment) ? { show: true } : { show: false, msg: 'コスト/CO2を表示するには、サイドバーから工場、ユーティリティ、または設備を選択してください。' };
    return { show: true };
});

const shouldShowTarget = computed(() => {
    const isShopEquipment = activeTab.value === 'shop' && !!route.query.equipment;
    return activeTab.value === 'period' || isShopEquipment;
});

const getCurrentUnit = computed(() => {
    if (activeTab.value === 'shop') return shopDisplayType.value.includes('cost') ? '円' : 'kg-CO2';
    return 'kWh';
});

// --- LOGIC: DATE & TITLE GENERATION ---
const yearRange = computed(() => {
    const currentYear = new Date().getFullYear();
    const startYear = currentYear - 10;
    const endYear = currentYear + 5;
    return Array.from({ length: endYear - startYear + 1 }, (_, i) => startYear + i).sort((a, b) => b - a);
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

const getChartTitle = computed(() => {
    const fmt = (d, f) => { try { return format(d, f, { locale: ja }); } catch (e) { return ''; } };
    const getWeekRange = (dateObj) => {
        if (!dateObj) return '';
        const start = startOfWeek(dateObj, { weekStartsOn: 1 });
        const end = endOfWeek(dateObj, { weekStartsOn: 1 });
        return `${fmt(dateObj, "yyyy")} (${fmt(start, "dd/MM")} ～ ${fmt(end, "dd/MM")})`;
    };

    let prefix = '';
    let suffix = '';

    if (activeTab.value === 'period') {
        prefix = currentPeriodType.value === 'day' ? '期間（日）' : currentPeriodType.value === 'week' ? '期間（週）' : currentPeriodType.value === 'month' ? '期間（月）' : '期間（年）';
    } else if (activeTab.value === 'comparison') {
        prefix = currentPeriodType.value === 'day' ? '設備比較（日）' : currentPeriodType.value === 'week' ? '設備比較（週）' : currentPeriodType.value === 'month' ? '設備比較（月）' : '設備比較（年）';
    } else if (activeTab.value === 'shop') {
        const typeLabel = shopDisplayTypes.find(t => t.value === shopDisplayType.value)?.label || 'コスト';
        suffix = currentPeriodType.value === 'day' ? '（日）' : currentPeriodType.value === 'week' ? '（週）' : currentPeriodType.value === 'month' ? '（月）' : '（年）';
        prefix = `${typeLabel}${suffix}`;
    }

    let mainDateStr = '';
    let compareDateStr = '';

    // Logic format date
    const formatDateStr = (d, type) => {
        if (type === 'day') return fmt(d, "yyyy/MM/dd");
        if (type === 'week') return getWeekRange(d);
        if (type === 'month') return fmt(d, "yyyy/MM");
        return fmt(d, "yyyy");
    };

    mainDateStr = formatDateStr(currentDate.value, currentPeriodType.value);

    if (compareDate.value && activeTab.value !== 'comparison') {
        compareDateStr = formatDateStr(compareDate.value, currentPeriodType.value);
        return `${prefix}: ${mainDateStr} ~ ${compareDateStr}`;
    }

    return `${prefix}: ${mainDateStr}`;
});

// Thêm biến này để lưu dữ liệu chi tiết cho bảng so sánh
const comparisonDetailData = ref({ labels: [], datasets: [] });
// --- DATA GENERATION (MOCK) ---
const generateChartData = async () => {
    if (!chartVisibility.value.show) {
        chartData.value = { labels: [], datasets: [] };
        return;
    }

    isLoading.value = true;
    await new Promise(r => setTimeout(r, 400));

    let labels = [];
    let dataCount = 0;

    if (currentPeriodType.value === 'day') {
        labels = Array.from({ length: 24 }, (_, i) => `${String(i).padStart(2, '0')}:00`);
        dataCount = 24;
    } else if (currentPeriodType.value === 'week') {
        const weekStart = startOfWeek(currentDate.value, { locale: ja });
        const weekdayNames = ['日', '月', '火', '水', '木', '金', '土'];

        // Logic 168 điểm cho cả Period Tab VÀ Shop Tab (nếu là Level 4 - Line Chart)
        if (activeTab.value === 'period' || (activeTab.value === 'shop' && nodeLevel.value === 4)) {
            labels = [];
            dataCount = 168;
            for (let i = 0; i < 7; i++) {
                const currentDay = addDays(weekStart, i);
                const dayName = weekdayNames[currentDay.getDay()];
                for (let h = 0; h < 24; h++) labels.push(dayName);
            }
        } else {
            labels = weekdayNames;
            dataCount = 7;
        }
    } else if (currentPeriodType.value === 'month') {
        labels = Array.from({ length: 30 }, (_, i) => `${i + 1}日`);
        dataCount = 30;
    } else {
        labels = Array.from({ length: 12 }, (_, i) => `${i + 1}月`);
        dataCount = 12;
    }
    
    const timeLabels = [...labels]; // Backup Time Labels
    const datasets = [];

    // --- TAB SPECIFIC DATA LOGIC ---
    // 1. PERIOD TAB
    if (activeTab.value === 'period') {
        const actualColor = 'hsl(217, 91%, 60%)';
        datasets.push({
            label: '実績',
            data: Array.from({ length: dataCount }, () => Math.random() * 50 + 30),
            borderColor: actualColor,
            borderWidth: 2, fill: false, tension: 0.1,
            pointStyle: 'circle', pointRadius: 4, pointBorderWidth: 2, pointBackgroundColor: '#ffffff', pointBorderColor: actualColor,
            pointHoverRadius: 6, pointHoverBorderWidth: 3, pointHoverBackgroundColor: '#ffffff',
        });
        if (compareDate.value) {
            const compareColor = 'hsl(142, 71%, 45%)';
            datasets.push({
                label: getDateDisplay(compareDate.value, currentPeriodType.value),
                data: Array.from({ length: dataCount }, () => Math.random() * 50 + 20),
                borderColor: compareColor,
                borderWidth: 2, fill: false, tension: 0.1,
                pointStyle: 'circle', pointRadius: 4, pointBorderWidth: 2, pointBackgroundColor: '#ffffff', pointBorderColor: compareColor,
                pointHoverRadius: 6, pointHoverBorderWidth: 3, pointHoverBackgroundColor: '#ffffff',
            });
        }
        if (showTarget.value) {
            datasets.push({
                label: '目標',
                data: Array.from({ length: dataCount }, () => 70),
                borderColor: 'hsl(0, 84%, 60%)',
                borderWidth: 2, borderDash: [5, 5],
                pointRadius: 0, pointHoverRadius: 0, fill: false
            });
        }
    }
    // 2. COMPARISON TAB
    else if (activeTab.value === 'comparison') {
        const equipments = ['EQ-001', 'EQ-002', 'EQ-003', 'EQ-004'];

        // --- A. DỮ LIỆU CHO BIỂU ĐỒ (SNAPSHOT) ---
        // Trục X là Tên thiết bị
        labels = equipments;
        datasets.push({
            label: '実績',
            data: equipments.map(() => Math.random() * 100 + 50),
            backgroundColor: 'hsl(217, 91%, 60%)',
            barPercentage: 0.5,
        });

        // --- B. DỮ LIỆU CHO BẢNG (TIME SERIES) ---
        // Tạo dữ liệu chi tiết theo thời gian cho từng thiết bị
        const detailDatasets = equipments.map((eqName, index) => {
            // Mock dữ liệu theo timeLabels (dataCount)
            return {
                label: eqName,
                data: Array.from({ length: dataCount }, () => Math.random() * 100 + 50),
            };
        });

        // Lưu vào biến riêng cho bảng
        comparisonDetailData.value = {
            labels: timeLabels, // Sử dụng Time Labels (00:00, 01:00...)
            datasets: detailDatasets
        };
    }
    // 3. SHOP TAB
    else if (activeTab.value === 'shop') {
        // CASE 3A: LEVEL 4 (HIỂN THỊ BIỂU ĐỒ LINE)
        if (nodeLevel.value === 4) {
            // Thực tế (Actual) - Blue Line
            const actualColor = 'hsl(217, 91%, 60%)';
            datasets.push({
                label: '実績', // Hoặc Cost/CO2 tùy loại hiển thị
                data: Array.from({ length: dataCount }, () => Math.random() * 50 + 30),
                borderColor: actualColor,
                borderWidth: 2, fill: false, tension: 0.1,
                pointStyle: 'circle', pointRadius: 4, pointBorderWidth: 2, pointBackgroundColor: '#ffffff', pointBorderColor: actualColor,
                pointHoverRadius: 6, pointHoverBorderWidth: 3, pointHoverBackgroundColor: '#ffffff',
            });

            // So sánh (Compare) - Green Line
            if (compareDate.value) {
                const compareColor = 'hsl(142, 71%, 45%)';
                datasets.push({
                    label: getDateDisplay(compareDate.value, currentPeriodType.value), // Hoặc '実績 (Compare)'
                    data: Array.from({ length: dataCount }, () => Math.random() * 50 + 20),
                    borderColor: compareColor,
                    borderWidth: 2, fill: false, tension: 0.1,
                    pointStyle: 'circle', pointRadius: 4, pointBorderWidth: 2, pointBackgroundColor: '#ffffff', pointBorderColor: compareColor,
                    pointHoverRadius: 6, pointHoverBorderWidth: 3, pointHoverBackgroundColor: '#ffffff',
                });
            }

            // Kế hoạch (Plan) - Orange Line
            if (!shopDisplayType.value.includes('per_unit')) {
                const planColor = 'hsl(32, 95%, 44%)';
                datasets.push({
                    label: '生産計画',
                    data: Array.from({ length: dataCount }, () => Math.random() * 500 + 200),
                    borderColor: planColor,
                    borderWidth: 2, fill: false, tension: 0.1,
                    yAxisID: 'y1', // Vẫn dùng trục phải
                    pointStyle: 'circle', pointRadius: 4, pointBorderWidth: 2, pointBackgroundColor: '#ffffff', pointBorderColor: planColor,
                    pointHoverRadius: 6, pointHoverBorderWidth: 3, pointHoverBackgroundColor: '#ffffff',
                });
            }
        }

        // CASE 3B: LEVEL < 4 (HIỂN THỊ BIỂU ĐỒ STACKED BAR - CŨ)
        else {
            datasets.push({
                label: 'Category 1',
                data: Array.from({ length: dataCount }, () => Math.random() * 30 + 10),
                backgroundColor: 'hsl(217, 91%, 60%)',
                stack: 'Stack 0',
            });
            datasets.push({
                label: 'Category 2',
                data: Array.from({ length: dataCount }, () => Math.random() * 20 + 5),
                backgroundColor: 'hsl(217, 91%, 80%)',
                stack: 'Stack 0',
            });
            if (compareDate.value) {
                datasets.push({
                    label: 'Category 1 (Comp)',
                    data: Array.from({ length: dataCount }, () => Math.random() * 30 + 10),
                    backgroundColor: 'hsl(142, 71%, 45%)',
                    stack: 'Stack 1',
                });
            }
            if (!shopDisplayType.value.includes('per_unit')) {
                const planColor = 'hsl(32, 95%, 44%)';
                datasets.push({
                    type: 'line', // Mixed Chart
                    label: '生産計画',
                    data: Array.from({ length: dataCount }, () => Math.random() * 500 + 200),
                    borderColor: planColor,
                    borderWidth: 2, fill: false, yAxisID: 'y1', order: 0,
                    pointStyle: 'circle', pointRadius: 4, pointBorderWidth: 2, pointBackgroundColor: '#ffffff', pointBorderColor: planColor,
                    pointHoverRadius: 6, pointHoverBorderWidth: 3, pointHoverBackgroundColor: '#ffffff',
                });
            }
        }
    }

    chartData.value = { labels, datasets };
    isLoading.value = false;
};

// --- EVENTS HANDLERS ---
const updateDate = (val, target) => {
    let d = new Date();
    if (!val) d = null;
    else if (currentPeriodType.value === 'week' && val.includes('-W')) d = parseISO(val.substring(0, 4) + '-01-01');
    else if (currentPeriodType.value === 'year') d = new Date(parseInt(val.substring(0, 4)), 0, 1);
    else d = new Date(val);

    if (target === 'date') { currentDate.value = d; pickerValue.value = val; }
    else { compareDate.value = d; comparePickerValue.value = val; }
};

const clearCompare = () => { compareDate.value = null; comparePickerValue.value = ''; };

// Xử lý sự kiện "Apply" từ Modal Axis Settings
const applyAxisSettings = (newSettings) => {
    Object.assign(axisSettings, newSettings); // Update reactive state
    isAxisModalOpen.value = false; // Đóng modal
};

// --- WATCHERS & HOOKS ---
watch([activeTab, currentPeriodType, currentDate, compareDate, showTarget, shopDisplayType, () => route.query], () => {
    if (activeTab.value === 'comparison') compareDate.value = null;
    generateChartData();
}, { deep: true });

onMounted(() => {
    generateChartData();
});
</script>

<style scoped>
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>