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


export const mockMenuData = reactive([
    {
        id: 'f1', name: '第1工場', type: 'factory', isVisible: true,
        children: [
            {
                id: 'l1', name: '組立ラインA', type: 'line', isVisible: true,
                children: [
                    { id: 'p1', name: '溶接工程', type: 'process', isVisible: true },
                    { id: 'p2', name: '塗装工程', type: 'process', isVisible: true }
                ]
            }
        ]
    },
    { id: 'f2', name: '第2工場', type: 'factory', isVisible: true, children: [] }
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

// 4. Charts
export const generateChartData = (period) => {
    const count = period === 'day' ? 24 : (period === 'month' ? 30 : 12);
    const labels = Array.from({ length: count }, (_, i) => i + 1);
    
    return {
        labels: labels,
        datasets: [
            {
                label: '実績 (Actual)',
                backgroundColor: '#ef4444', 
                borderColor: '#ef4444',
                data: Array.from({ length: count }, () => Math.floor(Math.random() * 500) + 100),
                borderWidth: 1
            },
            {
                label: '目標 (Target)',
                backgroundColor: '#3b82f6',
                borderColor: '#3b82f6',
                data: Array.from({ length: count }, () => 400),
                type: 'line',
                borderDash: [5, 5],
                pointRadius: 0
            }
        ]
    };
};