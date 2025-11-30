<template>
  <div class="border-b border-gray-200 bg-white p-4 flex flex-col gap-4">
    
    <div class="flex flex-wrap items-center gap-6">
      
      <template v-if="mode === 'dashboard'">
        <div class="inline-flex h-10 items-center justify-center rounded-md bg-gray-100 p-1 text-gray-500">
          <button 
            v-for="m in modes" :key="m.value"
            @click="$emit('update:viewMode', m.value)"
            class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
            :class="viewMode === m.value ? 'bg-white text-black shadow-sm' : 'hover:text-gray-900'"
          >
            {{ m.label }}
          </button>
        </div>

        <div class="relative">
          <input 
            type="date" 
            :value="formattedDate"
            @input="updateDate"
            class="flex h-10 w-full rounded-md border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>
      </template>

      <div v-else class="text-lg font-semibold text-gray-700 mr-auto">
        System Configuration
      </div>

      <button 
        @click="$emit('toggleMode')"
        class="flex items-center gap-2 h-10 px-4 text-sm font-medium rounded-md border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm transition-colors ml-auto"
      >
        <component :is="mode === 'dashboard' ? Settings : BarChart3" class="h-4 w-4" />
        {{ mode === 'dashboard' ? 'Configuration' : 'Graph' }}
      </button>

    </div>

    <div v-if="mode === 'dashboard'" class="flex flex-wrap items-center gap-6">
      <div class="flex gap-2">
        <button 
          v-for="type in unitTypes" 
          :key="type.value"
          @click="$emit('update:unitType', type.value)"
          class="h-9 px-4 text-sm font-medium rounded-md border transition-colors shadow-sm"
          :class="unitType === type.value 
            ? 'bg-white border-primary text-primary ring-1 ring-primary' 
            : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'
          "
        >
          {{ type.label }}
        </button>
      </div>

      <div class="flex items-center gap-2 border-l border-gray-200 pl-6">
        <input 
          type="checkbox" 
          id="showTarget" 
          :checked="showTarget"
          @change="$emit('update:showTarget', $event.target.checked)"
          class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"
        />
        <label for="showTarget" class="text-sm font-medium leading-none cursor-pointer select-none">
          Show Target Line
        </label>
      </div>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue';
import { format } from 'date-fns';
import { Settings, BarChart3 } from 'lucide-vue-next'; // Import thêm icon BarChart3

const props = defineProps(['viewMode', 'unitType', 'selectedDate', 'showTarget', 'mode']); // Thêm prop mode
const emit = defineEmits(['update:viewMode', 'update:unitType', 'update:selectedDate', 'update:showTarget', 'toggleMode']); // Thêm emit toggleMode

const modes = [
  { value: 'daily', label: 'Daily Report' },
  { value: 'period', label: 'Period Report' },
  { value: 'comparison', label: 'Comparison' },
  { value: 'shop-comparison', label: 'Shop Comparison' },
];

const unitTypes = [
  { value: 'energy', label: 'Energy (kWh)' },
  { value: 'cost', label: 'Cost (USD)' },
  { value: 'co2', label: 'CO₂ (kg)' },
];

const formattedDate = computed(() => {
  return props.selectedDate ? format(props.selectedDate, 'yyyy-MM-dd') : '';
});

const updateDate = (e) => {
  const dateValue = e.target.value;
  if (!dateValue) return;
  emit('update:selectedDate', new Date(dateValue));
};
</script>