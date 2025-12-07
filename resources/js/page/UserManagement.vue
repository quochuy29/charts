<template>
  <div class="p-6 space-y-6 bg-gray-50 min-h-full font-sans text-gray-800">
    
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">ユーザー管理</h1>
        <p class="text-sm text-gray-500 mt-1">システムユーザーの管理と権限設定</p>
      </div>
      <button @click="openAddModal" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800 flex items-center gap-2 text-sm font-medium shadow-sm transition-all active:scale-95">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
        <span>ユーザー追加</span>
      </button>
    </div>

    <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50/50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[25%]">表示名</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[30%]">メールアドレス</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[20%]">役割</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">作成日</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">操作</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in paginatedUsers" :key="user.id" class="group hover:bg-gray-50/50 transition-colors">
            
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <div v-if="editingId === user.id">
                <input v-model="editForm.display_name" class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
              </div>
              <div v-else class="font-medium text-gray-900">{{ user.display_name }}</div>
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div v-if="editingId === user.id">
                <input v-model="editForm.email" class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
              </div>
              <span v-else>{{ user.email }}</span>
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <div v-if="editingId === user.id">
                 <select v-model="editForm.role" class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                    <option value="admin">管理者</option>
                    <option value="user">一般ユーザー</option>
                 </select>
              </div>
              <span v-else :class="roleClass(user.role)" class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full border">
                {{ user.role === 'admin' ? '管理者' : '一般ユーザー' }}
              </span>
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ user.created_at }}
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              
              <div v-if="editingId === user.id" class="flex justify-end items-center gap-2">
                <button @click="saveEdit" class="p-1.5 text-green-600 hover:bg-green-50 rounded-md transition-colors" title="保存">
                  <component :is="Check" class="w-4 h-4" />
                </button>
                <button @click="cancelEdit" class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-md transition-colors" title="キャンセル">
                  <component :is="X" class="w-4 h-4" />
                </button>
              </div>

              <div v-else class="flex justify-end items-center gap-2">
                <button @click="startEdit(user)" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors" title="編集">
                  <component :is="Pencil" class="w-4 h-4" />
                </button>
                <button @click="confirmDelete(user)" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors" title="削除">
                  <component :is="Trash2" class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex items-center justify-end space-x-2 py-2">
        <button 
          @click="currentPage > 1 ? currentPage-- : null"
          :disabled="currentPage === 1"
          class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          前へ
        </button>
        <span class="text-sm text-gray-600 px-2">
           {{ currentPage }} / {{ totalPages }}
        </span>
        <button 
          @click="currentPage < totalPages ? currentPage++ : null"
          :disabled="currentPage === totalPages"
          class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          次へ
        </button>
    </div>

    <div v-if="showAddModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm">
      <div class="bg-white p-6 rounded-lg shadow-xl w-[400px]">
        <h2 class="text-lg font-bold mb-4">新規ユーザー追加</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">表示名</label>
            <input v-model="addForm.display_name" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">メールアドレス</label>
            <input v-model="addForm.email" type="email" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">役割</label>
            <select v-model="addForm.role" class="w-full border rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
              <option value="user">一般ユーザー</option>
              <option value="admin">管理者</option>
            </select>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <button @click="showAddModal = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">キャンセル</button>
          <button @click="saveNewUser" class="px-4 py-2 text-sm bg-gray-900 text-white rounded-md hover:bg-gray-800">追加</button>
        </div>
      </div>
    </div>

    <div v-if="userToDelete" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm">
       <div class="bg-white p-6 rounded-lg shadow-xl w-[400px] transform transition-all scale-100">
          <h3 class="text-lg font-bold text-gray-900">本当に削除しますか？</h3>
          <p class="mt-2 text-sm text-gray-500">
             この操作は取り消せません。ユーザー <strong>{{ userToDelete.display_name }}</strong> はシステムから完全に削除されます。
          </p>
          <div class="mt-6 flex justify-end gap-3">
             <button @click="userToDelete = null" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                キャンセル
             </button>
             <button @click="executeDelete" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">
                削除する
             </button>
          </div>
       </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Pencil, Trash2, Check, X } from 'lucide-vue-next'; // Import Icon
import { mockUsers } from '../services/mockData';

// --- State ---
// Tạo thêm data giả để test phân trang
const generateMoreUsers = () => {
    const list = [...mockUsers];
    for (let i = 4; i <= 12; i++) {
        list.push({
            id: i.toString(),
            display_name: `ユーザー ${i}`,
            email: `user${i}@toyota.com`,
            role: i % 3 === 0 ? 'admin' : 'user',
            created_at: '2025-01-10'
        });
    }
    return list;
};

const users = ref(generateMoreUsers()); // Dùng ref để có thể sửa/xóa trực tiếp
const editingId = ref(null);
const editForm = reactive({ id: null, display_name: '', email: '', role: '' });

// Pagination
const currentPage = ref(1);
const itemsPerPage = 5;

// Modal States
const showAddModal = ref(false);
const addForm = reactive({ display_name: '', email: '', role: 'user' });
const userToDelete = ref(null);

// --- Computed Properties ---
const totalPages = computed(() => Math.ceil(users.value.length / itemsPerPage));

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return users.value.slice(start, end);
});

// --- Methods ---
const roleClass = (role) => {
  return role === 'admin' 
    ? 'bg-red-50 text-red-700 border-red-200' 
    : 'bg-blue-50 text-blue-700 border-blue-200';
};

// 1. Inline Edit Logic
const startEdit = (user) => {
    editingId.value = user.id;
    // Clone data vào form
    Object.assign(editForm, user);
};

const saveEdit = () => {
    const index = users.value.findIndex(u => u.id === editForm.id);
    if (index !== -1) {
        // Cập nhật lại list
        Object.assign(users.value[index], editForm);
    }
    editingId.value = null; // Tắt chế độ edit
};

const cancelEdit = () => {
    editingId.value = null;
};

// 2. Add Logic
const openAddModal = () => {
    Object.assign(addForm, { display_name: '', email: '', role: 'user' });
    showAddModal.value = true;
};

const saveNewUser = () => {
    const newUser = {
        id: Date.now().toString(),
        ...addForm,
        created_at: new Date().toISOString().split('T')[0]
    };
    users.value.unshift(newUser); // Thêm lên đầu
    showAddModal.value = false;
};

// 3. Delete Logic (Custom Dialog)
const confirmDelete = (user) => {
    userToDelete.value = user; // Mở dialog
};

const executeDelete = () => {
    if (userToDelete.value) {
        users.value = users.value.filter(u => u.id !== userToDelete.value.id);
        userToDelete.value = null; // Đóng dialog
        
        // Reset về trang 1 nếu trang hiện tại hết data
        if (paginatedUsers.value.length === 0 && currentPage.value > 1) {
            currentPage.value--;
        }
    }
};
</script>