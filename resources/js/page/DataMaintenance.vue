<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Bảo trì dữ liệu</h1>

    <div class="border-b border-gray-200 mb-6">
      <nav class="-mb-px flex space-x-8">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="currentTab = tab.id"
          :class="[
            currentTab === tab.id
              ? 'border-red-500 text-red-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
          ]"
        >
          {{ tab.name }}
        </button>
      </nav>
    </div>

    <div class="bg-white rounded-lg shadow p-6 min-h-[400px]">
      
      <div v-if="['tag', 'param', 'device'].includes(currentTab)">
        <div class="flex gap-4 mb-6">
           <button class="bg-blue-600 text-white px-4 py-2 rounded flex items-center gap-2 hover:bg-blue-700">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
             CSV Upload
           </button>
           <button class="text-blue-600 hover:underline flex items-center gap-2">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
             Download Template
           </button>
        </div>
        
        <div class="text-center text-gray-500 py-10 border-2 border-dashed rounded-lg bg-gray-50">
           <p>Chưa có file nào được tải lên cho mục: {{ getTabName(currentTab) }}</p>
        </div>
      </div>

      <div v-if="currentTab === 'recovery'">
         <h3 class="font-bold mb-4">Khôi phục dữ liệu</h3>
         <div class="flex gap-4 items-end mb-6">
           <div>
             <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
             <input type="datetime-local" class="border rounded px-3 py-2">
           </div>
           <div>
             <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
             <input type="datetime-local" class="border rounded px-3 py-2">
           </div>
           <button @click="startRecovery" :disabled="isRecovering" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 disabled:opacity-50">
             {{ isRecovering ? 'Đang xử lý...' : 'Thực thi Khôi phục' }}
           </button>
         </div>

         <div v-if="isRecovering || progress > 0" class="w-full bg-gray-200 rounded-full h-4 mb-2">
           <div class="bg-green-600 h-4 rounded-full transition-all duration-300" :style="{ width: progress + '%' }"></div>
         </div>
         <p v-if="progress === 100" class="text-green-600 font-bold">Khôi phục thành công!</p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const tabs = [
  { id: 'tag', name: 'Tag Calculation Formula' },
  { id: 'param', name: 'Parameter Setting' },
  { id: 'recovery', name: 'Data Recovery' },
  { id: 'device', name: 'Device Management' },
];

const currentTab = ref('tag');
const isRecovering = ref(false);
const progress = ref(0);

const getTabName = (id) => tabs.find(t => t.id === id)?.name;

const startRecovery = () => {
  isRecovering.value = true;
  progress.value = 0;
  const interval = setInterval(() => {
    progress.value += 10;
    if (progress.value >= 100) {
      clearInterval(interval);
      isRecovering.value = false;
    }
  }, 300);
};
</script>