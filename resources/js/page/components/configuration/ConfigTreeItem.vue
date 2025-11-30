<template>
    <draggable
        class="pl-6 border-l border-dashed border-gray-200 ml-2 min-h-[10px]"
        :list="list"
        item-key="id"
        :group="dragGroupName"
        :animation="200"
        ghost-class="ghost"
        handle=".drag-handle"
        tag="div"
    >
        <template #item="{ element, index }">
            <div class="mt-2 relative">
                <div
                    class="flex items-center gap-2 px-3 py-2 rounded-md border border-transparent hover:border-gray-200 hover:bg-gray-50 transition-all group bg-white shadow-sm"
                >
                    <span
                        class="drag-handle cursor-move p-1 text-gray-400 hover:text-gray-600"
                    >
                        <GripVertical class="w-4 h-4" />
                    </span>

                    <span
                        v-if="hasChildren(element)"
                        @click="toggleExpand(element.id)"
                        class="p-1 hover:bg-gray-200 rounded cursor-pointer text-gray-500"
                    >
                        <ChevronDown
                            v-if="isExpanded(element.id)"
                            class="w-4 h-4"
                        />
                        <ChevronRight v-else class="w-4 h-4" />
                    </span>
                    <span v-else class="w-6"></span>

                    <component
                        :is="getIcon(element.type)"
                        class="w-4 h-4 text-[hsl(195,85%,45%)]"
                    />

                    <div v-if="editingId === element.id" class="flex-1 mr-2">
                        <input
                            ref="editInputRef"
                            v-model="element.name"
                            class="w-full px-2 py-1 text-sm border border-[hsl(195,85%,45%)] rounded focus:outline-none focus:ring-1 focus:ring-[hsl(195,85%,45%)]"
                            @blur="stopEditing"
                            @keyup.enter="stopEditing"
                            autoFocus
                        />
                    </div>
                    <span
                        v-else
                        class="text-sm font-medium text-gray-700 flex-1 truncate select-none"
                    >
                        {{ element.name }}
                    </span>

                    <div
                        class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                        <button
                            @click="startEditing(element)"
                            class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded"
                            title="Rename"
                        >
                            <Edit2 class="w-3.5 h-3.5" />
                        </button>
                        <button
                            @click="handleDelete(index)"
                            class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded"
                            title="Delete"
                        >
                            <Trash2 class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>

                <div
                    v-if="
                        isExpanded(element.id) && element.type !== 'equipment'
                    "
                    class="mb-2"
                >
                    <ConfigTreeItem
                        :list="element.children"
                        :parentType="element.type"
                    />
                </div>
            </div>
        </template>
    </draggable>
</template>

<script setup>
import { ref, computed, nextTick } from "vue";
import draggable from "vuedraggable";
import {
    Factory,
    TrendingUp,
    Package,
    Zap,
    ChevronRight,
    ChevronDown,
    Edit2,
    Trash2,
    GripVertical,
} from "lucide-vue-next";

const props = defineProps({
    list: { type: Array, required: true },
    parentType: { type: String, default: "root" }, // root | factory | line | shop
});

// --- LOGIC PHÂN QUYỀN KÉO THẢ ---
// Tên group được sinh ra dựa trên loại cha.
// Ví dụ: Nếu đang ở trong node Factory, danh sách này chỉ chấp nhận các item thuộc nhóm 'tree-factory' (tức là Line).
const dragGroupName = computed(() => {
    return `tree-${props.parentType}`;
});

// State & Logic khác giữ nguyên
const expandedIds = ref(new Set());
const editingId = ref(null);
const editInputRef = ref(null);

const toggleExpand = (id) => {
    if (expandedIds.value.has(id)) expandedIds.value.delete(id);
    else expandedIds.value.add(id);
};

const hasChildren = (element) =>
    element.children && element.children.length > 0;
const isExpanded = (id) => expandedIds.value.has(id) || true;

const getIcon = (type) => {
    switch (type) {
        case "factory":
            return Factory;
        case "line":
            return TrendingUp;
        case "shop":
            return Package;
        default:
            return Zap;
    }
};

const startEditing = (node) => {
    editingId.value = node.id;
    nextTick(() => {
        if (Array.isArray(editInputRef.value)) {
            const input =
                document.querySelector("input:focus") ||
                document.querySelector("input");
            if (input) input.focus();
        }
    });
};

const stopEditing = () => {
    editingId.value = null;
};

const handleDelete = (index) => {
    if (confirm("このノードを削除しますか？")) {
        props.list.splice(index, 1);
    }
};
</script>

<style scoped>
.ghost {
    opacity: 0.5;
    background: #e0f2fe;
    border: 1px dashed #0ea5e9;
    border-radius: 6px;
}
</style>
