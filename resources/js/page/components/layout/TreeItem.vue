<template>
  <div class="select-none">
    <div 
      class="group flex items-center justify-between py-2 pr-2 rounded-md transition-colors cursor-pointer mb-0.5"
      :class="[
        // Logic Style: Nếu đang được chọn (isSelected) thì màu xanh, ngược lại hover xám
        isActive 
          ? 'bg-blue-50 text-blue-700 font-bold' 
          : 'text-slate-600 hover:bg-gray-100 hover:text-slate-900',
        
        // Căn giữa nếu sidebar bị thu gọn
        isCollapsed ? 'justify-center pl-2' : ''
      ]"
      :style="{ paddingLeft: isCollapsed ? '' : paddingLeft }"
      @click.stop="handleNodeClick"
      :title="isCollapsed ? node.name : ''"
    >
      
      <div class="flex items-center overflow-hidden gap-2">
        <component :is="iconComponent" class="w-4 h-4 shrink-0 transition-colors" 
            :class="isActive ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600'" 
        />

        <span 
          class="text-sm whitespace-nowrap transition-all duration-300 truncate"
          :class="isCollapsed ? 'w-0 opacity-0' : 'w-auto opacity-100'"
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
import { 
  ChevronRight, 
  Factory,      // Level 2 (Facility)
  Zap,          // Level 3 (Utility)
  Settings,     // Level 4 (Equipment) - Thay Package bằng Settings hoặc icon khác tùy ý
  TrendingUp    // Level 1 (Line)
} from 'lucide-vue-next';

const props = defineProps({
  node: { type: Object, required: true },
  depth: { type: Number, default: 0 },
  isCollapsed: { type: Boolean, default: false },
  currentQuery: { type: Object, default: () => ({}) } // Nhận route.query từ cha
});

const emit = defineEmits(['select']);

// State mở rộng/thu gọn
const isOpen = ref(false);

const hasChildren = computed(() => props.node.children && props.node.children.length > 0);

// Tính toán thụt đầu dòng (Padding Left) dựa trên Depth
// Level 0 (Line): 12px, Level 1: +12px...
const paddingLeft = computed(() => `${(props.depth * 12) + 12}px`);

// --- LOGIC QUAN TRỌNG: Xác định node này có đang active không ---
const isActive = computed(() => {
    // Mapping item_type sang query key
    // item_type: 1=Line, 2=Facility, 3=Utility, 4=Equipment
    let typeKey = '';
    if (props.node.item_type === 2) typeKey = 'facility';
    else if (props.node.item_type === 3) typeKey = 'utility';
    else if (props.node.item_type === 4) typeKey = 'equipment';
    
    // So sánh ID trong query URL với ID của node này
    return typeKey && props.currentQuery[typeKey] == props.node.id;
});

// Chọn Icon dựa trên item_type
const iconComponent = computed(() => {
    switch (props.node.item_type) {
        case 1: return TrendingUp; // Line
        case 2: return Factory;    // Facility
        case 3: return Zap;        // Utility
        case 4: return Settings;   // Equipment
        default: return TrendingUp;
    }
});

// Tự động mở cha nếu con đang active
watch(isActive, (val) => {
    if (val) {
        // Logic mở cha sẽ được xử lý tự nhiên do Vue reactivity nếu component cha render lại
        // Tuy nhiên ở đây isOpen là local state. 
        // Để đơn giản, ta chỉ cần đảm bảo khi click thì toggle.
        isOpen.value = true;
    }
}, { immediate: true });

const handleNodeClick = () => {
    // 1. Emit sự kiện select để cha xử lý chuyển trang (hiển thị graph)
    // Chỉ emit nếu là Level 2, 3, 4 (Facility, Utility, Equipment)
    // Level 1 (Line) thường chỉ để group, nhưng nếu muốn click hiển thị graph tổng thì bỏ điều kiện check item_type
    if (props.node.item_type >= 2) {
        emit('select', props.node);
    }
    
    // 2. Nếu có con, cũng toggle mở rộng luôn để tiện thao tác
    if (hasChildren.value) {
        isOpen.value = !isOpen.value;
    }
};

const toggleExpand = () => {
    isOpen.value = !isOpen.value;
};
</script>