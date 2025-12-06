<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Cấu hình Menu</h1>
    <div class="bg-white rounded-lg shadow p-6">
      <div class="mb-4 flex gap-2">
         <button @click="expandAll" class="text-sm bg-gray-100 px-3 py-1 rounded hover:bg-gray-200">Mở rộng tất cả</button>
         <button @click="collapseAll" class="text-sm bg-gray-100 px-3 py-1 rounded hover:bg-gray-200">Thu gọn tất cả</button>
      </div>

      <div class="space-y-4">
        <div v-for="factory in mockMenuData" :key="factory.id" class="border rounded p-3">
          <div class="flex items-center gap-3">
            <input type="checkbox" v-model="factory.isVisible" class="h-4 w-4 text-blue-600 rounded">
            <span class="font-bold text-lg">{{ factory.name }}</span>
            <span class="text-xs bg-gray-200 px-2 py-0.5 rounded text-gray-600">Nhà máy</span>
          </div>
          
          <div class="ml-8 mt-3 space-y-3 pl-4 border-l-2 border-gray-100">
            <div v-for="line in factory.children" :key="line.id">
              <div class="flex items-center gap-3">
                <input type="checkbox" v-model="line.isVisible" class="h-4 w-4 text-blue-600 rounded">
                <span class="font-medium">{{ line.name }}</span>
                <span class="text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-500">Dây chuyền</span>
              </div>

              <div class="ml-8 mt-2 space-y-2">
                <div v-for="process in line.children" :key="process.id" class="flex items-center gap-3">
                  <input type="checkbox" v-model="process.isVisible" class="h-4 w-4 text-blue-600 rounded">
                  <span class="text-gray-700">{{ process.name }}</span>
                  <span class="text-xs text-gray-400">Công đoạn</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { mockMenuData } from '../services/mockData';

// Các hàm expand/collapse có thể thêm logic phức tạp hơn nếu dùng component đệ quy
const expandAll = () => { /* Logic mở rộng UI */ };
const collapseAll = () => { /* Logic thu gọn UI */ };
</script>