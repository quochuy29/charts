import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // QUAN TRỌNG: Để gửi cookie session/remember_token
window.axios.defaults.baseURL = 'http://localhost:8080'; // URL Backend của bạn

// Add a response interceptor
window.axios.interceptors.response.use(
    response => {
        // (Tùy chọn) Sliding Session:
        // Nếu backend cấu hình session gia hạn khi có activity, 
        // ta có thể update lại sessionExpiry ở đây mỗi khi gọi API thành công.
        // if (localStorage.getItem('sessionExpiry')) { ... update time ... }
        return response;
    },
    error => {
        if (error.response && error.response.status === 401) {
            // 401 Unauthorized -> Token/Session hết hạn từ phía Server
            localStorage.removeItem('isAuthenticated');
            localStorage.removeItem('sessionExpiry');
            
            // Redirect về login
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);