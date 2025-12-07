<template>
  <div class="p-6 space-y-6 bg-gray-50 min-h-full font-sans text-gray-800">
    
    <div>
      <h1 class="text-2xl font-bold text-gray-900">データ保守</h1>
      <p class="text-sm text-gray-500 mt-1">各種データの設定・保守および履歴確認</p>
    </div>

    <div class="border-b border-gray-200">
      <nav class="-mb-px flex space-x-8">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="currentTab = tab.id"
          :class="[
            currentTab === tab.id
              ? 'border-blue-500 text-blue-600 font-bold'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium',
            'whitespace-nowrap py-4 px-1 border-b-2 text-sm transition-colors'
          ]"
        >
          {{ tab.name }}
        </button>
      </nav>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 min-h-[500px] flex flex-col">
      
      <div class="mb-10">
        
        <div v-if="['tag', 'param', 'device'].includes(currentTab)">
          <div class="flex flex-col sm:flex-row gap-4 mb-6">
             <button class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-blue-700 shadow-sm flex items-center gap-2 transition-all active:scale-95">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
               CSVアップロード
             </button>
             <button class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2 font-bold text-sm">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
               テンプレートダウンロード
             </button>
          </div>
          
          <div class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50/50 p-8 text-center hover:bg-gray-50 transition-colors cursor-pointer group">
             <div class="p-3 bg-white rounded-full w-fit mx-auto mb-3 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
             </div>
             <p class="text-gray-900 font-bold mb-1">クリックしてファイルをアップロード</p>
             <p class="text-xs text-gray-500">またはドラッグ＆ドロップ (CSVファイルのみ)</p>
          </div>
        </div>

        <div v-if="currentTab === 'recovery'">
           <div class="bg-orange-50 border border-orange-100 rounded-xl p-6">
              <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                リカバリー期間指定
              </h3>
              <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full">
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">開始日時</label>
                  <input type="datetime-local" class="border border-gray-300 rounded-lg px-3 py-2 w-full font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                </div>
                <div class="w-full">
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">終了日時</label>
                  <input type="datetime-local" class="border border-gray-300 rounded-lg px-3 py-2 w-full font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                </div>
                <button @click="startRecovery" :disabled="isRecovering" class="bg-orange-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm transition-all active:scale-95 whitespace-nowrap min-w-[120px]">
                  {{ isRecovering ? '処理中...' : '復旧実行' }}
                </button>
              </div>

              <div v-if="isRecovering || progress > 0" class="mt-6 space-y-2">
                 <div class="flex justify-between text-xs font-bold text-gray-600">
                    <span>Processing...</span>
                    <span>{{ progress }}%</span>
                 </div>
                 <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                   <div class="bg-orange-500 h-2 rounded-full transition-all duration-300 ease-out" :style="{ width: progress + '%' }"></div>
                 </div>
                 <p v-if="progress === 100" class="text-green-600 text-sm font-bold mt-2 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    復旧完了
                 </p>
              </div>
           </div>
        </div>

      </div>

      <hr class="border-gray-100 mb-8" />

      <div class="flex-1">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          {{ currentTab === 'recovery' ? '復旧履歴' : 'アップロード履歴' }}
        </h3>

        <div class="border border-gray-200 rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-16">No.</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                  {{ currentTab === 'recovery' ? '対象期間' : 'ファイル名' }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">実行日時</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">実行者</th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-24">ステータス</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(item, index) in paginatedHistory" :key="item.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                   {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  <div v-if="currentTab === 'recovery'" class="flex flex-col">
                    <span>{{ item.targetStart }}</span>
                    <span class="text-gray-400 text-xs rotate-90 w-fit pl-1">~</span>
                    <span>{{ item.targetEnd }}</span>
                  </div>
                  <div v-else class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    {{ item.fileName }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ item.executedAt }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                      {{ item.user.charAt(0) }}
                    </div>
                    {{ item.user }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="item.status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ item.status === 'success' ? '成功' : '失敗' }}
                  </span>
                </td>
              </tr>
              <tr v-if="paginatedHistory.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">
                  履歴がありません
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="totalPages > 1" class="flex items-center justify-between mt-4">
           <div class="text-sm text-gray-500">
              {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredHistory.length) }} / {{ filteredHistory.length }} 件
           </div>
           <div class="flex items-center gap-2">
              <button 
                @click="currentPage > 1 ? currentPage-- : null"
                :disabled="currentPage === 1"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                前へ
              </button>
              <span class="text-sm text-gray-600 px-2 font-medium">
                 {{ currentPage }} / {{ totalPages }}
              </span>
              <button 
                @click="currentPage < totalPages ? currentPage++ : null"
                :disabled="currentPage === totalPages"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                次へ
              </button>
           </div>
        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

// --- Configuration ---
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

// --- Mock History Data (Đã thêm nhiều data để test phân trang) ---
const mockHistoryData = [
  // Tag Data (8 items)
  { id: 1, type: 'tag', fileName: 'tag_config_v1.csv', executedAt: '2025/01/20 10:30', user: 'Admin', status: 'success' },
  { id: 2, type: 'tag', fileName: 'tag_config_v2.csv', executedAt: '2025/01/21 14:15', user: 'Manager', status: 'error' },
  { id: 6, type: 'tag', fileName: 'tag_config_v3.csv', executedAt: '2025/01/22 09:00', user: 'Admin', status: 'success' },
  { id: 7, type: 'tag', fileName: 'tag_config_v4.csv', executedAt: '2025/01/23 11:20', user: 'User A', status: 'success' },
  { id: 8, type: 'tag', fileName: 'tag_config_v5.csv', executedAt: '2025/01/24 16:45', user: 'Manager', status: 'success' },
  { id: 9, type: 'tag', fileName: 'tag_config_v6.csv', executedAt: '2025/01/25 10:10', user: 'Admin', status: 'error' },
  { id: 10, type: 'tag', fileName: 'tag_config_v7.csv', executedAt: '2025/01/26 13:30', user: 'User B', status: 'success' },
  { id: 11, type: 'tag', fileName: 'tag_config_v8.csv', executedAt: '2025/01/27 08:50', user: 'Admin', status: 'success' },

  // Param Data
  { id: 3, type: 'param', fileName: 'param_2025_Q1.csv', executedAt: '2025/01/15 09:00', user: 'Admin', status: 'success' },
  
  // Device Data
  { id: 4, type: 'device', fileName: 'device_list_update.csv', executedAt: '2025/01/10 11:45', user: 'User A', status: 'success' },
  
  // Recovery Data
  { id: 5, type: 'recovery', targetStart: '2024/12/01 00:00', targetEnd: '2024/12/02 00:00', executedAt: '2025/01/05 08:20', user: 'System', status: 'success' },
];

// --- Pagination Logic ---
const itemsPerPage = 5;
const currentPage = ref(1);

const filteredHistory = computed(() => {
  return mockHistoryData.filter(item => item.type === currentTab.value);
});

const totalPages = computed(() => Math.ceil(filteredHistory.value.length / itemsPerPage));

const paginatedHistory = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredHistory.value.slice(start, end);
});

// Reset page when tab changes
watch(currentTab, () => {
  currentPage.value = 1;
});

// --- Methods ---
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