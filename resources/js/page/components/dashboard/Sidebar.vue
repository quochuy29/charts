<template>
  <div class="w-80 bg-white border-r border-gray-200 flex flex-col h-full shrink-0 transition-all duration-300">
    
    <div class="p-4 border-b border-gray-200">
      <h1 class="text-xl font-bold text-gray-900 mb-4">Energy Monitor</h1>
      
      <div v-if="mode === 'dashboard'" class="relative">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-500" />
        <input
          placeholder="Search equipment..."
          class="w-full pl-10 pr-4 py-2 bg-gray-100 border-none rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
        />
      </div>
    </div>

    <div class="flex-1 overflow-y-auto p-2">
      
      <TreeItem 
        v-if="mode === 'dashboard'"
        :list="treeData" 
        :selectedNode="selectedNode"
        @select="handleSelect" 
      />

      <div v-else class="flex flex-col gap-1">
        <button
          v-for="item in configMenuItems" :key="item.id"
          @click="$emit('update:activeConfigTab', item.id)"
          class="flex items-center gap-3 px-4 py-3 rounded-md text-sm font-medium transition-colors"
          :class="activeConfigTab === item.id 
            ? 'bg-primary/10 text-primary' 
            : 'text-gray-700 hover:bg-gray-100'"
        >
          <component :is="item.icon" class="h-4 w-4" />
          {{ item.label }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
// SỬA TẠI ĐÂY: Đổi FileTree thành FolderTree
import { Search, Users, FolderTree, Settings } from 'lucide-vue-next'; 
import TreeItem from './TreeItem.vue';
import { initialTreeData } from '../../../src/data/mock.js';

// Cập nhật Props
const props = defineProps({
  selectedNode: Object,
  mode: { type: String, default: 'dashboard' }, // 'dashboard' | 'configuration'
  activeConfigTab: { type: String, default: 'user-management' }
});

const emit = defineEmits(['update:selectedNode', 'update:activeConfigTab']);

const treeData = ref(initialTreeData);

// Danh sách menu cho màn hình Configuration
const configMenuItems = [
  { id: 'user-management', label: 'User Management', icon: Users },
  { id: 'tree-tag', label: 'Configuration Tree Tag', icon: FolderTree }, // Cập nhật icon ở đây
  { id: 'setting', label: 'Setting', icon: Settings },
];

const handleSelect = (node) => {
  emit('update:selectedNode', node);
};
</script>