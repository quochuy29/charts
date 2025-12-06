<template>
  <div class="flex h-screen w-full bg-gray-50">
    <Sidebar v-if="!isLoginPage" />

    <main class="flex-1 overflow-auto p-0">
       <div v-if="!isLoginPage" class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-10">
          <h2 class="font-semibold text-gray-700 capitalize">{{ route.name }}</h2>
          <div class="text-sm text-gray-500">Welcome, {{ userRole }}</div>
       </div>

      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import Sidebar from './components/layout/Sidebar.vue';

const route = useRoute();
const isLoginPage = computed(() => route.path === '/login');
const userRole = localStorage.getItem('userRole') || 'Guest';
</script>