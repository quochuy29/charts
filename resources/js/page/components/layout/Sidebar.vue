<template>
  <aside class="w-64 bg-slate-900 text-slate-100 flex flex-col h-full transition-all duration-300">
    <div class="p-4 border-b border-slate-700 flex items-center gap-2">
      <div class="font-bold text-xl tracking-tight">EMS System</div>
    </div>

    <div class="flex-1 overflow-y-auto py-4">
      <div class="px-3 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Main Menu</div>
      <nav class="space-y-1 px-2 mb-6">
        <router-link to="/dashboard" class="flex items-center px-3 py-2 rounded-md hover:bg-slate-800 transition-colors" :class="{ 'bg-slate-800 text-white': $route.path === '/dashboard' }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
          Dashboard
        </router-link>
        <router-link to="/production-planning" class="flex items-center px-3 py-2 rounded-md hover:bg-slate-800 transition-colors" :class="{ 'bg-slate-800 text-white': $route.path === '/production-planning' }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>
          Kế hoạch sản xuất
        </router-link>
      </nav>

      <div v-if="visibleFactories.length > 0">
        <div class="px-3 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Nhà máy</div>
        <nav class="space-y-1 px-2">
          <div v-for="factory in visibleFactories" :key="factory.id" class="space-y-1">
            <button @click="toggle(factory.id)" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-slate-800 transition-colors text-left">
              <span class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" /></svg>
                {{ factory.name }}
              </span>
              <svg :class="{'rotate-90': isOpen(factory.id)}" class="h-4 w-4 transform transition-transform text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
            
            <div v-show="isOpen(factory.id)" class="pl-4 space-y-1">
              <div v-for="line in getVisibleChildren(factory.children)" :key="line.id">
                <button @click="toggle(line.id)" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-slate-800 transition-colors text-sm text-slate-300">
                  <span>{{ line.name }}</span>
                  <svg :class="{'rotate-90': isOpen(line.id)}" class="h-3 w-3 transform transition-transform text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>

                <div v-show="isOpen(line.id)" class="pl-4 border-l border-slate-700 ml-3 space-y-1 mt-1">
                  <router-link 
                    v-for="process in getVisibleChildren(line.children)" 
                    :key="process.id"
                    :to="{ path: '/dashboard', query: { process: process.id } }"
                    class="block px-3 py-1.5 rounded-md hover:bg-slate-800 text-sm text-slate-400 hover:text-white"
                    :class="{ 'text-white font-medium': $route.query.process === process.id }"
                  >
                    {{ process.name }}
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </nav>
      </div>
      
      <div class="mt-8 px-3 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Hệ thống</div>
      <nav class="space-y-1 px-2 pb-4">
        <router-link to="/user-management" class="flex items-center px-3 py-2 rounded-md hover:bg-slate-800 transition-colors" :class="{ 'bg-slate-800 text-white': $route.path === '/user-management' }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-purple-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" /></svg>
          Quản lý người dùng
        </router-link>
        <router-link to="/menu-settings" class="flex items-center px-3 py-2 rounded-md hover:bg-slate-800 transition-colors" :class="{ 'bg-slate-800 text-white': $route.path === '/menu-settings' }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
          Menu cấu hình
        </router-link>
        <router-link to="/data-maintenance" class="flex items-center px-3 py-2 rounded-md hover:bg-slate-800 transition-colors" :class="{ 'bg-slate-800 text-white': $route.path === '/data-maintenance' }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-pink-400" viewBox="0 0 20 20" fill="currentColor"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" /><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" /></svg>
          Bảo trì dữ liệu
        </router-link>
        <router-link to="/login" class="flex items-center px-3 py-2 rounded-md hover:bg-slate-800 transition-colors text-red-400 hover:text-red-300 mt-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" /></svg>
          Đăng xuất
        </router-link>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import { mockMenuData } from '../../../services/mockData';

// Quản lý trạng thái mở/đóng của các menu
const openItems = ref({});

const toggle = (id) => {
  openItems.value[id] = !openItems.value[id];
};

const isOpen = (id) => !!openItems.value[id];

// Chỉ hiển thị các mục có isVisible = true
const visibleFactories = computed(() => {
  return mockMenuData.filter(f => f.isVisible);
});

const getVisibleChildren = (children) => {
  return (children || []).filter(c => c.isVisible);
};
</script>