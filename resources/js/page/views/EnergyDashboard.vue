<template>
  <div class="flex h-screen w-full bg-gray-50 overflow-hidden">
    
    <Sidebar 
      class="hidden md:flex" 
      mode="dashboard"
      :selectedNode="selectedNode"
      @update:selectedNode="selectedNode = $event"
    />
    
    <div class="flex-1 flex flex-col overflow-hidden relative">
      
      <ControlPanel
        v-model:viewMode="viewMode"
        v-model:unitType="unitType"
        v-model:selectedDate="selectedDate"
        v-model:showTarget="showTarget"
        mode="dashboard"
        @toggleMode="goToConfiguration"
      />

      <div class="flex-1 p-6 overflow-hidden min-h-0">
        <ChartArea
          :viewMode="viewMode"
          :unitType="unitType"
          :selectedDate="selectedDate"
          :showTarget="showTarget"
          :selectedNode="selectedNode"
          :yAxisConfig="yAxisConfig"
        >
          <template #actions>
            <button
              @click="dataTableOpen = true"
              class="flex items-center h-9 px-4 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm transition-colors border border-gray-200"
            >
              <Table2 class="h-4 w-4 mr-2" />
              Data Table
            </button>
            <button
              @click="yAxisSettingsOpen = true"
              class="flex items-center h-9 px-4 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm transition-colors border border-gray-200"
            >
              <Settings2 class="h-4 w-4 mr-2" />
              Y-Axis Settings
            </button>
          </template>
        </ChartArea>
      </div>
    </div>

    <DataTableDialog
      v-model:open="dataTableOpen"
      :viewMode="viewMode"
      :unitType="unitType"
      :selectedDate="selectedDate"
      :selectedNode="selectedNode"
    />

    <YAxisSettingsDialog
      v-model:open="yAxisSettingsOpen"
      :currentConfig="yAxisConfig"
      @apply-settings="handleApplyYAxis"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router'; // Import router
import { Table2, Settings2 } from 'lucide-vue-next';

// Import Components
import Sidebar from '../components/dashboard/Sidebar.vue';
import ControlPanel from '../components/dashboard/ControlPanel.vue';
import ChartArea from '../components/dashboard/ChartArea.vue';
import DataTableDialog from '../components/dashboard/DataTableDialog.vue';
import YAxisSettingsDialog from '../components/dashboard/YAxisSettingsDialog.vue';

const router = useRouter();

// --- DASHBOARD STATE ---
const viewMode = ref('daily');
const unitType = ref('energy');
const selectedDate = ref(new Date());
const showTarget = ref(true);
const selectedNode = ref({
  id: "factory-1",
  name: "Factory 1 - Main Production",
  type: "factory",
});

// --- DIALOG STATE ---
const dataTableOpen = ref(false);
const yAxisSettingsOpen = ref(false);
const yAxisConfig = ref({ min: null, max: null, interval: null });

const handleApplyYAxis = (newConfig) => {
  yAxisConfig.value = newConfig;
};

// --- NAVIGATION ---
// Thay vì đổi biến state nội bộ, ta chuyển hướng trang
const goToConfiguration = () => {
  router.push({ name: 'user-management' }); // Mặc định vào trang con đầu tiên
};
</script>