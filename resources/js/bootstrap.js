import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = 'http://localhost:8080'; // URL Backend của bạn
window.axios.defaults.withCredentials = true;

window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // Nếu nhận lỗi 401 từ bất kỳ API nào -> Redirect về Login.
            // Trừ trường hợp đang ở trang login rồi.
            if (window.location.pathname !== '/login') {
                 window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);