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

router.beforeEach(async (to, from, next) => {
    const publicPages = ['Login'];
    const authRequired = !publicPages.includes(to.name);

    if (authRequired) {
        try {
            await axios.get('/api/get-user-login');
            next();
        } catch (error) {
            // Logic cũ giữ nguyên
            if (error.response && error.response.status === 401) {
                return next({ name: 'Login' });
            }
            return next({ name: 'Login' });
        }
    } else {
        // XỬ LÝ CHO TRANG LOGIN
        if (to.name === 'Login') {
            // [MỚI] Nếu URL có param ?logout=true thì bỏ qua check session
            if (to.query.logout) {
                return next(); 
            }

            try {
                // Chỉ check khi user truy cập trực tiếp (F5 hoặc gõ URL)
                await axios.get('/api/get-user-login');
                return next({ name: 'dashboard' });
            } catch (e) {
                next();
            }
        } else {
            next();
        }
    }
});

export default router;