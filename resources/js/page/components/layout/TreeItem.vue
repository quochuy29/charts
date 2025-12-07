<template>
  <draggable
    class="pl-4"
    :list="safeList"
    item-key="id"
    group="g1"
    :animation="200"
    ghost-class="ghost"
    tag="div"
  >
    <template #item="{ element }">
      <div class="mt-1">
        <div
          class="flex items-center gap-2 px-2 py-1.5 rounded-md cursor-pointer transition-colors group"
          :class="isSelected(element) ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100'"
          @click.stop="onSelect(element)"
        >

          <component :is="getIcon(element.type)" class="w-4 h-4 text-gray-500" />

          <span class="text-sm font-medium truncate flex-1 select-none">
            {{ element.name }}
          </span>
          <span 
            v-if="hasChildren(element)"
            @click.stop="toggleExpand(element.id)"
            class="p-0.5 hover:bg-gray-200 rounded flex items-center justify-center w-5 h-5"
          >
            <ChevronDown v-if="isExpanded(element.id)" class="w-4 h-4" />
            <ChevronRight v-else class="w-4 h-4" />
          </span>
          <span v-else class="w-5 h-5 inline-block"></span>
          </div>

        <TreeItem
          v-if="isExpanded(element.id) && hasChildren(element)"
          :list="element.children"
          :selectedNode="selectedNode"
          @select="$emit('select', $event)"
        />
      </div>
    </template>
  </draggable>
</template>

<script setup>
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';
// Đã xóa import Edit2 và nextTick không dùng đến
import { Factory, TrendingUp, Package, Zap, ChevronRight, ChevronDown } from 'lucide-vue-next';

const props = defineProps({
  list: { type: Array, default: () => [] },
  selectedNode: { type: Object, default: null }
});

const emit = defineEmits(['select']);

const safeList = computed({
  get: () => props.list || [],
  set: (val) => {
    // Logic kéo thả vẫn hoạt động
  }
});

const expandedIds = ref(new Set());
// Đã xóa state editingId và editInput

const toggleExpand = (id) => {
  if (expandedIds.value.has(id)) expandedIds.value.delete(id);
  else expandedIds.value.add(id);
};

const isExpanded = (id) => expandedIds.value.has(id);

const hasChildren = (element) => {
  return element.children && element.children.length > 0;
};

const isSelected = (element) => {
  return props.selectedNode && props.selectedNode.id === element.id;
};

const getIcon = (type) => {
  switch (type) {
    case 'factory': return Factory;
    case 'line': return TrendingUp;
    case 'shop': return Package;
    default: return Zap;
  }
};

const onSelect = (node) => {
  emit('select', { id: node.id, name: node.name, type: node.type });
};

// Đã xóa hàm startEditing và stopEditing
</script>

<style scoped>
.ghost {
  opacity: 0.5;
  background: #e0f2fe;
  border: 1px dashed #0ea5e9;
  border-radius: 4px;
}
</style>