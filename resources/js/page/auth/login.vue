<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-96">
      <h2 class="text-2xl font-bold mb-6 text-center text-red-600">Toyota Kyushu EMS</h2>
      <form @submit.prevent="login">
        <div class="mb-4">
          <label class="block text-gray-700">Email</label>
          <input type="text" v-model="email" class="w-full border p-2 rounded" placeholder="admin@toyota.com">
        </div>
        <div class="mb-6">
          <label class="block text-gray-700">Password</label>
          <input type="password" v-model="password" class="w-full border p-2 rounded" placeholder="******">
        </div>
        <button type="submit" class="w-full bg-red-600 text-white p-2 rounded hover:bg-red-700">
          Sign In (Bypass)
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const router = useRouter();

const login = () => {
    // LOGIC FAKE: Không cần check đúng sai, lưu bừa token và chuyển trang
    localStorage.setItem('isAuthenticated', 'true');
    localStorage.setItem('userRole', 'admin'); // Giả lập luôn là admin
    
    console.log("Login bypass successful!");
    router.push('/dashboard');
};
</script>