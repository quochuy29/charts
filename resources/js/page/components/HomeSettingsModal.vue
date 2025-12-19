<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    
    <div class="bg-white rounded shadow-lg w-[800px] overflow-hidden flex flex-col max-h-[90vh]">
      
      <div class="px-6 py-4 flex justify-between items-center border-b">
        <h2 class="text-xl font-bold text-gray-800">ホーム画面の表示設定</h2>
        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
      </div>

      <div class="p-8 overflow-y-auto">
        
        <div v-if="isLoading" class="text-center py-10 text-gray-500">
          読み込み中... (Đang tải...)
        </div>

        <div v-else>
          <div class="flex mb-2 px-2">
            <div class="w-1/3"></div> <div class="w-1/3 text-center text-gray-600 font-medium bg-gray-50 py-1 rounded mx-2">設備</div>
            <div class="w-1/3 text-center text-gray-600 font-medium bg-gray-50 py-1 rounded mx-2">グラフの種類</div>
          </div>

          <div v-for="(item, index) in localConfig" :key="index" class="flex items-center mb-6 last:mb-0">
            
            <div class="w-1/3 font-bold text-gray-700 pl-2">
              {{ item.label }}
            </div>

            <div class="w-1/3 px-2">
              <select 
                v-model="item.facilityId" 
                class="w-full border border-gray-300 rounded px-3 py-2 text-gray-700 focus:outline-none focus:border-blue-500 bg-white"
              >
                <option v-for="fac in facilitiesList" :key="fac.id" :value="fac.id">
                  {{ fac.name }}
                </option>
              </select>
            </div>

            <div class="w-1/3 px-2">
              <select 
                v-model="item.graphTypeId" 
                class="w-full border border-gray-300 rounded px-3 py-2 text-gray-700 focus:outline-none focus:border-blue-500 bg-white"
              >
                <option v-for="type in graphTypesList" :key="type.id" :value="type.id">
                  {{ type.name }}
                </option>
              </select>
            </div>

          </div>
        </div>
      </div>

      <div class="px-6 py-4 flex justify-center gap-4 border-t bg-gray-50">
        <button 
          @click="handleSave"
          :disabled="isSaving"
          class="px-8 py-2 bg-[#0091D5] hover:bg-blue-600 text-white rounded shadow transition-colors font-medium min-w-[120px]"
        >
          {{ isSaving ? '保存中...' : '保存' }}
        </button>

        <button 
          @click="closeModal" 
          class="px-8 py-2 bg-white border border-[#0091D5] text-[#0091D5] hover:bg-blue-50 rounded shadow transition-colors font-medium min-w-[120px]"
        >
          キャンセル
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { fetchHomeSettings, updateHomeSettings } from '../../services/mockData';

const props = defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close', 'saved']);

// State
const isLoading = ref(false);
const isSaving = ref(false);

const facilitiesList = ref([]);
const graphTypesList = ref([]);
const localConfig = ref([]); // Mảng chứa 4 object config

// Hàm load dữ liệu
const loadData = async () => {
  isLoading.value = true;
  try {
    const data = await fetchHomeSettings();
    facilitiesList.value = data.facilities;
    graphTypesList.value = data.graphTypes;
    
    // Clone deep để không sửa trực tiếp vào mockData khi chưa Save
    localConfig.value = JSON.parse(JSON.stringify(data.config));
  } catch (e) {
    console.error(e);
  } finally {
    isLoading.value = false;
  }
};

// Watch khi modal mở thì load data
watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    loadData();
  }
});

const closeModal = () => {
  emit('close');
};

const handleSave = async () => {
  isSaving.value = true;
  try {
    // Gửi mảng config gồm 4 phần tử lên server
    await updateHomeSettings(localConfig.value);
    emit('saved', localConfig.value);
    closeModal();
  } catch (e) {
    alert('Lỗi lưu dữ liệu');
  } finally {
    isSaving.value = false;
  }
};
</script>

<style scoped>
/* Tùy chỉnh thêm nếu Tailwind chưa đủ */
</style>