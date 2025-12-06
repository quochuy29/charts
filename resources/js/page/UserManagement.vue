<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý người dùng</h1>
      <button @click="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center">
        <span>+ Thêm mới</span>
      </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên hiển thị</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.display_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span :class="roleClass(user.role)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ user.role === 'admin' ? 'Quản trị viên' : 'Người dùng' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.created_at }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openModal(user)" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</button>
              <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="bg-white p-5 rounded-lg shadow-xl w-96">
        <h2 class="text-xl font-bold mb-4">{{ isEditing ? 'Cập nhật' : 'Thêm mới' }} người dùng</h2>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2">Tên hiển thị</label>
          <input v-model="formData.display_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
          <input v-model="formData.email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2">Vai trò</label>
          <select v-model="formData.role" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="user">Người dùng</option>
            <option value="admin">Quản trị viên</option>
          </select>
        </div>
        <div class="flex justify-end gap-2">
          <button @click="showModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Hủy</button>
          <button @click="saveUser" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Lưu</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { mockUsers } from '../services/mockData';

const users = mockUsers;
const showModal = ref(false);
const isEditing = ref(false);
const formData = reactive({ id: null, display_name: '', email: '', role: 'user' });

const roleClass = (role) => {
  return role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
};

const openModal = (user = null) => {
  if (user) {
    isEditing.value = true;
    Object.assign(formData, user);
  } else {
    isEditing.value = false;
    Object.assign(formData, { id: null, display_name: '', email: '', role: 'user' });
  }
  showModal.value = true;
};

const saveUser = () => {
  if (isEditing.value) {
    const index = users.findIndex(u => u.id === formData.id);
    if (index !== -1) Object.assign(users[index], formData);
  } else {
    users.push({ ...formData, id: Date.now().toString(), created_at: new Date().toISOString().split('T')[0] });
  }
  showModal.value = false;
};

const deleteUser = (id) => {
  if(confirm('Bạn có chắc chắn muốn xóa?')) {
    const index = users.findIndex(u => u.id === id);
    if (index !== -1) users.splice(index, 1);
  }
};
</script>