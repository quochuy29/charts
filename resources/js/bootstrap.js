import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // QUAN TRỌNG: Để gửi cookie session/remember_token
window.axios.defaults.baseURL = 'http://localhost:8080'; // URL Backend của bạn

// --- THÊM INTERCEPTOR ---
// Tự động gắn token từ LocalStorage vào Header
// 1. Request Interceptor: Luôn gắn Access Token
window.axios.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('access_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Flag để tránh loop vô hạn khi refresh token lỗi
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

// 2. Response Interceptor: Xử lý 401 Unauthorized
window.axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        // Nếu lỗi 401 và chưa thử refresh lần nào
        if (error.response && error.response.status === 401 && !originalRequest._retry) {
            
            // Nếu đang trong quá trình refresh, đưa request vào hàng đợi
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

            // Lấy Refresh Token từ storage
            const refreshToken = localStorage.getItem('refresh_token');

            if (!refreshToken) {
                // Không có refresh token -> Logout thật
                return Promise.reject(error);
            }

            try {
                // Gọi API refresh bằng Refresh Token
                // Lưu ý: Cần set Authorization header thủ công bằng Refresh Token cho request này
                const response = await axios.post('/api/refresh-token', {
                    refresh_token: refreshToken 
                }, {
                    headers: { 'Authorization': `Bearer ${refreshToken}` }
                });

                const { access_token, refresh_token: newRefreshToken } = response.data;

                // Cập nhật token mới vào Storage
                localStorage.setItem('access_token', access_token);
                localStorage.setItem('refresh_token', newRefreshToken); // Rotation: Refresh token cũng được làm mới

                // Cập nhật header mặc định
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + access_token;
                
                // Xử lý hàng đợi các request bị pending
                processQueue(null, access_token);
                
                // Thử lại request ban đầu với token mới
                originalRequest.headers['Authorization'] = 'Bearer ' + access_token;
                return axios(originalRequest);

            } catch (refreshError) {
                // Nếu refresh cũng lỗi (ví dụ hết hạn 5 năm hoặc bị revoke) -> Logout triệt để
                processQueue(refreshError, null);
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                localStorage.removeItem('userRole');
                window.location.href = '/login';
                return Promise.reject(refreshError);
            } finally {
                isRefreshing = false;
            }
        }

        return Promise.reject(error);
    }
);