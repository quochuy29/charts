import { reactive } from 'vue';

// 1. Menu

export const initialTreeData = [
    {
        id: "factory-1",
        name: "Factory 1 - Main Production",
        type: "factory",
        children: [
            {
                id: "line-1",
                name: "Line 1 - Assembly",
                type: "line",
                children: [
                    {
                        id: "shop-1",
                        name: "Shop A - Welding",
                        type: "shop",
                        children: [
                            { id: "eq-1", name: "Welder Unit 01", type: "equipment" },
                            { id: "eq-2", name: "Welder Unit 02", type: "equipment" },
                            { id: "eq-3", name: "Transformer T-001", type: "equipment" }
                        ]
                    },
                    {
                        id: "shop-2",
                        name: "Shop B - Painting",
                        type: "shop",
                        children: [
                            { id: "eq-4", name: "Paint Booth 01", type: "equipment" },
                            { id: "eq-5", name: "Paint Booth 02", type: "equipment" },
                        ]
                    },
                ],
            },
        ],
    },
    {
        id: "factory-2",
        name: "Factory 2 - Processing",
        type: "factory",
        children: [],
    },
];


export const mockTreeData = reactive([
    {
        id: 'line-1', name: '第1ライン', type: 'line', isVisible: true,
        children: [
            {
                id: 'fac-1', name: '塗装工程', type: 'facility', isVisible: true,
                children: [
                    {
                        id: 'util-1', name: '電気', type: 'utility', isVisible: true,
                        children: [
                            { id: 'eq-1', name: '乾燥炉 A', type: 'equipment', isVisible: true },
                            { id: 'eq-2', name: '乾燥炉 B', type: 'equipment', isVisible: true }
                        ]
                    },
                    {
                        id: 'util-2', name: '水', type: 'utility', isVisible: true,
                        children: []
                    }
                ]
            },
            { id: 'fac-2', name: '組立工程', type: 'facility', isVisible: true, children: [] }
        ]
    },
    { id: 'line-2', name: '第2ライン', type: 'line', isVisible: true, children: [] }
]);

// 2. Users
export const mockUsers = reactive([
    { id: 'Admin', display_name: '管理者', email: 'admin@toyota-kyushu.com', role: 'admin', created_at: '2025-01-01' },
    { id: 'HuyPQ', display_name: '管理者', email: 'admin@toyota-kyushu.com', role: 'admin', created_at: '2025-01-01' },
    { id: 'PhucND', display_name: '管理者', email: 'admin@toyota-kyushu.com', role: 'admin', created_at: '2025-01-01' },
    { id: 'ThangNV', display_name: '管理者', email: 'admin@toyota-kyushu.com', role: 'admin', created_at: '2025-01-01' },
    { id: 'AnhNQ', display_name: '管理者', email: 'admin@toyota-kyushu.com', role: 'admin', created_at: '2025-01-01' },
    { id: 'NguyetLTA', display_name: '管理者', email: 'admin@toyota-kyushu.com', role: 'admin', created_at: '2025-01-01' },
    { id: 'ThinhLD', display_name: '管理者', email: 'admin@toyota-kyushu.com', role: 'admin', created_at: '2025-01-01' },
]);

// 3. Plans
export const mockProductionPlans = reactive({
    'f1': {
        2025: {
            4: { units: 1200, notes: '年度始め' },
            5: { units: 1500, notes: '' },
            6: { units: 1400, notes: '定期メンテナンス' }
        }
    }
});

// 3. Chart Data Generator (Update logic cho Comparison & Shop)
export const generateChartData = (period, unit, showTarget) => {
    // Logic sinh dữ liệu ngẫu nhiên (giữ nguyên logic cũ hoặc nâng cấp)
    // Trả về { labels, datasets }
    let count = 24;
    let labelPrefix = '';
    if (period === 'day') { count = 24; labelPrefix = ':00'; }
    else if (period === 'week') { count = 7; labelPrefix = '日'; }
    else if (period === 'month') { count = 31; labelPrefix = '日'; }
    else if (period === 'year') { count = 12; labelPrefix = '月'; }

    const labels = Array.from({ length: count }, (_, i) => `${i + 1}${labelPrefix}`);
    const data1 = Array.from({ length: count }, () => Math.floor(Math.random() * 100) + 50);
    
    const datasets = [
        {
            label: '実績 (Actual)',
            backgroundColor: '#2563eb', // Blue
            borderColor: '#2563eb',
            data: data1,
            tension: 0.3
        }
    ];

    if (showTarget) {
        datasets.push({
            label: '目標 (Target)',
            backgroundColor: '#ef4444', // Red
            borderColor: '#ef4444',
            data: data1.map(v => v * 1.2),
            borderDash: [5, 5],
            pointRadius: 0
        });
    }

    return { labels, datasets };
};
// resources/js/mockData.js

// --- PHẦN 1: CẤU HÌNH CỐ ĐỊNH (METADATA) ---

// 1. Danh sách thiết bị
const mockFacilities = [
  { id: 1, name: '1ライン > 工場' },
  { id: 2, name: '1ライン > 工場 > 電気' },
  { id: 3, name: '1ライン > 工場 > ガス > ガス' },
  { id: 4, name: '2ライン > 工場' },
  { id: 5, name: '2ライン > 工場 > 電気' },
];

// 2. Danh sách loại biểu đồ
const mockGraphTypes = [
  { id: 'cost_co2', name: 'コスト/CO2', chartType: 'bar' },
  { id: 'unit_co2', name: '台当たりCO2排出量', chartType: 'line' },
  { id: 'co2_emission', name: 'CO2排出量', chartType: 'bar' },
  { id: 'usage_trend', name: '使用量推移', chartType: 'line' },
];

// 3. Cấu hình mặc định của User (4 vị trí)
let userHomeConfig = [
  { positionId: 0, label: 'Graph#1: 左上', facilityId: 1, graphTypeId: 'cost_co2' },
  { positionId: 1, label: 'Graph#2: 右上', facilityId: 2, graphTypeId: 'unit_co2' },
  { positionId: 2, label: 'Graph#3: 左下', facilityId: 3, graphTypeId: 'co2_emission' },
  { positionId: 3, label: 'Graph#4: 右下', facilityId: 2, graphTypeId: 'usage_trend' },
];

// --- PHẦN 2: API GIẢ LẬP ---

export const fetchHomeSettings = () => {
  return new Promise((resolve) => {
    setTimeout(() => {
      resolve({
        facilities: mockFacilities,
        graphTypes: mockGraphTypes,
        config: JSON.parse(JSON.stringify(userHomeConfig))
      });
    }, 300);
  });
};

export const updateHomeSettings = (newConfig) => {
  return new Promise((resolve) => {
    setTimeout(() => {
      userHomeConfig = newConfig;
      console.log("MockAPI Saved:", newConfig);
      resolve({ success: true });
    }, 500);
  });
};

// --- PHẦN 3: DATA GENERATOR CHO CHART ---

/**
 * Hàm sinh dữ liệu ChartJS dựa trên loại biểu đồ được chọn
 * @param {String} typeId - ID của loại biểu đồ (vd: 'cost_co2')
 */
export const generateMockChartData = (typeId) => {
  const labels = Array.from({ length: 12 }, (_, i) => `${i + 1}月`); // 12 tháng
  
  // Hàm random số
  const rnd = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;

  let datasets = [];

  switch (typeId) {
    case 'cost_co2': // Bar Chart (2 cột)
      datasets = [
        {
          label: 'Cost (万円)',
          data: labels.map(() => rnd(100, 500)),
          backgroundColor: '#3b82f6', // blue-500
        },
        {
          label: 'CO2 (t)',
          data: labels.map(() => rnd(50, 200)),
          backgroundColor: '#10b981', // green-500
        }
      ];
      break;

    case 'unit_co2': // Line Chart
      datasets = [
        {
          label: 'kg-CO2/unit',
          data: labels.map(() => rnd(20, 80)),
          borderColor: '#f59e0b', // amber-500
          tension: 0.4,
          fill: false
        }
      ];
      break;

    case 'co2_emission': // Bar Chart (1 cột)
      datasets = [
        {
          label: 'CO2 Emission',
          data: labels.map(() => rnd(1000, 3000)),
          backgroundColor: '#6366f1', // indigo-500
        }
      ];
      break;
      
    case 'usage_trend': // Line Chart (Comparison)
      datasets = [
        {
          label: 'Actual',
          data: labels.map(() => rnd(800, 1200)),
          borderColor: '#2563eb', // blue-600
          tension: 0.3
        },
        {
          label: 'Target',
          data: labels.map(() => 1000), // Target đường thẳng
          borderColor: '#ef4444', // red-500
          borderDash: [5, 5],
          pointRadius: 0
        }
      ];
      break;

    default:
      datasets = [{ label: 'Data', data: [] }];
  }

  return { labels, datasets };
};

// resources/js/services/mockData.js
import { format, startOfWeek, addDays } from 'date-fns';
import { ja } from 'date-fns/locale';

// ... (Giữ nguyên các mock data cũ: mockTreeData, mockUsers...)

/**
 * --- PHẦN 5: DỮ LIỆU BIỂU ĐỒ DASHBOARD ---
 * Giả lập API trả về data cho Chart.js
 */

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

export const fetchDashboardChartData = ({ 
    activeTab, 
    periodType, 
    currentDate, 
    compareDate, 
    showTarget, 
    shopDisplayType 
}) => {
    return new Promise((resolve) => {
        setTimeout(() => {
            let labels = [];
            let datasets = [];
            
            // Hàm random số
            const rnd = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;

            // 1. Tạo Labels (Trục hoành)
            if (periodType === 'day') {
                labels = Array.from({ length: 24 }, (_, i) => `${i}:00`);
            } else if (periodType === 'week') {
                labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            } else if (periodType === 'month') {
                labels = Array.from({ length: 31 }, (_, i) => `${i + 1}`);
            } else {
                labels = Array.from({ length: 12 }, (_, i) => `${i + 1}月`);
            }
            
            // 2. Tạo Datasets theo Tab
            if (activeTab === 'comparison') {
                // --- Tab Comparison: Biểu đồ cột so sánh thiết bị ---
                labels = ['設備 A', '設備 B', '設備 C', '設備 D', '設備 E'];
                datasets = [{
                    label: '実績 (Actual)',
                    data: labels.map(() => rnd(100, 500)),
                    backgroundColor: '#2563eb',
                }];
                // Comparison thường không hiển thị Target dạng Line, nhưng nếu cần có thể thêm ở đây
            } 
            else if (activeTab === 'shop') {
                // --- Tab Shop: Biểu đồ hỗn hợp (Stacked Bar + Line) ---
                
                // Cột 1: Điện
                datasets.push({
                    type: 'bar',
                    label: '電力 (Elec)',
                    data: labels.map(() => rnd(50, 200)),
                    backgroundColor: '#3b82f6', // blue
                    stack: 'Stack 0',
                    yAxisID: 'y'
                });

                // Cột 2: Gas
                datasets.push({
                    type: 'bar',
                    label: 'ガス (Gas)',
                    data: labels.map(() => rnd(20, 100)),
                    backgroundColor: '#f59e0b', // amber
                    stack: 'Stack 0',
                    yAxisID: 'y'
                });

                // Đường: Kế hoạch sản xuất (Plan) - Trục phải
                // Chỉ hiện khi KHÔNG phải chế độ 'per_unit' (theo source React)
                if (!shopDisplayType.includes('per_unit')) {
                    datasets.push({
                        type: 'line',
                        label: '生産計画 (Plan)',
                        data: labels.map(() => rnd(800, 1200)),
                        borderColor: '#10b981', // green
                        borderWidth: 2,
                        tension: 0.3,
                        yAxisID: 'y1', // Trục Y bên phải
                        pointStyle: 'circle',
                        pointRadius: 4,
                        pointBackgroundColor: '#fff'
                    });
                }

                // === FIX: THÊM LOGIC TARGET CHO TAB SHOP ===
                if (showTarget) {
                    datasets.push({
                        type: 'line',
                        label: '目標 (Target)',
                        // Tạo đường thẳng mục tiêu (giả lập giá trị cố định, ví dụ 350)
                        data: labels.map(() => 350), 
                        borderColor: '#ef4444', // red
                        borderDash: [5, 5],     // Nét đứt
                        pointRadius: 0,         // Không hiện chấm tròn
                        borderWidth: 1.5,
                        yAxisID: 'y',           // Gắn vào trục trái (cùng đơn vị với Bar)
                        order: 0                // Vẽ đè lên trên cùng
                    });
                }
            } 
            else {
                // --- Tab Period: Biểu đồ đường xu hướng (Usage Trend) ---
                
                // Đường chính (Hiện tại)
                datasets.push({
                    label: '実績 (Actual)',
                    data: labels.map(() => rnd(50, 150)),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.3,
                    fill: true
                });

                // Đường so sánh (Quá khứ)
                if (compareDate) {
                    const compareLabel = `比較 (${getDateDisplay(compareDate, periodType)})`;
                    datasets.push({
                        label: compareLabel,
                        data: labels.map(() => rnd(40, 140)),
                        borderColor: '#9333ea', // purple
                        borderDash: [5, 5],
                        tension: 0.3,
                        fill: false
                    });
                }

                // Đường mục tiêu (Target)
                if (showTarget) {
                    datasets.push({
                        type: 'line',
                        label: '目標 (Target)',
                        data: labels.map(() => 130),
                        borderColor: '#ef4444', // red
                        borderDash: [5, 5],
                        pointRadius: 0,
                        borderWidth: 1.5
                    });
                }
            }

            resolve({ labels, datasets });
        }, 300);
    });
};