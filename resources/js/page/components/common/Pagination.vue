<template>
    <div v-if="totalPages > 1" class="flex items-center justify-between px-2 py-2 select-none w-full">
        <div class="text-sm text-gray-500 font-medium">
            {{ startItem }} - {{ endItem }} / {{ totalItems }} 件
        </div>

        <div class="flex items-center gap-2">
            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                class="flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                前へ
            </button>

            <span class="text-sm text-gray-600 px-2 font-medium">
                {{ currentPage }} / {{ totalPages }}
            </span>

            <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages"
                class="flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                次へ
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1">
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
    import { computed } from 'vue';

    const props = defineProps({
        currentPage: { type: Number, required: true },
        totalPages: { type: Number, required: true },
        itemsPerPage: { type: Number, default: 5 }, // Thêm prop này
        totalItems: { type: Number, default: 0 }      // Thêm prop này
    });

    const emit = defineEmits(['update:currentPage']);

    // Tính toán số thứ tự bản ghi bắt đầu và kết thúc
    const startItem = computed(() => (props.currentPage - 1) * props.itemsPerPage + 1);
    const endItem = computed(() => Math.min(props.currentPage * props.itemsPerPage, props.totalItems));

    const changePage = (page) => {
        if (page >= 1 && page <= props.totalPages) {
            emit('update:currentPage', page);
        }
    };
</script>