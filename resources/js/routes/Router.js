import { createRouter, createWebHistory } from 'vue-router';
import Login from '../page/auth/login.vue';

const routes = [
    { path: '/login', name: 'Login', component: Login },
    // Lazy load các trang mới
    { path: '/home', name: 'home', component: () => import('../page/Home.vue') },
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
    // Check token
    const token = localStorage.getItem('access_token');
    
    // Logic check hết hạn client-side (Optional - vì API sẽ trả về 401 nếu hết hạn)
    // Nhưng nếu muốn UX tốt hơn thì check ở đây:
    const tokenExpiry = localStorage.getItem('tokenExpiry');
    if (token && tokenExpiry) {
        const now = new Date();
        const expiry = new Date(tokenExpiry);
        if (now > expiry) {
            localStorage.removeItem('access_token');
            localStorage.removeItem('tokenExpiry');
            return next({ name: 'Login' });
        }
    }

    // Redirect logic
    if (to.name === 'Login' && token) {
        return next({ name: 'dashboard' });
    }

    if (to.name !== 'Login' && !token) {
        return next({ name: 'Login' });
    }

    next();
});

export default router;