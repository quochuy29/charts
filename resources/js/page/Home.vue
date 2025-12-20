<template>
    <div class="p-6 bg-gray-50 min-h-full font-sans text-gray-800">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">ホーム (Home)</h1>
                <p class="text-sm text-gray-500 mt-1">{{ formattedMonth }}のデータ</p>
            </div>
            
            <button @click="openSettings" class="flex items-center gap-2 px-4 py-2 bg-white border rounded-lg hover:bg-gray-100 shadow-sm transition-colors text-sm font-medium text-gray-700">
                <Settings class="w-4 h-4" />
                表示設定
            </button>
        </div>

        <div v-if="isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-[calc(100vh-150px)]">
            <div v-for="i in 4" :key="i" class="bg-white rounded-xl shadow-sm border p-4 animate-pulse">
                <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
                <div class="h-full bg-gray-100 rounded"></div>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-10">
            <div 
                v-for="(chartItem, index) in renderedCharts" 
                :key="index"
                class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col h-[400px]"
            >
                <div class="mb-4 flex justify-between items-start border-b pb-2">
                    <div>
                        <span class="text-xs text-gray-400 font-mono block">{{ chartItem.positionLabel }}</span>
                        <h3 class="font-bold text-lg text-gray-800 truncate" :title="chartItem.title">
                            {{ chartItem.title }}
                        </h3>
                    </div>
                    <span class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded">
                        {{ chartItem.type === 'bar' ? 'Bar Chart' : 'Line Chart' }}
                    </span>
                </div>

                <div class="flex-1 relative w-full min-h-0">
                    <BarChart 
                        v-if="chartItem.type === 'bar'" 
                        :data="chartItem.data" 
                        :options="commonOptions" 
                    />
                    <LineChart 
                        v-else 
                        :data="chartItem.data" 
                        :options="commonOptions" 
                    />
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
import { format } from "date-fns";
import { ja } from "date-fns/locale";
import { Settings } from 'lucide-vue-next';

// Imports Components
import LineChart from './components/charts/type/Line.vue';
import BarChart from './components/charts/type/Bar.vue';
import HomeSettingsModal from './components/HomeSettingsModal.vue';

// Imports Data Services
import { fetchHomeSettings, generateMockChartData } from '../services/mockData';

// --- State ---
const currentMonth = ref(new Date());
const showSettings = ref(false);
const isLoading = ref(true);

// Dữ liệu cấu hình lấy từ API
const dashboardConfig = ref([]);
const facilitiesMap = ref([]);
const graphTypesMap = ref([]);

// --- Computed Properties ---
const formattedMonth = computed(() => {
    return format(currentMonth.value, "yyyy年MM月", { locale: ja });
});

/**
 * Biến đổi từ dashboardConfig (IDs) -> Dữ liệu hiển thị (Data, Title, Type)
 */
const renderedCharts = computed(() => {
    return dashboardConfig.value.map(slot => {
        // 1. Tìm tên thiết bị & loại biểu đồ
        const facility = facilitiesMap.value.find(f => f.id === slot.facilityId);
        const graphType = graphTypesMap.value.find(t => t.id === slot.graphTypeId);

        // 2. Tên hiển thị
        const facilityName = facility ? facility.name : '未設定';
        const graphName = graphType ? graphType.name : '';
        const title = `${facilityName} - ${graphName}`;

        // 3. Xác định loại chart (Bar/Line) dựa trên metadata của mockData
        // Mặc định là 'bar' nếu không tìm thấy
        const chartType = graphType?.chartType || 'bar';

        // 4. Sinh dữ liệu giả (Fake Data)
        const chartData = generateMockChartData(slot.graphTypeId);

        return {
            positionLabel: slot.label, // Graph#1: 左上
            title: title,
            type: chartType,
            data: chartData
        };
    });
});

// --- Chart Options (Dùng chung) ---
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

// Hàm khởi tạo: Load settings từ mockData
const loadSettings = async () => {
    isLoading.value = true;
    try {
        const data = await fetchHomeSettings();
        if (data) {
            dashboardConfig.value = data.config;
            facilitiesMap.value = data.facilities;
            graphTypesMap.value = data.graphTypes;
        }
    } catch (e) {
        console.error("Lỗi tải settings:", e);
    } finally {
        isLoading.value = false;
    }
};

// Callback khi user bấm Save ở Modal
const onSettingsSaved = (newConfig) => {
    console.log("Cập nhật config mới:", newConfig);
    // Cập nhật state, Vue sẽ tự động tính toán lại `renderedCharts` nhờ Computed
    dashboardConfig.value = newConfig;
};

// --- Lifecycle ---
onMounted(() => {
    loadSettings();
});
</script>