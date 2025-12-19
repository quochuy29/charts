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

// 1. Danh sách thiết bị (Cột bên trái)
const mockFacilities = [
  { id: 1, name: '1ライン > 工場' },
  { id: 2, name: '1ライン > 工場 > 電気' },
  { id: 3, name: '1ライン > 工場 > ガス > ガス' },
  { id: 4, name: '2ライン > 工場' },
  { id: 5, name: '2ライン > 工場 > 電気' },
];

// 2. Danh sách loại biểu đồ (Cột bên phải)
const mockGraphTypes = [
  { id: 'cost_co2', name: 'コスト/CO2' },
  { id: 'unit_co2', name: '台当たりCO2排出量' }, // Lượng thải CO2 trên mỗi đơn vị
  { id: 'co2_emission', name: 'CO2排出量' },      // Lượng thải CO2
  { id: 'usage_trend', name: '使用量推移' },      // Xu hướng sử dụng
];

// 3. Cấu hình hiện tại của User (4 vị trí)
// Position: 0=TopLeft, 1=TopRight, 2=BottomLeft, 3=BottomRight
let userHomeConfig = [
  { positionId: 0, label: 'Graph#1: 左上', facilityId: 1, graphTypeId: 'cost_co2' },
  { positionId: 1, label: 'Graph#2: 右上', facilityId: 2, graphTypeId: 'unit_co2' },
  { positionId: 2, label: 'Graph#3: 左下', facilityId: 3, graphTypeId: 'co2_emission' },
  { positionId: 3, label: 'Graph#4: 右下', facilityId: 2, graphTypeId: 'usage_trend' },
];

// API: Lấy danh sách options và config hiện tại
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

// API: Lưu config
export const updateHomeSettings = (newConfig) => {
  return new Promise((resolve) => {
    setTimeout(() => {
      userHomeConfig = newConfig; // Lưu vào biến tạm
      console.log("MockAPI Saved:", newConfig);
      resolve({ success: true });
    }, 500);
  });
};