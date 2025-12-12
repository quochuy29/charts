<template>
  <aside class="w-[260px] bg-white text-slate-700 flex flex-col h-full border-r border-gray-200 font-sans transition-all duration-300">
    
    <div class="flex-1 overflow-y-auto py-6 px-3 space-y-8">
      
      <div>
        <div class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">メインメニュー</div>
        <router-link
          v-for="item in configMenuItems" :key="item.id"
          :to="item.url"
          @click="$emit('update:activeConfigTab', item.id)"
          class="flex items-center gap-3 px-4 py-3 rounded-md text-sm font-medium transition-colors"
          :class="activeConfigTab === item.id 
            ? 'bg-primary/10 text-primary' 
            : 'text-gray-700 hover:bg-gray-100'"
        >
          <component :is="item.icon" class="h-4 w-4" />
          {{ item.label }}
        </router-link>
      </div>

      <div >
        <div class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">工場</div>
         <TreeItem 
            :list="initialTreeData" 
            :selectedNode="selectedNode"
            @select="handleSelect" 
          />
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import { mockMenuData, initialTreeData } from '../../../services/mockData';
import { Search, Users, Database, LayoutDashboard, Calendar } from 'lucide-vue-next'; 
import TreeItem from './TreeItem.vue';

const props = defineProps({
  selectedNode: Object,
  activeConfigTab: { type: String, default: 'dashboard' }
});

const openItems = ref({});
const toggle = (id) => { openItems.value[id] = !openItems.value[id]; };
const isOpen = (id) => !!openItems.value[id];

const configMenuItems = [
  { id: 'dashboard', label: "ダッシュボード", url: "/dashboard", icon: LayoutDashboard },
  { id: 'production-planning', label: "生産計画設定", url: "/production-planning", icon: Calendar },
  { id: 'user-management', label: "ユーザー管理", url: "/user-management", icon: Users },
  { id: 'data-maintenance', label: "データ保守", url: "/data-maintenance", icon: Database },
];

const visibleFactories = computed(() => mockMenuData.filter(f => f.isVisible));
const getVisibleChildren = (children) => (children || []).filter(c => c.isVisible);
const emit = defineEmits(['update:selectedNode', 'update:activeConfigTab']);
const handleSelect = (node) => {
  emit('update:selectedNode', node);
};
</script>