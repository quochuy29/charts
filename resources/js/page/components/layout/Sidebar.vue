<template>
  <aside class="w-[260px] bg-white text-slate-700 flex flex-col h-full border-r border-gray-200 font-sans transition-all duration-300">
    
    <div class="flex-1 overflow-y-auto py-6 px-3 space-y-6">
      
      <div>
        <div class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">メインメニュー</div>
        <nav class="space-y-1">
          <router-link to="/production-planning" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 hover:text-slate-900 transition-colors group" :class="{ 'bg-blue-50 text-blue-700 font-medium': $route.path === '/production-planning' }">
            <Calendar class="w-4 h-4 mr-3 text-slate-500 group-hover:text-slate-900" /> 生産計画設定
          </router-link>
          <router-link to="/user-management" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 hover:text-slate-900 transition-colors group" :class="{ 'bg-blue-50 text-blue-700 font-medium': $route.path === '/user-management' }">
            <Users class="w-4 h-4 mr-3 text-slate-500 group-hover:text-slate-900" /> ユーザー管理
          </router-link>
          <router-link to="/data-maintenance" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100 hover:text-slate-900 transition-colors group" :class="{ 'bg-blue-50 text-blue-700 font-medium': $route.path === '/data-maintenance' }">
            <Database class="w-4 h-4 mr-3 text-slate-500 group-hover:text-slate-900" /> データ保守
          </router-link>
        </nav>
      </div>

      <div v-if="treeData.length > 0">
        <div class="px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
            <BarChart3 class="w-4 h-4" /> グラフ表示
        </div>
        <div class="space-y-1">
          <div v-for="line in treeData" :key="line.id">
            <button @click="toggle(line.id)" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-100 transition-colors text-sm font-medium">
              {{ line.name }}
              <ChevronRight class="w-4 h-4 text-slate-400 transition-transform" :class="{'rotate-90': isOpen(line.id)}" />
            </button>
            
            <div v-show="isOpen(line.id)" class="ml-4 border-l border-gray-200 pl-2 mt-1 space-y-1">
              <div v-for="facility in line.children" :key="facility.id">
                <div class="flex items-center group">
                    <div @click="nav('facility', facility.id)" class="flex-1 px-2 py-1.5 rounded-md hover:bg-gray-100 cursor-pointer text-sm" :class="isSelected('facility', facility.id) ? 'text-blue-700 bg-blue-50 font-medium' : ''">
                        {{ facility.name }}
                    </div>
                    <button v-if="hasChildren(facility)" @click.stop="toggle(facility.id)" class="p-1 hover:bg-gray-200 rounded">
                         <ChevronRight class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="{'rotate-90': isOpen(facility.id)}" />
                    </button>
                </div>

                <div v-show="isOpen(facility.id)" class="ml-3 border-l border-gray-200 pl-2 mt-1 space-y-1">
                    <div v-for="utility in facility.children" :key="utility.id">
                        <div class="flex items-center group">
                            <div @click="nav('utility', utility.id)" class="flex-1 px-2 py-1.5 rounded-md hover:bg-gray-100 cursor-pointer text-sm" :class="isSelected('utility', utility.id) ? 'text-blue-700 bg-blue-50 font-medium' : ''">
                                {{ utility.name }}
                            </div>
                            <button v-if="hasChildren(utility)" @click.stop="toggle(utility.id)" class="p-1 hover:bg-gray-200 rounded">
                                <ChevronRight class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="{'rotate-90': isOpen(utility.id)}" />
                            </button>
                        </div>

                        <div v-show="isOpen(utility.id)" class="ml-3 border-l border-gray-200 pl-2 mt-1 space-y-1">
                             <div v-for="equip in utility.children" :key="equip.id" 
                                @click="nav('equipment', equip.id)"
                                class="px-2 py-1.5 rounded-md hover:bg-gray-100 cursor-pointer text-sm"
                                :class="isSelected('equipment', equip.id) ? 'text-blue-700 bg-blue-50 font-medium' : ''">
                                {{ equip.name }}
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </aside>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { Calendar, Users, Database, BarChart3, ChevronRight } from 'lucide-vue-next';
import { mockTreeData } from '../../../services/mockData';

const router = useRouter();
const route = useRoute();
const treeData = mockTreeData;
const openItems = ref({});

const toggle = (id) => { openItems.value[id] = !openItems.value[id]; };
const isOpen = (id) => !!openItems.value[id];
const hasChildren = (node) => node.children && node.children.length > 0;

const nav = (type, id) => {
    router.push({ path: '/dashboard', query: { [type]: id } });
};

const isSelected = (type, id) => route.query[type] === id;
</script>