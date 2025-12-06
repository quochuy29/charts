import { reactive } from 'vue';

// 1. Dữ liệu Menu (Reactive để trang MenuSettings có thể chỉnh sửa và Sidebar tự cập nhật)
export const mockMenuData = reactive([
    {
        id: 'f1', name: 'Nhà máy 1', type: 'factory', isVisible: true,
        children: [
            {
                id: 'l1', name: 'Dây chuyền lắp ráp A', type: 'line', isVisible: true,
                children: [
                    { id: 'p1', name: 'Hàn (Welding)', type: 'process', isVisible: true },
                    { id: 'p2', name: 'Sơn (Painting)', type: 'process', isVisible: true }
                ]
            }
        ]
    },
    {
        id: 'f2', name: 'Nhà máy 2', type: 'factory', isVisible: true,
        children: []
    }
]);

// 2. Dữ liệu Users
export const mockUsers = reactive([
    { id: '1', display_name: 'HuyPQ', email: 'huypq@vnext.vn', role: 'admin', created_at: '2025-01-01' },
    { id: '2', display_name: 'PhucND', email: 'phucnd@vnext.vn', role: 'user', created_at: '2025-01-01' },
    { id: '3', display_name: 'ThangNV', email: 'thangnv@vnext.vn', role: 'user', created_at: '2025-01-01' },
]);

// 3. Dữ liệu Kế hoạch sản xuất (Production Plans)
// Key là factoryId, Value là object chứa năm -> tháng -> dữ liệu
export const mockProductionPlans = reactive({
    'f1': {
        2025: {
            4: { units: 1200, notes: 'Bắt đầu năm tài chính' },
            5: { units: 1500, notes: '' },
            6: { units: 1400, notes: 'Bảo trì định kỳ' }
        }
    }
});

// Hàm helper để sinh dữ liệu biểu đồ
export const generateChartData = (period) => {
    const count = period === 'day' ? 24 : (period === 'month' ? 30 : 12);
    const labels = Array.from({ length: count }, (_, i) => i + 1);
    const dataActual = Array.from({ length: count }, () => Math.floor(Math.random() * 500) + 100);
    const dataTarget = Array.from({ length: count }, () => 400); // Mục tiêu cố định
    
    return {
        labels: labels,
        datasets: [
            {
                label: 'Thực tế (Actual)',
                backgroundColor: '#3b82f6', // blue-500
                borderColor: '#3b82f6',
                data: dataActual,
                fill: false,
                type: 'bar' // Mặc định là bar, có thể override ở component
            },
            {
                label: 'Mục tiêu (Target)',
                backgroundColor: '#ef4444', // red-500
                borderColor: '#ef4444',
                data: dataTarget,
                type: 'line', // Đường line mục tiêu
                borderDash: [5, 5],
                pointRadius: 0
            }
        ]
    };
};