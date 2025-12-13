<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border border-gray-100">
      
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-zinc-900 text-white mb-4">
           <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3V6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3" /></svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">エネルギーモニター</h1>
        <p class="text-sm text-gray-500 mt-2">Enter your credentials to access</p>
      </div>

      <form @submit.prevent="login" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">ユーザーID:</label>
          <input v-model="userId" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-zinc-900 focus:border-transparent outline-none transition-all" placeholder="name@toyota.com" required>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">パスワード:</label>
          <input v-model="password" type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-zinc-900 focus:border-transparent outline-none transition-all" required>
        </div>

        <button type="submit" class="w-full bg-zinc-900 hover:bg-zinc-800 text-white font-bold py-2.5 rounded-lg transition-colors shadow-sm">
          ログイン
        </button>
      </form>

      <div class="mt-8 text-center text-xs text-gray-400">
        © 2025 Toyota Motor Kyushu
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios'; // Đảm bảo đã import axios

const userId = ref(''); // Đổi email -> userId
const password = ref('');
const router = useRouter();
const errorMessage = ref('');

const login = async () => {
    try {
        // Gọi API login đã tạo ở bước trên
        // Laravel Sanctum yêu cầu CSRF token trước
        await axios.get('/sanctum/csrf-cookie');
        
        const response = await axios.post('/api/login', {
            user_id: userId.value,
            password: password.value
        });

        const data = response.data;
        
        // --- LOGIC QUAN TRỌNG ---
        localStorage.setItem('isAuthenticated', 'true');
        localStorage.setItem('userRole', 'Admin'); // Ví dụ

        // Lưu trạng thái persistent
        localStorage.setItem('isPersistent', data.is_persistent);

        if (!data.is_persistent) {
            // Nếu có thời hạn -> Tính toán mốc thời gian hết hạn (Timestamp hiện tại + lifetime)
            const expiryTime = new Date().getTime() + data.session_expires_in_ms;
            localStorage.setItem('sessionExpiry', expiryTime);
        } else {
            // Nếu vô hạn -> Xóa expiry cũ để tránh logic sai
            localStorage.removeItem('sessionExpiry');
        }

        router.push('/dashboard');

    } catch (error) {
        if (error.response && error.response.status === 422) {
            // Hiển thị lỗi validate hoặc lỗi khóa tài khoản (Too many attempts)
            errorMessage.value = error.response.data.message || 'Login failed';
        } else {
             console.error(error);
        }
    }
};
</script>