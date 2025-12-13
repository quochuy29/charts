import { createRouter, createWebHistory } from 'vue-router';
import Login from '../page/auth/login.vue';

const routes = [
    { path: '/login', name: 'Login', component: Login },
    // Lazy load các trang mới
    { path: '/dashboard', name: 'dashboard', component: () => import('../page/Dashboard.vue') },
    { path: '/user-management', name: 'user-management', component: () => import('../page/UserManagement.vue') },
    { path: '/production-planning', name: 'production-planning', component: () => import('../page/ProductionPlanning.vue') },
    { path: '/data-maintenance', name: 'data-maintenance', component: () => import('../page/DataMaintenance.vue') },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// --- NAVIGATION GUARD ---
router.beforeEach((to, from, next) => {
    // 1. Lấy dữ liệu từ LocalStorage
    let isAuthenticated = localStorage.getItem('isAuthenticated') === 'true';
    const isPersistent = localStorage.getItem('isPersistent') === 'true'; 
    const sessionExpiry = localStorage.getItem('sessionExpiry');
    const now = new Date().getTime();

    // 2. KIỂM TRA HẾT HẠN (Tự động Logout nếu hết hạn trước khi xử lý chuyển trang)
    // Nếu đang là "đã đăng nhập" NHƯNG không phải "vô hạn" VÀ thời gian hiện tại > thời gian hết hạn
    if (isAuthenticated && !isPersistent && sessionExpiry && now > parseInt(sessionExpiry)) {
        // Xóa sạch dữ liệu đăng nhập
        localStorage.removeItem('isAuthenticated');
        localStorage.removeItem('isPersistent');
        localStorage.removeItem('sessionExpiry');
        localStorage.removeItem('userRole'); // Xóa các info khác nếu có
        
        isAuthenticated = false; // Cập nhật lại biến cờ để dùng cho logic bên dưới
    }

    // 3. XỬ LÝ LOGIC ĐIỀU HƯỚNG

    // TRƯỜNG HỢP A: User muốn vào trang Login
    if (to.name === 'Login') {
        if (isAuthenticated) {
            // Nếu đã login và còn hạn -> KHÔNG CHO VÀO LOGIN -> Đẩy về Dashboard
            return next({ name: 'dashboard' });
        } else {
            // Nếu chưa login hoặc đã hết hạn -> Cho phép vào Login
            return next();
        }
    }

    // TRƯỜNG HỢP B: User muốn vào các trang nội bộ (Dashboard, UserManagement...)
    if (to.name !== 'Login') {
        if (!isAuthenticated) {
            // Nếu chưa login -> Đẩy về Login
            return next({ name: 'Login' });
        } else {
            // Nếu đã login -> Cho phép đi tiếp
            return next();
        }
    }

    next();
});

export default router;