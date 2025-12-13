<template>
  <div 
    v-if="!isCollapsed" 
    @click="$emit('toggle-sidebar')"
    class="fixed inset-0 bg-black/50 z-20 md:hidden transition-opacity duration-300"
  ></div>

  <aside 
    class="bg-white text-slate-700 flex flex-col h-full border-r border-gray-200 font-sans transition-all duration-300 ease-in-out z-30 fixed md:relative"
    :class="[
      'w-[260px]', 
      isCollapsed ? '-translate-x-full md:translate-x-0 md:w-[70px]' : 'translate-x-0 md:w-[260px]'
    ]"
  >
    <div class="flex-1 overflow-y-auto py-6 px-3 space-y-6 overflow-x-hidden">
      
      <div>
        <div 
            class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider transition-all duration-300 overflow-hidden whitespace-nowrap"
            :class="isCollapsed ? 'max-h-0 opacity-0 mb-0' : 'max-h-6 opacity-100 mb-2'"
        >
            メインメニュー
        </div>
        
        <nav class="space-y-1">
          <router-link to="/production-planning" 
            class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 hover:text-slate-900 transition-colors group h-10" 
            :class="[
                $route.path === '/production-planning' ? 'bg-blue-50 text-blue-700 font-medium' : '',
                isCollapsed ? 'justify-center' : ''
            ]"
            :title="isCollapsed ? '生産計画設定' : ''"
            @click="handleMobileClick"
          >
            <Calendar class="w-5 h-5 text-slate-500 group-hover:text-slate-900 shrink-0 transition-colors" /> 
            <span 
                class="whitespace-nowrap overflow-hidden transition-all duration-300 ease-in-out"
                :class="isCollapsed ? 'w-0 opacity-0 ml-0' : 'w-auto opacity-100 ml-3'"
            >
                生産計画設定
            </span>
          </router-link>

          <router-link to="/user-management" 
            class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 hover:text-slate-900 transition-colors group h-10" 
            :class="[
                $route.path === '/user-management' ? 'bg-blue-50 text-blue-700 font-medium' : '',
                isCollapsed ? 'justify-center' : ''
            ]"
            :title="isCollapsed ? 'ユーザー管理' : ''"
            @click="handleMobileClick"
          >
            <Users class="w-5 h-5 text-slate-500 group-hover:text-slate-900 shrink-0 transition-colors" /> 
            <span 
                class="whitespace-nowrap overflow-hidden transition-all duration-300 ease-in-out"
                :class="isCollapsed ? 'w-0 opacity-0 ml-0' : 'w-auto opacity-100 ml-3'"
            >
                ユーザー管理
            </span>
          </router-link>

          <router-link to="/data-maintenance" 
            class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 hover:text-slate-900 transition-colors group h-10" 
            :class="[
                $route.path === '/data-maintenance' ? 'bg-blue-50 text-blue-700 font-medium' : '',
                isCollapsed ? 'justify-center' : ''
            ]"
            :title="isCollapsed ? 'データ保守' : ''"
            @click="handleMobileClick"
          >
            <Database class="w-5 h-5 text-slate-500 group-hover:text-slate-900 shrink-0 transition-colors" /> 
            <span 
                class="whitespace-nowrap overflow-hidden transition-all duration-300 ease-in-out"
                :class="isCollapsed ? 'w-0 opacity-0 ml-0' : 'w-auto opacity-100 ml-3'"
            >
                データ保守
            </span>
          </router-link>
        </nav>
      </div>

      <div>
        <div 
            class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center h-8 overflow-hidden transition-all duration-300"
            :class="{'justify-center': isCollapsed, 'gap-2': !isCollapsed}"
        >
            <BarChart3 class="w-5 h-5 shrink-0" /> 
            <span 
                class="whitespace-nowrap overflow-hidden transition-all duration-300"
                :class="isCollapsed ? 'w-0 opacity-0' : 'w-auto opacity-100'"
            >
                グラフ表示
            </span>
        </div>

        <div v-show="!isCollapsed" class="space-y-1 transition-opacity duration-300">
            <div v-if="loading" class="text-center py-2 text-sm text-gray-400">Loading...</div>

            <TreeItem 
                v-else
                v-for="item in treeData" 
                :key="item.id"
                :node="item"
                :depth="0"
                :selectedNode="selectedNode"
                :isCollapsed="isCollapsed"
                @select="handleNodeSelect"
            />
        </div>
      </div>

    </div>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { Calendar, Users, Database, BarChart3 } from 'lucide-vue-next';
import TreeItem from './TreeItem.vue'; // Đảm bảo bạn đã tạo file này
import axios from 'axios';

const props = defineProps({
    mode: String,
    selectedNode: Object,
    isCollapsed: { type: Boolean, default: false }
});

const emit = defineEmits(['toggle-sidebar', 'update:selectedNode']);
const router = useRouter();
const route = useRoute();

const treeData = ref([]);
const loading = ref(false);

// Hàm gọi API lấy Tree
const fetchTreeData = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/equipments/tree');
        treeData.value = response.data;
    } catch (error) {
        console.error("Failed to load equipment tree:", error);
    } finally {
        loading.value = false;
    }
};

// Xử lý khi chọn node lá (Equipment/Line...)
const handleNodeSelect = (node) => {
    // Logic ánh xạ item_type sang query param
    let type = 'equipment';
    if(node.item_type === 1) type = 'line';
    else if(node.item_type === 2) type = 'facility';
    else if(node.item_type === 3) type = 'utility';
    // item_type === 4 là equipment (mặc định)
    
    router.push({ path: '/dashboard', query: { [type]: node.id } });
    
    // Auto close sidebar on mobile
    handleMobileClick();
};

const handleMobileClick = () => {
    if (window.innerWidth < 768) {
        emit('toggle-sidebar');
    }
};

onMounted(() => {
    fetchTreeData();
});
</script>