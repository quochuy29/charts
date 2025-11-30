<template>
    <div
        class="bg-white rounded-lg border border-gray-200 shadow-sm h-full flex flex-col overflow-hidden"
    >
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">ユーザー管理</h2>
            <div class="flex justify-end">
                <button
                    @click="openAddModal"
                    class="flex items-center gap-2 px-4 py-2 bg-[hsl(195,85%,45%)] text-white text-sm font-semibold rounded-md shadow-sm hover:bg-[hsl(195,85%,40%)] transition-all active:scale-95"
                >
                    <UserPlus class="h-4 w-4" />
                    ユーザー追加
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-auto p-6">
            <table class="w-full text-sm text-left">
                <thead
                    class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10"
                >
                    <tr>
                        <th
                            class="py-3 px-4 font-semibold text-gray-600 text-center"
                        >
                            ユーザー名
                        </th>
                        <th
                            class="py-3 px-4 font-semibold text-gray-600 text-center"
                        >
                            氏名
                        </th>
                        <th
                            class="py-3 px-4 font-semibold text-gray-600 text-center"
                        >
                            役割
                        </th>
                        <th
                            class="py-3 px-4 font-semibold text-gray-600 text-center"
                        >
                            状態
                        </th>
                        <th
                            class="py-3 px-4 font-semibold text-gray-600 text-center"
                        >
                            操作
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="user in users"
                        :key="user.id"
                        class="hover:bg-gray-50/80 transition-colors"
                    >
                        <td
                            class="py-4 px-4 font-medium text-gray-900 text-center"
                        >
                            {{ user.username }}
                        </td>
                        <td class="py-4 px-4 text-gray-700 text-center">
                            {{ user.fullName }}
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                :class="
                                    user.role === 'admin'
                                        ? 'bg-red-50 text-red-700 border-red-200'
                                        : 'bg-green-50 text-green-700 border-green-200'
                                "
                            >
                                {{
                                    user.role === "admin"
                                        ? "管理者"
                                        : "一般ユーザー"
                                }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <button
                                @click="toggleStatus(user)"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                                :class="
                                    user.isActive
                                        ? 'bg-[hsl(195,85%,45%)]'
                                        : 'bg-gray-200'
                                "
                            >
                                <span
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"
                                    :class="
                                        user.isActive
                                            ? 'translate-x-6'
                                            : 'translate-x-1'
                                    "
                                />
                            </button>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <button
                                    @click="openEditModal(user)"
                                    class="p-1.5 text-gray-700 hover:bg-gray-100 rounded-md transition-colors"
                                    title="編集"
                                >
                                    <Edit class="h-4 w-4" />
                                </button>
                                <button
                                    @click="openDeleteModal(user)"
                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-md transition-colors"
                                    title="削除"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <UserFormModal
            :isOpen="isFormModalOpen"
            :mode="formMode"
            :initialData="selectedUser"
            @close="isFormModalOpen = false"
            @save="handleSaveUser"
        />

        <div
            v-if="isDeleteModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity"
        >
            <div
                class="bg-white rounded-xl shadow-2xl w-full max-w-sm m-4 animate-in fade-in zoom-in-95 duration-200 overflow-hidden"
            >
                <div class="p-6 text-center">
                    <div
                        class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4"
                    >
                        <Trash2 class="h-6 w-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                        ユーザー削除
                    </h3>
                    <p class="text-sm text-gray-500">
                        本当に
                        <span class="font-bold text-gray-800">{{
                            targetUser?.fullName
                        }}</span>
                        を削除しますか？<br />この操作は取り消せません。
                    </p>
                </div>
                <div
                    class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center gap-3"
                >
                    <button
                        @click="isDeleteModalOpen = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none w-full"
                    >
                        キャンセル
                    </button>
                    <button
                        @click="handleDeleteUser"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none shadow-sm w-full"
                    >
                        削除する
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { UserPlus, Edit, Trash2 } from "lucide-vue-next";
import UserFormModal from "./UserFormModal.vue"; // Import component mới

// --- DATA ---
const users = ref([
    {
        id: 1,
        username: "admin_01",
        fullName: "山田 太郎",
        role: "admin",
        isActive: true,
    },
    {
        id: 2,
        username: "user_dev",
        fullName: "鈴木 一郎",
        role: "user",
        isActive: true,
    },
    {
        id: 3,
        username: "user_guest",
        fullName: "佐藤 花子",
        role: "user",
        isActive: false,
    },
    {
        id: 4,
        username: "manager_opt",
        fullName: "田中 次郎",
        role: "admin",
        isActive: true,
    },
    {
        id: 5,
        username: "intern_01",
        fullName: "高橋 三郎",
        role: "user",
        isActive: false,
    },
]);

// --- STATE QUẢN LÝ MODAL ---
const isFormModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const formMode = ref("add");
const selectedUser = ref(null); // User được chọn để sửa
const targetUser = ref(null); // User được chọn để xóa

// --- ACTIONS: ADD / EDIT ---
const openAddModal = () => {
    formMode.value = "add";
    selectedUser.value = null; // Reset data
    isFormModalOpen.value = true;
};

const openEditModal = (user) => {
    formMode.value = "edit";
    selectedUser.value = { ...user }; // Clone object để tránh reference trực tiếp
    isFormModalOpen.value = true;
};

// Hàm xử lý khi FormModal emit sự kiện 'save'
const handleSaveUser = (userData) => {
    if (formMode.value === "add") {
        // Logic thêm mới
        const newUser = {
            ...userData,
            id: Date.now(), // Fake ID
        };
        users.value.push(newUser);
    } else {
        // Logic cập nhật
        const index = users.value.findIndex((u) => u.id === userData.id);
        if (index !== -1) {
            users.value[index] = userData;
        }
    }
    isFormModalOpen.value = false;
};

// --- ACTIONS: DELETE ---
const openDeleteModal = (user) => {
    targetUser.value = user;
    isDeleteModalOpen.value = true;
};

const handleDeleteUser = () => {
    if (targetUser.value) {
        users.value = users.value.filter((u) => u.id !== targetUser.value.id);
    }
    isDeleteModalOpen.value = false;
    targetUser.value = null;
};

// --- ACTIONS: TOGGLE STATUS ---
const toggleStatus = (user) => {
    user.isActive = !user.isActive;
};
</script>
