<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">メニュー設定</h1>
    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
      <div class="mb-6 flex gap-3">
         <button @click="expandAll" class="text-sm font-bold bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">全て展開</button>
         <button @click="collapseAll" class="text-sm font-bold bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">全て折りたたむ</button>
      </div>

      <div class="space-y-4">
        <div v-for="factory in mockMenuData" :key="factory.id" class="border border-gray-200 rounded-lg p-4 bg-gray-50/30">
          <div class="flex items-center gap-3">
            <input type="checkbox" v-model="factory.isVisible" class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
            <span class="font-bold text-lg text-gray-800">{{ factory.name }}</span>
            <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded uppercase tracking-wide">工場</span>
          </div>
          
          <div class="ml-6 mt-4 space-y-3 pl-4 border-l-2 border-gray-200">
            <div v-for="line in factory.children" :key="line.id">
              <div class="flex items-center gap-3">
                <input type="checkbox" v-model="line.isVisible" class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                <span class="font-medium text-gray-700">{{ line.name }}</span>
                <span class="text-[10px] font-bold bg-gray-200 text-gray-600 px-2 py-0.5 rounded uppercase tracking-wide">ライン</span>
              </div>

              <div class="ml-7 mt-2 space-y-2">
                <div v-for="process in line.children" :key="process.id" class="flex items-center gap-3 p-1.5 rounded hover:bg-gray-100 transition-colors w-fit pr-4">
                  <input type="checkbox" v-model="process.isVisible" class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                  <span class="text-sm text-gray-600">{{ process.name }}</span>
                  <span class="text-[10px] text-gray-400 border border-gray-200 px-1.5 rounded">工程</span>
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

const expandAll = () => { /* Logic */ };
const collapseAll = () => { /* Logic */ };
</script>