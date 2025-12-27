<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0" @click.self="close">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[85vh] flex flex-col animate-in zoom-in-95 duration-200 sm:rounded-lg border border-slate-200">
            
            <div class="flex flex-col space-y-1.5 p-6 border-b">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold leading-none tracking-tight text-lg">データ詳細: {{ processName }}（{{ unit }}）</h3>
                        <p class="text-sm text-slate-500 mt-2 font-medium">{{ periodLabelText }}</p>
                    </div>
                    <button @click="close" class="rounded-sm opacity-70 ring-offset-white transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:ring-offset-2">
                        <component :is="XIcon" class="h-5 w-5" />
                    </button>
                </div>
                <div class="pt-2">
                    <button @click="downloadCSV" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 h-8 px-3 gap-2 shadow-sm">
                        <component :is="DownloadIcon" class="h-3.5 w-3.5" /> CSV出力
                    </button>
                </div>
            </div>

            <div class="p-0 overflow-auto flex-1 relative">
                
                <table v-if="activeTab === 'period'" class="w-full text-sm text-left caption-bottom">
                    <thead class="[&_tr]:border-b sticky top-0 bg-white z-10 shadow-sm">
                        <tr class="border-b transition-colors hover:bg-slate-100/50">
                            <th class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">
                                {{ currentPeriodType === 'week' ? '曜日' : '日付' }}
                            </th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">時間</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-slate-500">
                                {{ processName }}<br>
                                <span class="text-xs text-slate-400 font-normal">{{ getHeaderSubDate(currentDate, false) }}</span>
                            </th>
                            <th v-if="compareDate" class="h-12 px-4 text-right align-middle font-medium text-slate-500">
                                {{ processName }}<br>
                                <span class="text-xs text-slate-400 font-normal">{{ getHeaderSubDate(compareDate, false) }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <tr v-for="(label, idx) in chartData.labels" :key="idx" class="border-b transition-colors hover:bg-slate-50">
                            <td class="p-4 align-middle font-medium text-slate-900 bg-slate-50/30">{{ getRowDateDisplay(label) }}</td>
                            <td class="p-4 align-middle text-slate-500 bg-slate-50/30">
                                {{ currentPeriodType === 'week' ? String(idx % 24).padStart(2, '0') + ':00' : getRowTimeDisplay(label) }}
                            </td>
                            <td class="p-4 align-middle text-right tabular-nums">{{ formatNumber(getDatasetValue('実績', idx)) }}</td>
                            <td v-if="compareDate" class="p-4 align-middle text-right tabular-nums">{{ formatNumber(getDatasetValue('compare', idx)) }}</td>
                        </tr>
                    </tbody>
                </table>

                <table v-else-if="activeTab === 'comparison'" class="w-full text-sm text-left caption-bottom">
                    <thead class="[&_tr]:border-b sticky top-0 bg-white z-10 shadow-sm">
                        <tr class="border-b transition-colors hover:bg-slate-100/50">
                            <template v-if="currentPeriodType === 'week'">
                                <th class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">曜日</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">時間</th>
                            </template>
                            <template v-else>
                                <th class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[150px] bg-slate-50/50">
                                    {{ currentPeriodType === 'day' ? '時間' : '日付' }}
                                </th>
                            </template>
                            <th v-for="ds in chartData.datasets" :key="ds.label" class="h-12 px-4 text-right align-middle font-medium text-slate-500">{{ ds.label }}</th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <tr v-for="(label, idx) in chartData.labels" :key="idx" class="border-b transition-colors hover:bg-slate-50">
                            <template v-if="currentPeriodType === 'week'">
                                <td class="p-4 align-middle font-medium text-slate-900 bg-slate-50/30">{{ label }}</td>
                                <td class="p-4 align-middle text-slate-500 bg-slate-50/30">00:00</td>
                            </template>
                            <template v-else>
                                <td class="p-4 align-middle font-medium text-slate-900 bg-slate-50/30">{{ label }}</td>
                            </template>
                            <td v-for="ds in chartData.datasets" :key="ds.label" class="p-4 align-middle text-right tabular-nums">{{ formatNumber(ds.data[idx]) }}</td>
                        </tr>
                    </tbody>
                </table>

                <table v-else class="w-full text-sm text-left caption-bottom">
                    <thead class="[&_tr]:border-b sticky top-0 bg-white z-10 shadow-sm">
                        <tr class="border-b transition-colors hover:bg-slate-100/50">
                            <th class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">
                                {{ currentPeriodType === 'week' ? '曜日' : '日付' }}
                            </th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-slate-500 w-[100px] bg-slate-50/50">時間</th>
                            <th v-for="ds in chartData.datasets" :key="ds.label" class="h-12 px-4 text-right align-middle font-medium text-slate-500">
                                {{ ds.label }}<br>
                                <span class="text-xs text-slate-400 font-normal">
                                    {{ ds.label.includes('(Comp)') || ds.stack === 'Stack 1' ? getHeaderSubDate(compareDate, true) : getHeaderSubDate(currentDate, true) }}
                                </span>
                                <span v-if="ds.yAxisID === 'y1'" class="text-[10px] ml-1 text-slate-400 font-normal">(右軸)</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <tr v-for="(label, idx) in chartData.labels" :key="idx" class="border-b transition-colors hover:bg-slate-50">
                            <td class="p-4 align-middle font-medium text-slate-900 bg-slate-50/30">{{ getRowDateDisplay(label) }}</td>
                            <td class="p-4 align-middle text-slate-500 bg-slate-50/30">{{ getRowTimeDisplay(label) }}</td>
                            <td v-for="ds in chartData.datasets" :key="ds.label" class="p-4 align-middle text-right tabular-nums">{{ formatNumber(ds.data[idx]) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="!chartData.labels.length" class="p-10 text-center text-slate-500">データがありません</div>
            </div>

            <div class="p-4 border-t bg-slate-50 flex justify-end rounded-b-lg">
                <button @click="close" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 h-9 px-4 py-2">閉じる</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { format, getWeek } from 'date-fns';
import { ja } from 'date-fns/locale';
import { X as XIcon, Download as DownloadIcon } from 'lucide-vue-next';

const props = defineProps({
    isOpen: Boolean,
    chartData: Object,
    activeTab: String,
    currentPeriodType: String,
    processName: String,
    unit: String,
    currentDate: Date,
    compareDate: Date
});

const emit = defineEmits(['close']);

const close = () => emit('close');

// --- HELPERS ---
const periodLabelText = computed(() => {
    switch (props.currentPeriodType) {
        case 'day': return '期間: 日別';
        case 'week': return '期間: 週別';
        case 'month': return '期間: 月別';
        case 'year': return '期間: 年別';
        default: return '期間';
    }
});

const formatNumber = (val) => {
    if (val === undefined || val === null) return '-';
    return Math.round(val).toLocaleString();
};

const getDatasetValue = (type, index) => {
    if (type === '実績') {
        const ds = props.chartData.datasets.find(d => d.label === '実績');
        return ds ? ds.data[index] : null;
    } else {
        const ds = props.chartData.datasets.find(d => d.label !== '実績' && d.label !== '目標');
        return ds ? ds.data[index] : null;
    }
};

const getHeaderSubDate = (dateObj, isShop) => {
    if (!dateObj) return '';
    try {
        if (props.currentPeriodType === 'day') return format(dateObj, 'yyyy/MM/dd');
        if (props.currentPeriodType === 'week') return `${format(dateObj, 'yyyy')}/${getWeek(dateObj)}週`;
        if (props.currentPeriodType === 'month') return format(dateObj, 'yyyy/MM');
        if (props.currentPeriodType === 'year') return format(dateObj, 'yyyy');
    } catch (e) { return ''; }
    return '';
};

const getRowDateDisplay = (label) => {
    if (props.currentPeriodType === 'day') return '-';
    if (props.currentPeriodType === 'week') return label;
    return label;
};

const getRowTimeDisplay = (label) => {
    if (props.currentPeriodType === 'day') return label;
    if (props.currentPeriodType === 'week') return '00:00';
    return '-';
};

const downloadCSV = () => {
    if (!props.chartData.labels || props.chartData.labels.length === 0) return;
    const headers = ['時間/項目', ...props.chartData.datasets.map(ds => ds.label)];
    const rows = props.chartData.labels.map((label, index) => {
        const rowData = [label];
        props.chartData.datasets.forEach(ds => {
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
</script>