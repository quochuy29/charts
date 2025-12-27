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
                <template v-if="chartVisibility.show">
                    <BarChart v-if="shouldUseBarChart" :data="chartData" :options="chartOptions"
                        class="w-full h-full" />
                    <LineChart v-else :data="chartData" :options="chartOptions" class="w-full h-full" />
                </template>

                <div v-else
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

        <div v-if="isAxisModalOpen"
            class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0"
            @click.self="isAxisModalOpen = false">
            <div
                class="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border bg-white p-6 shadow-lg duration-200 sm:rounded-lg">
                <div class="flex flex-col space-y-1.5 text-center sm:text-left">
                    <h2 class="text-lg font-semibold leading-none tracking-tight">Y軸スケール設定</h2>
                </div>

                <div class="space-y-6 py-4">
                    <div class="space-y-3">
                        <label
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Y1軸
                            設定:</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <span class="text-xs text-muted-foreground text-slate-500">最小値:</span>
                                <input v-model="axisSettings.yLeftMin"
                                    class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950"
                                    placeholder="0">
                            </div>
                            <div class="space-y-1">
                                <span class="text-xs text-muted-foreground text-slate-500">最大値:</span>
                                <input v-model="axisSettings.yLeftMax"
                                    class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950"
                                    placeholder="自動">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs text-muted-foreground text-slate-500">間隔:</span>
                            <input v-model="axisSettings.yLeftStepSize"
                                class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950"
                                placeholder="自動">
                        </div>
                    </div>

                    <div class="space-y-3 transition-opacity duration-200"
                        :class="{ 'opacity-50': activeTab !== 'shop' }">
                        <label
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Y2軸
                            設定:</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <span class="text-xs text-muted-foreground text-slate-500">最小値:</span>
                                <input v-model="axisSettings.yRightMin" :disabled="activeTab !== 'shop'"
                                    class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950 disabled:cursor-not-allowed disabled:bg-slate-50"
                                    placeholder="0">
                            </div>
                            <div class="space-y-1">
                                <span class="text-xs text-muted-foreground text-slate-500">最大値:</span>
                                <input v-model="axisSettings.yRightMax" :disabled="activeTab !== 'shop'"
                                    class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950 disabled:cursor-not-allowed disabled:bg-slate-50"
                                    placeholder="自動">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs text-muted-foreground text-slate-500">間隔:</span>
                            <input v-model="axisSettings.yRightStepSize" :disabled="activeTab !== 'shop'"
                                class="flex h-10 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-950 disabled:cursor-not-allowed disabled:bg-slate-50"
                                placeholder="自動">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2">
                    <button @click="isAxisModalOpen = false"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 h-10 px-4 py-2">キャンセル</button>
                    <button @click="applyAxisSettings"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors bg-slate-900 text-slate-50 hover:bg-slate-900/90 h-10 px-4 py-2">適用</button>
                </div>
            </div>
        </div>

        <div v-if="isDataTableOpen"
            class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0"
            @click.self="isDataTableOpen = false">
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[85vh] flex flex-col animate-in zoom-in-95 duration-200 sm:rounded-lg border border-slate-200">

                <div class="flex flex-col space-y-1.5 p-6 border-b">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold leading-none tracking-tight text-lg">データ詳細: {{ processName }}（{{
                                getCurrentUnit }}）</h3>
                            <p class="text-sm text-slate-500 mt-2 font-medium">{{ periodLabelText }}</p>
                        </div>
                        <button @click="isDataTableOpen = false"
                            class="rounded-sm opacity-70 ring-offset-white transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:ring-offset-2">
                            <component :is="XIcon" class="h-5 w-5" />
                        </button>
                    </div>
                    <div class="pt-2">
                        <button @click="downloadCSV"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 h-8 px-3 gap-2 shadow-sm">
                            <component :is="DownloadIcon" class="h-3.5 w-3.5" /> CSV出力
                        </button>
                    </div>
                </div>

                <div class="p-0 overflow-auto flex-1 relative">

                    <table v-if="activeTab === 'period'" class="w-full text-sm text-left caption-bottom">
                        <thead class="[&_tr]:border-b sticky top-0 bg-white z-10 shadow-sm">
                            <tr class="border-b transition-colors hover:bg-slate-100/50">
                                <th
                                    class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">
                                    {{ currentPeriodType === 'week' ? '曜日' : '日付' }}
                                </th>
                                <th
                                    class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">
                                    時間</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-slate-500">
                                    {{ processName }}<br>
                                    <span class="text-xs text-slate-400 font-normal">{{ getHeaderSubDate(currentDate,
                                        false)
                                        }}</span>
                                </th>
                                <th v-if="compareDate"
                                    class="h-12 px-4 text-right align-middle font-medium text-slate-500">
                                    {{ processName }}<br>
                                    <span class="text-xs text-slate-400 font-normal">{{ getHeaderSubDate(compareDate,
                                        false)
                                        }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0">
                            <tr v-for="(label, idx) in chartData.labels" :key="idx"
                                class="border-b transition-colors hover:bg-slate-50">
                                <td class="p-4 align-middle font-medium text-slate-900 bg-slate-50/30">{{
                                    getRowDateDisplay(label) }}</td>
                                <td class="p-4 align-middle text-slate-500 bg-slate-50/30">
                                    {{ currentPeriodType === 'week' ? String(idx % 24).padStart(2, '0') + ':00' :
                                    getRowTimeDisplay(label) }}
                                </td>
                                <td class="p-4 align-middle text-right tabular-nums">{{
                                    formatNumber(getDatasetValue('実績', idx))
                                    }}</td>
                                <td v-if="compareDate" class="p-4 align-middle text-right tabular-nums">{{
                                    formatNumber(getDatasetValue('compare', idx)) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table v-else-if="activeTab === 'comparison'" class="w-full text-sm text-left caption-bottom">
                        <thead class="[&_tr]:border-b sticky top-0 bg-white z-10 shadow-sm">
                            <tr class="border-b transition-colors hover:bg-slate-100/50">
                                <template v-if="currentPeriodType === 'week'">
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">
                                        曜日</th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">
                                        時間</th>
                                </template>
                                <template v-else>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[150px] bg-slate-50/50">
                                        {{ currentPeriodType === 'day' ? '時間' : '日付' }}
                                    </th>
                                </template>
                                <th v-for="ds in chartData.datasets" :key="ds.label"
                                    class="h-12 px-4 text-right align-middle font-medium text-slate-500">{{ ds.label }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0">
                            <tr v-for="(label, idx) in chartData.labels" :key="idx"
                                class="border-b transition-colors hover:bg-slate-50">
                                <template v-if="currentPeriodType === 'week'">
                                    <td class="p-4 align-middle font-medium text-slate-900 bg-slate-50/30">{{ label }}
                                    </td>
                                    <td class="p-4 align-middle text-slate-500 bg-slate-50/30">00:00</td>
                                </template>
                                <template v-else>
                                    <td class="p-4 align-middle font-medium text-slate-900 bg-slate-50/30">{{ label }}
                                    </td>
                                </template>
                                <td v-for="ds in chartData.datasets" :key="ds.label"
                                    class="p-4 align-middle text-right tabular-nums">{{ formatNumber(ds.data[idx]) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table v-else class="w-full text-sm text-left caption-bottom">
                        <thead class="[&_tr]:border-b sticky top-0 bg-white z-10 shadow-sm">
                            <tr class="border-b transition-colors hover:bg-slate-100/50">
                                <th
                                    class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">
                                    {{ currentPeriodType === 'week' ? '曜日' : '日付' }}
                                </th>
                                <th
                                    class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">
                                    時間</th>
                                <th v-for="ds in chartData.datasets" :key="ds.label"
                                    class="h-12 px-4 text-right align-middle font-medium text-slate-500">
                                    {{ ds.label }}<br>
                                    <span class="text-xs text-slate-400 font-normal">
                                        {{ ds.label.includes('(Comp)') || ds.stack === 'Stack 1' ?
                                            getHeaderSubDate(compareDate,
                                        true) : getHeaderSubDate(currentDate, true) }}
                                    </span>
                                    <span v-if="ds.yAxisID === 'y1'"
                                        class="text-[10px] ml-1 text-slate-400 font-normal">(右軸)</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0">
                            <tr v-for="(label, idx) in chartData.labels" :key="idx"
                                class="border-b transition-colors hover:bg-slate-50">
                                <td class="p-4 align-middle font-medium text-slate-900 bg-slate-50/30">{{
                                    getRowDateDisplay(label) }}</td>
                                <td class="p-4 align-middle text-slate-500 bg-slate-50/30">{{ getRowTimeDisplay(label)
                                    }}</td>
                                <td v-for="ds in chartData.datasets" :key="ds.label"
                                    class="p-4 align-middle text-right tabular-nums">{{ formatNumber(ds.data[idx]) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="!chartData.labels.length" class="p-10 text-center text-slate-500">データがありません</div>
                </div>

                <div class="p-4 border-t bg-slate-50 flex justify-end rounded-b-lg">
                    <button @click="isDataTableOpen = false"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 h-9 px-4 py-2">閉じる</button>
                </div>
            </div>
        </div>
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
    Table as TableIcon,
    Download as DownloadIcon
} from 'lucide-vue-next';

// Use Vue ChartJS wrappers
import LineChart from './components/charts/type/Line.vue';
import BarChart from './components/charts/type/Bar.vue';

// --- PROPS DEFINITION ---
// Nhận treeData từ component cha (App.vue hoặc Layout) để tránh fetch lại
const props = defineProps({
    treeData: {
        type: Array,
        default: () => []
    }
});

const route = useRoute();

// --- STATE ---
const activeTab = ref('period');
const currentPeriodType = ref('day');
const currentDate = ref(new Date());
const pickerValue = ref(format(new Date(), 'yyyy-MM-dd'));
const compareDate = ref(null);
const comparePickerValue = ref('');
const showTarget = ref(true);
const shopDisplayType = ref('cost');
const isLoading = ref(false);

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

const isAxisModalOpen = ref(false);
const isDataTableOpen = ref(false);
const axisSettings = reactive({
    yLeftMin: '', yLeftMax: '', yLeftStepSize: '',
    yRightMin: '', yRightMax: '', yRightStepSize: ''
});
const chartData = ref({ labels: [], datasets: [] });

// --- COMPUTED: HELPERS & LOGIC ---

const yearRange = computed(() => {
    const currentYear = new Date().getFullYear();
    const startYear = currentYear - 10;
    const endYear = currentYear + 5;
    const years = [];
    for (let i = startYear; i <= endYear; i++) years.push(i);
    return years.sort((a, b) => b - a);
});

// Logic tìm Path Node đệ quy: Trả về mảng tên [Cha, Con, Cháu]
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

// Title Path: 1ライン_工場_電気
const processName = computed(() => {
    const targetId = route.query.equipment || route.query.utility || route.query.facility;
    // Sử dụng props.treeData thay vì treeData local
    if (!targetId || props.treeData.length === 0) return '全工程';
    const pathArray = findPathToNode(props.treeData, targetId);
    return pathArray ? pathArray.join('_') : '全工程';
});

const chartVisibility = computed(() => {
    const hasFacility = !!route.query.facility;
    const hasUtility = !!route.query.utility;
    const hasEquipment = !!route.query.equipment;
    const tab = activeTab.value;

    if (tab === 'period') {
        if (hasUtility || hasEquipment) return { show: true };
        return { show: false, msg: '使用量推移を表示するには、サイドバーからユーティリティまたは設備を選択してください。' };
    }
    if (tab === 'comparison') {
        if (hasUtility) return { show: true };
        return { show: false, msg: '設備比較を表示するには、サイドバーからユーティリティを選択してください。' };
    }
    if (tab === 'shop') {
        if (hasFacility || hasUtility || hasEquipment) return { show: true };
        return { show: false, msg: 'コスト/CO2を表示するには、サイドバーから工場、ユーティリティ、または設備を選択してください。' };
    }
    return { show: true };
});

const shouldShowTarget = computed(() => {
    const isShopEquipment = activeTab.value === 'shop' && !!route.query.equipment;
    return activeTab.value === 'period' || isShopEquipment;
});

const shouldUseBarChart = computed(() => activeTab.value === 'comparison' || activeTab.value === 'shop');

const getChartTitle = computed(() => {
    const fmt = (d, f) => {
        if (!d) return '';
        try { return format(d, f, { locale: ja }); } catch (e) { return ''; }
    };

    const getWeekRange = (dateObj) => {
        if (!dateObj) return '';
        const start = startOfWeek(dateObj, { weekStartsOn: 1 });
        const end = endOfWeek(dateObj, { weekStartsOn: 1 });
        return `${fmt(dateObj, "yyyy")} (${fmt(start, "dd/MM")} ～ ${fmt(end, "dd/MM")})`;
    };

    switch (activeTab.value) {
        case 'period': {
            const prefix = currentPeriodType.value === 'day' ? '期間（日）'
                : currentPeriodType.value === 'week' ? '期間（週）'
                    : currentPeriodType.value === 'month' ? '期間（月）' : '期間（年）';
            let mainDateStr = '', compareDateStr = '';

            if (currentPeriodType.value === 'day') mainDateStr = fmt(currentDate.value, "yyyy/MM/dd");
            else if (currentPeriodType.value === 'week') mainDateStr = getWeekRange(currentDate.value);
            else if (currentPeriodType.value === 'month') mainDateStr = fmt(currentDate.value, "yyyy/MM");
            else mainDateStr = fmt(currentDate.value, "yyyy");

            if (compareDate.value) {
                if (currentPeriodType.value === 'day') compareDateStr = fmt(compareDate.value, "yyyy/MM/dd");
                else if (currentPeriodType.value === 'week') compareDateStr = getWeekRange(compareDate.value);
                else if (currentPeriodType.value === 'month') compareDateStr = fmt(compareDate.value, "yyyy/MM");
                else compareDateStr = fmt(compareDate.value, "yyyy");
                return `${prefix}: ${mainDateStr} ~ ${compareDateStr}`;
            }
            return `${prefix}: ${mainDateStr}`;
        }
        case 'comparison': {
            const prefix = currentPeriodType.value === 'day' ? '設備比較（日）'
                : currentPeriodType.value === 'week' ? '設備比較（週）'
                    : currentPeriodType.value === 'month' ? '設備比較（月）' : '設備比較（年）';
            let mainDateStr = '';
            if (currentPeriodType.value === 'day') mainDateStr = fmt(currentDate.value, "yyyy/MM/dd");
            else if (currentPeriodType.value === 'week') mainDateStr = getWeekRange(currentDate.value);
            else if (currentPeriodType.value === 'month') mainDateStr = fmt(currentDate.value, "yyyy/MM");
            else mainDateStr = fmt(currentDate.value, "yyyy");
            return `${prefix}: ${mainDateStr}`;
        }
        case 'shop': {
            const typeLabel = shopDisplayTypes.find(t => t.value === shopDisplayType.value)?.label || 'コスト';
            const suffix = currentPeriodType.value === 'day' ? '（日）'
                : currentPeriodType.value === 'week' ? '（週）'
                    : currentPeriodType.value === 'month' ? '（月）' : '（年）';
            const prefix = `${typeLabel}${suffix}`;
            let mainDateStr = '', compareDateStr = '';

            if (currentPeriodType.value === 'day') mainDateStr = fmt(currentDate.value, "yyyy/MM/dd");
            else if (currentPeriodType.value === 'week') mainDateStr = getWeekRange(currentDate.value);
            else if (currentPeriodType.value === 'month') mainDateStr = fmt(currentDate.value, "yyyy/MM");
            else mainDateStr = fmt(currentDate.value, "yyyy");

            if (compareDate.value) {
                if (currentPeriodType.value === 'day') compareDateStr = fmt(compareDate.value, "yyyy/MM/dd");
                else if (currentPeriodType.value === 'week') compareDateStr = getWeekRange(compareDate.value);
                else if (currentPeriodType.value === 'month') compareDateStr = fmt(compareDate.value, "yyyy/MM");
                else compareDateStr = fmt(compareDate.value, "yyyy");
                return `${prefix}: ${mainDateStr} ~ ${compareDateStr}`;
            }
            return `${prefix}: ${mainDateStr}`;
        }
        default: return '';
    }
});

const getUnitLabel = () => {
    if (activeTab.value === 'shop') return shopDisplayType.value.includes('cost') ? '円' : 'kg-CO2';
    return 'kWh';
};

const getCurrentUnit = computed(() => {
    if (activeTab.value === 'shop') {
        if (shopDisplayType.value.includes('cost')) return '円';
        if (shopDisplayType.value.includes('co2')) return 'kg-CO2';
        return '';
    }
    return 'kWh';
});

const periodLabelText = computed(() => {
    switch (currentPeriodType.value) {
        case 'day': return '期間: 日別';
        case 'week': return '期間: 週別';
        case 'month': return '期間: 月別';
        case 'year': return '期間: 年別';
        default: return '期間';
    }
});

// --- CHART OPTIONS ---
const chartOptions = computed(() => {
    const isShop = activeTab.value === 'shop';
    const isStacked = isShop;

    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                position: 'bottom',
                labels: { usePointStyle: true, boxWidth: 8 }
            },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                titleColor: '#1e293b', bodyColor: '#475569', borderColor: '#e2e8f0',
                borderWidth: 1, padding: 10,
                callbacks: {
                    label: function (context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
                        if (context.parsed.y !== null) label += Math.round(context.parsed.y).toLocaleString();
                        return label;
                    }
                }
            }
        },
        scales: {
            x: {
                stacked: isStacked,
                grid: { display: false },
                ticks: {
                    font: { size: 11 },
                    autoSkip: false,
                    maxRotation: 0,
                    callback: function (val, index) {
                        if (activeTab.value === 'period' && currentPeriodType.value === 'week') {
                            return index % 24 === 0 ? this.getLabelForValue(val) : '';
                        }
                        return this.getLabelForValue(val);
                    }
                }
            },
            y: {
                stacked: isStacked,
                beginAtZero: true,
                position: 'left',
                title: { display: true, text: getUnitLabel(), font: { weight: 'bold' } },
                min: axisSettings.yLeftMin ? Number(axisSettings.yLeftMin) : undefined,
                max: axisSettings.yLeftMax ? Number(axisSettings.yLeftMax) : undefined,
                ticks: { stepSize: axisSettings.yLeftStepSize ? Number(axisSettings.yLeftStepSize) : undefined },
                grid: { color: '#f1f5f9' }
            },
            y1: {
                display: isShop && !shopDisplayType.value.includes('per_unit'),
                position: 'right',
                beginAtZero: true,
                grid: { drawOnChartArea: false },
                title: { display: true, text: '生産台数 (台)', font: { weight: 'bold' } },
                min: axisSettings.yRightMin ? Number(axisSettings.yRightMin) : undefined,
                max: axisSettings.yRightMax ? Number(axisSettings.yRightMax) : undefined,
                ticks: { stepSize: axisSettings.yRightStepSize ? Number(axisSettings.yRightStepSize) : undefined }
            }
        }
    };
});

// --- DATE HELPERS ---
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

const formatNumber = (val) => {
    if (val === undefined || val === null) return '-';
    return Math.round(val).toLocaleString();
};

const getDatasetValue = (type, index) => {
    if (type === '実績') {
        const ds = chartData.value.datasets.find(d => d.label === '実績');
        return ds ? ds.data[index] : null;
    } else {
        const ds = chartData.value.datasets.find(d => d.label !== '実績' && d.label !== '目標');
        return ds ? ds.data[index] : null;
    }
};

const getHeaderSubDate = (dateObj, isShop) => {
    if (!dateObj) return '';
    try {
        if (currentPeriodType.value === 'day') return format(dateObj, 'yyyy/MM/dd');
        if (currentPeriodType.value === 'week') return `${format(dateObj, 'yyyy')}/${getWeek(dateObj)}週`;
        if (currentPeriodType.value === 'month') return format(dateObj, 'yyyy/MM');
        if (currentPeriodType.value === 'year') return format(dateObj, 'yyyy');
    } catch (e) { return ''; }
    return '';
};

const getRowDateDisplay = (label) => {
    if (currentPeriodType.value === 'day') return '-';
    if (currentPeriodType.value === 'week') return label;
    if (currentPeriodType.value === 'month') return label;
    if (currentPeriodType.value === 'year') return label;
    return label;
};

const getRowTimeDisplay = (label) => {
    if (currentPeriodType.value === 'day') return label;
    if (currentPeriodType.value === 'week') return '00:00';
    return '-';
};

// --- DATA MOCK/GENERATION ---
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

        if (activeTab.value === 'period') {
            labels = [];
            dataCount = 168; // 7 * 24
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

    const datasets = [];

    // 1. PERIOD TAB (Line Chart)
    if (activeTab.value === 'period') {
        const actualColor = 'hsl(217, 91%, 60%)';
        datasets.push({
            label: '実績',
            data: Array.from({ length: dataCount }, () => Math.random() * 50 + 30),
            borderColor: actualColor,
            borderWidth: 2,
            fill: false,
            tension: 0.1,
            pointStyle: 'circle', pointRadius: 4, pointBorderWidth: 2, pointBackgroundColor: '#ffffff', pointBorderColor: actualColor,
            pointHoverRadius: 6, pointHoverBorderWidth: 3, pointHoverBackgroundColor: '#ffffff',
        });

        if (compareDate.value) {
            const compareColor = 'hsl(142, 71%, 45%)';
            datasets.push({
                label: getDateDisplay(compareDate.value, currentPeriodType.value),
                data: Array.from({ length: dataCount }, () => Math.random() * 50 + 20),
                borderColor: compareColor,
                borderWidth: 2,
                fill: false,
                tension: 0.1,
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

    // 2. COMPARISON TAB (Bar Chart)
    else if (activeTab.value === 'comparison') {
        const equipments = ['EQ-001', 'EQ-002', 'EQ-003', 'EQ-004'];
        labels = equipments;

        datasets.push({
            label: '実績',
            data: equipments.map(() => Math.random() * 100 + 50),
            backgroundColor: 'hsl(217, 91%, 60%)',
        });
    }

    // 3. SHOP TAB (Stacked Bar + Line)
    else if (activeTab.value === 'shop') {
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
                type: 'line',
                label: '生産計画',
                data: Array.from({ length: dataCount }, () => Math.random() * 500 + 200),
                borderColor: planColor,
                borderWidth: 2,
                fill: false,
                yAxisID: 'y1',
                order: 0,
                pointStyle: 'circle', pointRadius: 4, pointBorderWidth: 2, pointBackgroundColor: '#ffffff', pointBorderColor: planColor,
                pointHoverRadius: 6, pointHoverBorderWidth: 3, pointHoverBackgroundColor: '#ffffff',
            });
        }
    }

    chartData.value = { labels, datasets };
    isLoading.value = false;
};

// --- HANDLERS ---
const updateDate = (val, target) => {
    let d = new Date();
    if (!val) d = null;
    else if (currentPeriodType.value === 'week' && val.includes('-W')) {
        d = parseISO(val.substring(0, 4) + '-01-01');
    } else if (currentPeriodType.value === 'year') {
        d = new Date(parseInt(val.substring(0, 4)), 0, 1);
    } else {
        d = new Date(val);
    }

    if (target === 'date') { currentDate.value = d; pickerValue.value = val; }
    else { compareDate.value = d; comparePickerValue.value = val; }
};

const clearCompare = () => { compareDate.value = null; comparePickerValue.value = ''; };
const applyAxisSettings = () => { isAxisModalOpen.value = false; };

const downloadCSV = () => {
    if (!chartData.value.labels || chartData.value.labels.length === 0) return;
    const headers = ['時間/項目', ...chartData.value.datasets.map(ds => ds.label)];
    const rows = chartData.value.labels.map((label, index) => {
        const rowData = [label];
        chartData.value.datasets.forEach(ds => {
            const val = ds.data[index];
            rowData.push(val !== null && val !== undefined ? val : '');
        });
        return rowData;
    });
    const csvContent = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
    const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    const timestamp = format(new Date(), 'yyyyMMdd_HHmmss');
    link.setAttribute("href", url);
    link.setAttribute("download", `chart_data_${timestamp}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Đã loại bỏ hàm fetchTreeData thừa vì dữ liệu được truyền qua props.treeData

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