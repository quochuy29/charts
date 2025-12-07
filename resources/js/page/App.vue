<template>
  <div class="flex h-screen w-full bg-gray-50">
    <Sidebar 
      v-if="!isLoginPage" 
      mode="dashboard" 
      :selectedNode="selectedNode"
      @update:selectedNode="selectedNode = $event" />

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <header v-if="!isLoginPage" class="h-16 flex items-center justify-between px-6 bg-white border-b border-gray-200 shrink-0 z-20 shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-red-600 rounded flex items-center justify-center text-white font-bold shadow-sm">
             <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
               <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5-9h10v2H7z"/>
             </svg>
          </div>
          <div>
             <div class="font-bold text-lg leading-none tracking-tight text-gray-900">TOYOTA MOTOR KYUSHU</div>
             <div class="text-[10px] text-gray-500 font-medium tracking-widest mt-0.5">ENERGY MANAGEMENT SYSTEM</div>
          </div>
        </div>

        <div class="flex items-center gap-4">
           <div class="text-right hidden sm:block">
              <div class="text-sm font-semibold text-gray-800">Welcome, {{ userRole }}</div>
           </div>
           <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold border border-slate-200">
              {{ userRole.charAt(0).toUpperCase() }}
           </div>
        </div>
      </header>

      <main class="flex-1 overflow-auto bg-gray-50 relative">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import Sidebar from './components/layout/Sidebar.vue';

const route = useRoute();
const isLoginPage = computed(() => route.path === '/login');
const userRole = localStorage.getItem('userRole') || 'Guest';

const selectedNode = ref({
  id: "factory-1",
  name: "Factory 1 - Main Production",
  type: "factory",
});
</script>