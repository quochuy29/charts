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
            // Gọi API check user session.
            // Nếu session còn sống -> 200 OK -> Next
            // Nếu session hết hạn -> 401 Unauthorized -> Catch -> Redirect Login
            
            // Lưu ý: Để tối ưu performance, bạn nên lưu user vào Store (Pinia/Vuex) 
            // và chỉ gọi API này 1 lần khi reload trang (App mount). 
            // Tuy nhiên, để đảm bảo an toàn tuyệt đối theo yêu cầu "hết session load page sẽ logout",
            // gọi trực tiếp hoặc bắt lỗi 401 từ Interceptor là cách chắc chắn nhất.
            
            await axios.get('/api/get-user-login');
            next();
        } catch (error) {
            // Nếu lỗi 401 (Unauthorized) -> Session hết hạn hoặc chưa login
            if (error.response && error.response.status === 401) {
                return next({ name: 'Login' });
            }
            // Các lỗi khác vẫn cho next hoặc xử lý tùy ý (ví dụ mất mạng)
            // Ở đây an toàn nhất là về Login
             return next({ name: 'Login' });
        }
    } else {
        // Nếu đang ở trang Login mà đã có session -> đẩy vào Dashboard
        if (to.name === 'Login') {
            try {
                await axios.get('/api/get-user-login');
                return next({ name: 'dashboard' });
            } catch (e) {
                // Chưa login -> Ở lại trang Login
                next();
            }
        } else {
            next();
        }
    }
});

export default router;