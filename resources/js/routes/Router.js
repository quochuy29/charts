import { createRouter, createWebHistory } from 'vue-router';
import Login from '../page/auth/login.vue';

const routes = [
    { path: '/login', name: 'Login', component: Login },
    { path: '/', redirect: '/dashboard' },
    
    // Lazy load các trang mới
    { path: '/dashboard', name: 'dashboard', component: () => import('../page/Dashboard.vue') },
    { path: '/user-management', name: 'user-management', component: () => import('../page/UserManagement.vue') },
    { path: '/production-planning', name: 'production-planning', component: () => import('../page/ProductionPlanning.vue') },
    { path: '/menu-settings', name: 'menu-settings', component: () => import('../page/MenuSettings.vue') },
    { path: '/data-maintenance', name: 'data-maintenance', component: () => import('../page/DataMaintenance.vue') },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// // Navigation Guard đơn giản
// router.beforeEach((to, from, next) => {
//     const isAuthenticated = localStorage.getItem('isAuthenticated');
//     if (to.name !== 'Login' && !isAuthenticated) next({ name: 'Login' });
//     else next();
// });

export default router;