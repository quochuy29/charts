<template>
  <div class="select-none">
    <div 
      class="group flex items-center justify-between py-2 pr-2 rounded-md transition-colors cursor-pointer mb-0.5"
      :class="[
        // 1. Chỉ giữ lại Background màu xanh nhạt cho row (thẻ cha)
        isActive ? 'bg-blue-50' : 'hover:bg-gray-100',
        
        // Căn lề khi menu thu gọn
        isCollapsed ? 'justify-center pl-2' : ''
      ]"
      :style="{ paddingLeft: isCollapsed ? '' : paddingLeft }"
      @click.stop="handleNodeClick"
      :title="isCollapsed ? node.name : ''"
    >
      
      <div class="flex items-center overflow-hidden gap-2">
        <span 
          class="text-sm whitespace-nowrap transition-all duration-300 truncate"
          :class="[
            isCollapsed ? 'w-0 opacity-0' : 'w-auto opacity-100',
            // Nếu Active: Màu xanh đậm + In đậm. Ngược lại: Màu xám.
            isActive 
              ? 'text-blue-700 font-bold' 
              : 'text-slate-600 group-hover:text-slate-900'
          ]"
        >
          {{ node.name }}
        </span>
      </div>

      <button 
        v-if="hasChildren && !isCollapsed"
        @click.stop="toggleExpand"
        class="p-0.5 rounded hover:bg-black/5 text-slate-400 transition-transform duration-200"
        :class="{'rotate-90 text-slate-600': isOpen}"
      >
        <ChevronRight class="w-4 h-4" />
      </button>
    </div>

    <div v-if="hasChildren && isOpen && !isCollapsed" class="transition-all duration-300">
      <TreeItem 
        v-for="child in node.children" 
        :key="child.id" 
        :node="child"
        :depth="depth + 1"
        :current-query="currentQuery"
        :is-collapsed="isCollapsed"
        @select="$emit('select', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  node: { type: Object, required: true },
  depth: { type: Number, default: 0 },
  isCollapsed: { type: Boolean, default: false },
  currentQuery: { type: Object, default: () => ({}) } 
});

const emit = defineEmits(['select']);

const isOpen = ref(false);
const hasChildren = computed(() => props.node.children && props.node.children.length > 0);

// Tính toán thụt đầu dòng
const paddingLeft = computed(() => `${(props.depth * 12) + 12}px`);

// --- LOGIC CHECK ACTIVE ---
const isActive = computed(() => {
    let typeKey = '';
    // Level 2: Facility, Level 3: Utility, Level 4: Equipment
    if (props.node.item_type === 2) typeKey = 'facility';
    else if (props.node.item_type === 3) typeKey = 'utility';
    else if (props.node.item_type === 4) typeKey = 'equipment';
    
    // So sánh ID (sử dụng == để so sánh lỏng giữa string/number)
    return typeKey && props.currentQuery[typeKey] == props.node.id;
});

// Tự động mở menu cha khi con được chọn
watch(isActive, (val) => {
    if (val) {
        isOpen.value = true;
    }
}, { immediate: true });

const handleNodeClick = () => {
    // Emit sự kiện để cha xử lý (cập nhật URL)
    if (props.node.item_type >= 2) {
        emit('select', props.node);
    }
    
    if (hasChildren.value) {
        isOpen.value = !isOpen.value;
    }
};

const toggleExpand = () => {
    isOpen.value = !isOpen.value;
};
</script>