<template>
    <div class="p-6 bg-gray-50 min-h-full font-sans text-gray-800">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">ホーム</h1>
                <p class="text-sm text-gray-500 mt-1">{{ formattedMonth }}のデータ</p>
            </div>
            <button @click="openSettings" class="p-2 rounded-full hover:bg-gray-200 text-gray-500 transition-colors">
                <Settings class="w-5 h-5" />
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div v-for="(graph, index) in displayGraphs" :key="index"
                class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col h-[350px]">
                <div class="mb-4">
                    <h3 class="font-bold text-lg text-gray-800 truncate">{{ graph.title }}</h3>
                </div>

                <div class="flex-1 relative w-full min-h-0">
                    <BarChart v-if="graph.type === 'bar'" :data="graph.data" :options="commonOptions" />
                    <LineChart v-else :data="graph.data" :options="commonOptions" />
                </div>
            </div>
        </div>

        <HomeSettingsModal 
            :is-open="showSettings"
            @close="showSettings = false"
            @saved="onSettingsSaved"
        />

    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { format, startOfMonth, endOfMonth, eachDayOfInterval } from "date-fns";
import { ja } from "date-fns/locale";
import { Settings } from 'lucide-vue-next';
import LineChart from './components/charts/type/Line.vue';
import BarChart from './components/charts/type/Bar.vue';
import HomeSettingsModal from './components/HomeSettingsModal.vue'; // Điều chỉnh đường dẫn nếu cần
import { fetchHomeSettings } from '../services/mockData';

// --- State ---
const currentMonth = ref(new Date());
const graphConfigs = ref([]); // Loaded from DB (mock)
const graphDataMap = ref({});

// --- Computed ---
const formattedMonth = computed(() => {
    return format(currentMonth.value, "yyyy年MM月", { locale: ja });
});

const displayGraphs = computed(() => {
    // If configs exist, map them to chart data structure
    if (graphConfigs.value.length > 0) {
        return graphConfigs.value
            .filter(config => config.sensor && config.graph_type)
            .map(config => {
                const title = `${config.sensor.display_label} - ${config.graph_type}`;
                const isBar = ["台当たりコスト", "台当たりCO2排出量", "設備別使用量比較"].includes(config.graph_type);

                return {
                    title,
                    type: isBar ? 'bar' : 'line',
                    data: convertToChartJsData(graphDataMap.value[config.graph_no] || [], isBar)
                };
            });
    }

    // Default Fallback Graphs
    const defaultItems = [
        { title: "エネルギー使用量 (kWh/m³)", type: "line" },
        { title: "コスト (円)", type: "line" },
        { title: "CO₂排出量 (kg)", type: "line" },
        { title: "設備別使用量比較", type: "bar" },
    ];

    return defaultItems.map(item => ({
        title: item.title,
        type: item.type,
        data: generateDefaultChartData(item.type)
    }));
});

// --- Options ---
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' },
        tooltip: {
            mode: 'index',
            intersect: false,
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: '#f3f4f6' }
        },
        x: {
            grid: { display: false }
        }
    }
};

// --- Methods ---
const openSettings = () => {
    showSettings.value = true;
};

// Mocking logic to generate random data (replacing React's generateDefaultData)
const generateDefaultChartData = (type) => {
    const start = startOfMonth(currentMonth.value);
    const end = endOfMonth(currentMonth.value);
    const days = eachDayOfInterval({ start, end });

    const labels = days.map(day => format(day, "d"));
    const actualData = days.map(() => Math.round((Math.random() * 200 + 100) * 100) / 100);
    const targetData = days.map(() => 150.0);

    return {
        labels,
        datasets: [
            {
                label: '実績',
                data: actualData,
                borderColor: type === 'bar' ? '#2563eb' : '#2563eb', // Chart-1 (blue)
                backgroundColor: type === 'bar' ? '#2563eb' : 'rgba(37, 99, 235, 0.1)',
                tension: 0.3,
                borderWidth: 2
            },
            {
                label: '目標',
                data: targetData,
                borderColor: '#ef4444', // Chart-2 (red)
                backgroundColor: '#ef4444',
                borderDash: [5, 5],
                tension: 0,
                borderWidth: 2,
                pointRadius: 0
            }
        ]
    };
};

// Helper to convert Recharts data format [{day, actual, target}] to Chart.js
const convertToChartJsData = (rawData, isBar) => {
    const labels = rawData.map(d => d.day);
    const actual = rawData.map(d => d.actual);
    const target = rawData.map(d => d.target);

    return {
        labels,
        datasets: [
            {
                label: '実績',
                data: actual,
                backgroundColor: isBar ? '#2563eb' : 'transparent',
                borderColor: '#2563eb',
                tension: 0.3
            },
            {
                label: '目標',
                data: target,
                borderColor: '#ef4444',
                borderDash: [5, 5],
                pointRadius: 0
            }
        ]
    };
};

const loadSettings = async () => {
    // TODO: Implement API call to Laravel backend
    // const response = await axios.get('/api/settings/home');
    // graphConfigs.value = response.data.graphs;
    // generateGraphData();

    // For now, we leave graphConfigs empty to trigger default view
    try {
        const data = await fetchHomeSettings();
        dashboardConfig.value = data.config;
        facilitiesMap.value = data.facilities;
        graphTypesMap.value = data.graphTypes;
    } catch (e) {
        console.error("Init error", e);
    }
};

// --- Logic mới cho Setting ---
const showSettings = ref(false);
const dashboardConfig = ref([]);
const facilitiesMap = ref([]);
const graphTypesMap = ref([]);

// Helpers để hiển thị tên ra ngoài dashboard (vì config chỉ lưu ID)
const getFacilityName = (id) => facilitiesMap.value.find(f => f.id === id)?.name || 'Unknown';
const getGraphTypeName = (id) => graphTypesMap.value.find(t => t.id === id)?.name || 'Unknown';

const onSettingsSaved = (newConfig) => {
    // Cập nhật lại cấu hình Dashboard ngay lập tức
    dashboardConfig.value = newConfig;
};

onMounted(() => {
    loadSettings();
});
</script>