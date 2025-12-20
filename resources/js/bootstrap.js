import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = 'http://localhost:8080'; // URL Backend của bạn

// --- THÊM INTERCEPTOR ---
// Tự động gắn token từ LocalStorage vào Header
// 1. Request Interceptor: Luôn gắn Access Token
// --- 1. Request Interceptor ---
window.axios.interceptors.request.use(
    (config) => {
        // QUAN TRỌNG: Nếu đang gọi API refresh-token, KHÔNG được ghi đè bằng access_token
        if (config.url && config.url.includes('/refresh-token')) {
            return config;
        }

        const token = localStorage.getItem('access_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Flag tránh loop
let isRefreshing = false;
let failedQueue = [];

const processQueue = (error, token = null) => {
    failedQueue.forEach(prom => {
        if (error) {
            prom.reject(error);
        } else {
            prom.resolve(token);
        }
    });
    failedQueue = [];
};

// --- 2. Response Interceptor ---
window.axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        // Nếu lỗi 401 và chưa thử refresh lần nào
        if (error.response && error.response.status === 401 && !originalRequest._retry) {
            
            if (isRefreshing) {
                return new Promise(function(resolve, reject) {
                    failedQueue.push({resolve, reject});
                }).then(token => {
                    originalRequest.headers['Authorization'] = 'Bearer ' + token;
                    return axios(originalRequest);
                }).catch(err => {
                    return Promise.reject(err);
                });
            }

            originalRequest._retry = true;
            isRefreshing = true;

            const refreshToken = localStorage.getItem('refresh_token');

            if (!refreshToken) {
                return Promise.reject(error);
            }

            try {
                // Gọi API refresh
                // Header Authorization ở đây sẽ được giữ nguyên nhờ logic mới trong Request Interceptor
                const response = await axios.post('/api/refresh-token', {
                    refresh_token: refreshToken 
                }, {
                    headers: { 'Authorization': `Bearer ${refreshToken}` }
                });

                const { access_token, refresh_token: newRefreshToken } = response.data;

                // Lưu token mới
                localStorage.setItem('access_token', access_token);
                localStorage.setItem('refresh_token', newRefreshToken);

                // Cập nhật header mặc định
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + access_token;
                
                // Xử lý hàng đợi
                processQueue(null, access_token);
                
                // Thử lại request ban đầu
                originalRequest.headers['Authorization'] = 'Bearer ' + access_token;
                return axios(originalRequest);

            } catch (refreshError) {
                processQueue(refreshError, null);
                // Xóa sạch token nếu refresh thất bại
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                localStorage.removeItem('userRole');
                
                // Redirect về login
                window.location.href = '/login';
                return Promise.reject(refreshError);
            } finally {
                isRefreshing = false;
            }
        }

        return Promise.reject(error);
    }
);