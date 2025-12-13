<template>
  <div 
    v-if="!isCollapsed" 
    @click="$emit('toggle-sidebar')"
    class="fixed inset-0 bg-black/50 z-20 md:hidden transition-opacity duration-300"
  ></div>

  <aside 
    class="bg-white text-slate-700 flex flex-col h-full border-r border-gray-200 font-sans transition-all duration-300 ease-in-out z-30
           fixed md:relative"
    :class="[
      // Mobile logic: Luôn rộng 260px, dùng translate để ẩn/hiện
      'w-[260px]', 
      isCollapsed ? '-translate-x-full md:translate-x-0 md:w-[70px]' : 'translate-x-0 md:w-[260px]'
    ]"
  >
    
    <div class="flex-1 overflow-y-auto py-6 px-3 space-y-6 overflow-x-hidden">
      
      <div>
        <div 
            class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider transition-all duration-300 overflow-hidden whitespace-nowrap"
            :class="isCollapsed ? 'md:max-h-0 md:opacity-0 md:mb-0' : 'max-h-6 opacity-100 mb-2'"
        >
            メインメニュー
        </div>
        
        <nav class="space-y-1">
          <router-link to="/production-planning" 
            class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 hover:text-slate-900 transition-colors group h-10" 
            :class="[
                $route.path === '/production-planning' ? 'bg-blue-50 text-blue-700 font-medium' : '',
                isCollapsed ? 'md:justify-center' : ''
            ]"
            @click="handleMobileClick"
          >
            <Calendar class="w-5 h-5 text-slate-500 group-hover:text-slate-900 shrink-0 transition-colors" /> 
            <span 
                class="whitespace-nowrap overflow-hidden transition-all duration-300 ease-in-out"
                :class="isCollapsed ? 'md:w-0 md:opacity-0 md:ml-0' : 'w-auto opacity-100 ml-3'"
            >
                生産計画設定
            </span>
          </router-link>

          <router-link to="/user-management" 
            class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 hover:text-slate-900 transition-colors group h-10" 
            :class="[
                $route.path === '/user-management' ? 'bg-blue-50 text-blue-700 font-medium' : '',
                isCollapsed ? 'md:justify-center' : ''
            ]"
            @click="handleMobileClick"
          >
            <Users class="w-5 h-5 text-slate-500 group-hover:text-slate-900 shrink-0 transition-colors" /> 
            <span 
                class="whitespace-nowrap overflow-hidden transition-all duration-300 ease-in-out"
                :class="isCollapsed ? 'md:w-0 md:opacity-0 md:ml-0' : 'w-auto opacity-100 ml-3'"
            >
                ユーザー管理
            </span>
          </router-link>

          <router-link to="/data-maintenance" 
            class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 hover:text-slate-900 transition-colors group h-10" 
            :class="[
                $route.path === '/data-maintenance' ? 'bg-blue-50 text-blue-700 font-medium' : '',
                isCollapsed ? 'md:justify-center' : ''
            ]"
            @click="handleMobileClick"
          >
            <Database class="w-5 h-5 text-slate-500 group-hover:text-slate-900 shrink-0 transition-colors" /> 
            <span 
                class="whitespace-nowrap overflow-hidden transition-all duration-300 ease-in-out"
                :class="isCollapsed ? 'md:w-0 md:opacity-0 md:ml-0' : 'w-auto opacity-100 ml-3'"
            >
                データ保守
            </span>
          </router-link>
        </nav>
      </div>

      <div v-if="treeData.length > 0">
        <div 
            class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center h-8 overflow-hidden transition-all duration-300"
            :class="{'md:justify-center': isCollapsed, 'gap-2': !isCollapsed}"
        >
            <BarChart3 class="w-5 h-5 shrink-0" /> 
            <span 
                class="whitespace-nowrap overflow-hidden transition-all duration-300"
                :class="isCollapsed ? 'md:w-0 md:opacity-0' : 'w-auto opacity-100'"
            >
                グラフ表示
            </span>
        </div>

        <div v-show="!isCollapsed" class="space-y-1 transition-opacity duration-300">
          <div v-for="line in treeData" :key="line.id">
            <button @click="toggle(line.id)" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-100 transition-colors text-sm font-medium whitespace-nowrap overflow-hidden">
              {{ line.name }}
              <ChevronRight class="w-4 h-4 text-slate-400 transition-transform shrink-0" :class="{'rotate-90': isOpen(line.id)}" />
            </button>
            
            <div v-show="isOpen(line.id)" class="ml-4 border-l border-gray-200 pl-2 mt-1 space-y-1">
               <div v-for="facility in line.children" :key="facility.id">
                    <div class="flex items-center group">
                        <div @click="nav('facility', facility.id)" class="flex-1 px-2 py-1.5 rounded-md hover:bg-gray-100 cursor-pointer text-sm whitespace-nowrap overflow-hidden text-ellipsis" :class="isSelected('facility', facility.id) ? 'text-blue-700 bg-blue-50 font-medium' : ''">
                            {{ facility.name }}
                        </div>
                        </div>
                    </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </aside>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { Calendar, Users, Database, BarChart3, ChevronRight } from 'lucide-vue-next';
import { mockTreeData } from '../../../services/mockData';

const props = defineProps({
    mode: String,
    selectedNode: Object,
    isCollapsed: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['toggle-sidebar', 'update:selectedNode']); // Khai báo emit

const router = useRouter();
const route = useRoute();
const treeData = mockTreeData;
const openItems = ref({});

const toggle = (id) => { openItems.value[id] = !openItems.value[id]; };
const isOpen = (id) => !!openItems.value[id];
const hasChildren = (node) => node.children && node.children.length > 0;

// Hàm tự động đóng sidebar trên mobile khi click vào menu item
const handleMobileClick = () => {
    if (window.innerWidth < 768) {
        emit('toggle-sidebar'); // Gọi lên cha để đổi state isCollapsed = true
    }
};

const nav = (type, id) => {
    router.push({ path: '/dashboard', query: { [type]: id } });
    handleMobileClick(); // Đóng sidebar sau khi chọn
};

const isSelected = (type, id) => route.query[type] === id;
</script>