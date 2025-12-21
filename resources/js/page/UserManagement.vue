<template>
    <div class="p-6 space-y-6 bg-gray-50 min-h-full font-sans text-gray-800">

        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">ユーザー管理</h1>
                <p class="text-sm text-gray-500 mt-1">システムユーザーの管理と権限設定</p>
            </div>
            <button @click="isAddDialogOpen = true"
                class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800 flex items-center gap-2 text-sm font-medium shadow-sm transition-all active:scale-95">
                <component :is="Plus" class="w-4 h-4" />
                <span>新規追加</span>
            </button>
        </div>

        <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[100px]">
                            ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">表示名
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            メールアドレス</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">役割
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">作成日
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-[120px]">
                            操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="loading">
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">読み込み中...</td>
                    </tr>
                    <tr v-else-if="users.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">ユーザーがいません</td>
                    </tr>
                    <tr v-for="user in users" :key="user.id" class="group hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-500">
                            {{ user.user_id }} </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.display_name
                            }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span :class="roleClass(getUserRole(user))"
                                class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full border">
                                {{ getRoleLabel(getUserRole(user)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center gap-2">
                                <button @click="openEditDialog(user)"
                                    class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors"
                                    title="編集">
                                    <component :is="Pencil" class="w-4 h-4" />
                                </button>
                                <button @click="openDeleteDialog(user)"
                                    class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors"
                                    title="削除">
                                    <component :is="Trash2" class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination v-if="users.length > 0" :current-page="currentPage" :total-pages="totalPages"
            :items-per-page="itemsPerPage" :total-items="totalItems" @update:currentPage="handlePageChange" />

        <UserAddModal :is-open="isAddDialogOpen" @close="isAddDialogOpen = false" @save="handleAddUser" />
        <UserEditModal :is-open="isEditDialogOpen" :user="selectedUser" @close="isEditDialogOpen = false"
            @save="handleEditUser" />
        <BaseAlertDialog :is-open="isDeleteDialogOpen" title="削除の確認" @close="isDeleteDialogOpen = false"
            @confirm="executeDelete">
            このユーザーの役割を削除してもよろしいですか？この操作は取り消せません。
        </BaseAlertDialog>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { format } from 'date-fns';
import axios from 'axios';

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

// Pagination State (Server Side)
const currentPage = ref(1);
const totalPages = ref(1);
const itemsPerPage = ref(5);
const totalItems = ref(0);

// --- API FETCH ---
const fetchUsers = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/users?page=${page}`);
        const data = response.data;

        users.value = data.data;
        currentPage.value = data.current_page;
        totalPages.value = data.last_page;
        itemsPerPage.value = data.per_page;
        totalItems.value = data.total;
    } catch (error) {
        console.error("Failed to fetch users:", error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchUsers(1);
});

const handlePageChange = (page) => {
    fetchUsers(page);
};

// --- HELPERS ---
// Hàm lấy tên role từ object user (do quan hệ many-to-many trả về mảng roles)
const getUserRole = (user) => {
    // Nếu user có mảng roles và không rỗng -> lấy role đầu tiên
    if (user.roles && user.roles.length > 0) {
        return user.roles[0].role_name;
    }
    // Fallback nếu không có role nào (mặc định là user thường)
    return 'user';
};

// 2. Hàm style màu sắc cho từng Role
const roleClass = (role) => {
  switch (role) {
      case 'admin':
          return 'bg-red-100 text-red-800 border-red-200';
      case 'display_user':
          return 'bg-purple-100 text-purple-800 border-purple-200'; // Màu tím cho Display User
      case 'user':
      default:
          return 'bg-blue-100 text-blue-800 border-blue-200';
  }
};

// 3. Hàm chuyển đổi tên Role sang tiếng Nhật
const getRoleLabel = (role) => {
    switch(role) {
        case 'admin': 
            return '管理者';       // Admin -> Quản trị viên
        case 'display_user': 
            return '表示用ユーザー'; // Display User -> Người dùng hiển thị
        case 'user': 
            return '一般ユーザー';   // User -> Người dùng thường
        default: 
            return '未設定';       // Default -> Chưa thiết lập
    }
};

const formatDate = (dateStr) => {
    try { return format(new Date(dateStr), "yyyy/MM/dd"); } catch (e) { return dateStr; }
};

// --- HANDLERS (Placeholder cho CRUD tiếp theo) ---
const handleAddUser = async (userData) => {
    try {
        // userData đã bao gồm: user_id, display_name, email, password, role, is_display_user
        await axios.post('/api/users', userData);
        
        alert('ユーザーを追加しました'); // Thông báo thành công
        isAddDialogOpen.value = false;
        
        // Reload lại danh sách (về trang 1 để thấy user mới nhất)
        fetchUsers(1); 
    } catch (error) {
        console.error("Failed to create user:", error);
        if (error.response && error.response.data && error.response.data.message) {
             alert('エラー: ' + error.response.data.message); // Hiển thị lỗi từ BE (ví dụ trùng ID)
        } else {
             alert('ユーザーの追加に失敗しました。');
        }
    }
};

const openEditDialog = (user) => {
    const userForEdit = {
        ...user,
        id: user.user_id, // Để hiển thị input readonly
        original_id: user.id, // <--- Thêm dòng này: Giữ ID gốc để gọi API PUT
        role: getUserRole(user)
    };
    selectedUser.value = userForEdit;
    isEditDialogOpen.value = true;
};

const handleEditUser = async (updatedData) => {
    try {
        // userData từ modal gửi ra
        // Cần truyền ID của bảng (primary key) để route nhận diện
        // Lưu ý: selectedUser.value.id trong logic hiển thị trước đó đang map là user_id string
        // Bạn cần đảm bảo lấy đúng "id" (primary key auto-increment) để gọi API /api/users/{id}
        // Hoặc nếu backend route binding theo id thì dùng id.
        
        // Giả sử selectedUser gốc có trường 'id' (int)
        const id = selectedUser.value.original_id || selectedUser.value.id; 

        await axios.put(`/api/users/${id}`, updatedData);
        
        alert('ユーザー情報を更新しました');
        isEditDialogOpen.value = false;
        selectedUser.value = null;
        fetchUsers(currentPage.value); // Reload data
    } catch (error) {
        console.error("Failed to update user:", error);
        if (error.response?.data?.message) {
            alert('エラー: ' + error.response.data.message);
        } else {
            alert('更新に失敗しました');
        }
    }
};

const openDeleteDialog = (user) => {
    selectedUser.value = user; // user này phải có trường .id (integer primary key)
    isDeleteDialogOpen.value = true;
};

const executeDelete = async () => {
    if (!selectedUser.value) return;

    try {
        // Lấy ID (Primary Key) của user cần xóa
        // Lưu ý: Đảm bảo user object có chứa trường 'id' (int) từ API get list
        const id = selectedUser.value.id; 

        await axios.delete(`/api/users/${id}`);
        
        alert('ユーザーを削除しました'); // Thông báo thành công
        isDeleteDialogOpen.value = false;
        selectedUser.value = null;
        
        // Load lại danh sách user
        fetchUsers(currentPage.value); 

    } catch (error) {
        console.error("Failed to delete user:", error);
        if (error.response && error.response.status === 403) {
             alert('エラー: ' + error.response.data.message); // Lỗi chặn xóa chính mình
        } else {
             alert('削除に失敗しました。');
        }
    }
};
</script>