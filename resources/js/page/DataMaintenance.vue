<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">データ保守</h1>

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
            'whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors'
          ]"
        >
          {{ tab.name }}
        </button>
      </nav>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 min-h-[400px]">
      
      <div v-if="['tag', 'param', 'device'].includes(currentTab)">
        <div class="flex gap-4 mb-8">
           <button class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-blue-700 shadow-sm flex items-center gap-2 transition-all active:scale-95">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
             CSVアップロード
           </button>
           <button class="text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-2 font-medium transition-colors">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
             テンプレートダウンロード
           </button>
        </div>
        
        <div class="flex flex-col items-center justify-center text-center py-16 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50/50">
           <div class="p-4 bg-gray-100 rounded-full mb-3">
              <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
           </div>
           <p class="text-gray-500 font-medium">「{{ getTabName(currentTab) }}」のファイルはまだアップロードされていません。</p>
        </div>
      </div>

      <div v-if="currentTab === 'recovery'">
         <h3 class="font-bold text-lg text-gray-800 mb-6 flex items-center gap-2">
            <span class="w-1 h-6 bg-red-500 rounded"></span>
            データ復旧
         </h3>
         
         <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
            <div class="flex flex-col md:flex-row gap-6 items-end mb-6">
              <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">開始日時</label>
                <input type="datetime-local" class="border border-gray-300 rounded-lg px-4 py-2 w-full font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">終了日時</label>
                <input type="datetime-local" class="border border-gray-300 rounded-lg px-4 py-2 w-full font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <button @click="startRecovery" :disabled="isRecovering" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm transition-all active:scale-95 min-w-[120px]">
                {{ isRecovering ? '処理中...' : '復旧実行' }}
              </button>
            </div>

            <div v-if="isRecovering || progress > 0" class="space-y-2">
               <div class="flex justify-between text-xs font-bold text-gray-500">
                  <span>進行状況</span>
                  <span>{{ progress }}%</span>
               </div>
               <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                 <div class="bg-green-500 h-2.5 rounded-full transition-all duration-300 ease-out" :style="{ width: progress + '%' }"></div>
               </div>
            </div>
            <p v-if="progress === 100" class="text-green-600 font-bold mt-4 flex items-center gap-2">
               <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
               復旧が完了しました！
            </p>
         </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const tabs = [
  { id: 'tag', name: 'Tag計算式設定' },
  { id: 'param', name: 'パラメータ設定' },
  { id: 'recovery', name: 'データ復旧' },
  { id: 'device', name: '設備管理' },
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