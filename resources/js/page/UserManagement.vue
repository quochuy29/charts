<template>
  <div class="p-6 space-y-6 bg-gray-50 min-h-full font-sans text-gray-800">
    
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">ユーザー管理</h1>
        <p class="text-sm text-gray-500 mt-1">システムユーザーの管理と権限設定</p>
      </div>
      <button @click="isAddDialogOpen = true" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800 flex items-center gap-2 text-sm font-medium shadow-sm transition-all active:scale-95">
        <component :is="Plus" class="w-4 h-4" />
        <span>新規追加</span>
      </button>
    </div>

    <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50/50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[100px]">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">表示名</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">メールアドレス</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">役割</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">作成日</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-[120px]">操作</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="loading">
             <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">読み込み中...</td>
          </tr>
          <tr v-else-if="users.length === 0">
             <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">ユーザーがいません</td>
          </tr>
          <tr v-for="user in paginatedUsers" :key="user.id" class="group hover:bg-gray-50/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-500">
              {{ user.id.length > 8 ? user.id.substring(0, 8) + '...' : user.id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.display_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span :class="roleClass(user.role)" class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full border">
                {{ getRoleLabel(user.role) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end items-center gap-2">
                <button @click="openEditDialog(user)" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors" title="編集">
                  <component :is="Pencil" class="w-4 h-4" />
                </button>
                <button @click="openDeleteDialog(user)" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors" title="削除">
                  <component :is="Trash2" class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Pagination 
      :current-page="currentPage" 
      :total-pages="totalPages" 
      :items-per-page="itemsPerPage"
      :total-items="users.length"
      @update:currentPage="currentPage = $event" 
    />

    <UserAddModal 
      :is-open="isAddDialogOpen" 
      @close="isAddDialogOpen = false" 
      @save="handleAddUser" 
    />

    <UserEditModal 
      :is-open="isEditDialogOpen" 
      :user="selectedUser"
      @close="isEditDialogOpen = false" 
      @save="handleEditUser" 
    />

    <BaseAlertDialog 
      :is-open="isDeleteDialogOpen" 
      title="削除の確認" 
      @close="isDeleteDialogOpen = false" 
      @confirm="executeDelete"
    >
      このユーザーの役割を削除してもよろしいですか？この操作は取り消せません。
    </BaseAlertDialog>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { format } from 'date-fns';

// Components
import Pagination from './components/common/Pagination.vue';
import BaseAlertDialog from './components/common/BaseAlertDialog.vue';
import UserAddModal from './components/user/UserAddModal.vue';
import UserEditModal from './components/user/UserEditModal.vue';

// --- STATE ---
const users = ref([]);
const loading = ref(true);
const isAddDialogOpen = ref(false);
const isEditDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const selectedUser = ref(null);

const currentPage = ref(1);
const itemsPerPage = 5;

// --- LIFECYCLE ---
onMounted(() => {
    loadUsers();
});

const loadUsers = () => {
    loading.value = true;
    setTimeout(() => {
        const hardcodedUsers = [
            { id: "HuyPQ1", display_name: "HuyPQ", email: "huypq@vnext.vn", role: "admin", created_at: "2025-01-01" },
            { id: "PhucND1", display_name: "PhucND", email: "phucnd@vnext.vn", role: "user", created_at: "2025-01-01" },
            { id: "ThangNV1", display_name: "ThangNV", email: "thangnv@vnext.vn", role: "user", created_at: "2025-01-01" },
            { id: "AnhNQ1", display_name: "AnhNQ", email: "anhnq@vnext.vn", role: "user", created_at: "2025-01-01" },
            { id: "ThuyNVT1", display_name: "ThuyNVT", email: "thuynvt@vnext.vn", role: "user", created_at: "2025-01-01" },
            { id: "ThinhLD1", display_name: "ThinhLD", email: "thinhld@vnext.vn", role: "user", created_at: "2025-01-01" },
            { id: "NguyetLTA1", display_name: "NguyetLTA", email: "nguyetlta@vnext.vn", role: "user", created_at: "2025-01-01" },
            { id: "TrinhPP1", display_name: "TrinhPP", email: "trinhpp@vnext.vn", role: "user", created_at: "2025-01-01" },
            { id: "HueNTB1", display_name: "HueNTB", email: "huentb@vnext.vn", role: "user", created_at: "2025-01-01" },
        ];
        users.value = [...hardcodedUsers]; 
        loading.value = false;
    }, 500);
};

// --- COMPUTED ---
const totalPages = computed(() => Math.ceil(users.value.length / itemsPerPage));

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return users.value.slice(start, end);
});

// --- HELPERS ---
const roleClass = (role) => {
  if (role === 'admin') return 'bg-red-100 text-red-800 border-red-200';
  if (role === 'manager') return 'bg-yellow-100 text-yellow-800 border-yellow-200';
  return 'bg-blue-100 text-blue-800 border-blue-200';
};

const getRoleLabel = (role) => {
    switch(role) {
        case 'admin': return '管理者';
        case 'manager': return 'マネージャー';
        case 'user': return 'ユーザー';
        default: return '未設定';
    }
};

const formatDate = (dateStr) => {
    try { return format(new Date(dateStr), "yyyy/MM/dd"); } catch (e) { return dateStr; }
};

// --- HANDLERS ---
const handleAddUser = (userData) => {
    const newUser = {
        id: userData.user_id,
        display_name: userData.display_name,
        email: userData.email,
        role: userData.role,
        created_at: new Date().toISOString()
    };
    users.value.unshift(newUser);
    alert('ユーザーを追加しました');
};

const openEditDialog = (user) => {
    selectedUser.value = user;
    isEditDialogOpen.value = true;
};

const handleEditUser = (updatedData) => {
    const index = users.value.findIndex(u => u.id === updatedData.id);
    if (index !== -1) {
        users.value[index] = { ...users.value[index], ...updatedData };
        alert("ユーザー情報を更新しました");
    }
    isEditDialogOpen.value = false;
    selectedUser.value = null;
};

const openDeleteDialog = (user) => {
    selectedUser.value = user;
    isDeleteDialogOpen.value = true;
};

const executeDelete = () => {
    if (selectedUser.value) {
        users.value = users.value.filter(u => u.id !== selectedUser.value.id);
        alert('ユーザーを削除しました');
        if (paginatedUsers.value.length === 0 && currentPage.value > 1) {
            currentPage.value--;
        }
        isDeleteDialogOpen.value = false;
        selectedUser.value = null;
    }
};
</script>