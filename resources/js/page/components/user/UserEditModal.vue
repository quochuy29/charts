<template>
    <BaseModal :isOpen="isOpen" title="ユーザー編集" @close="close">
        <div class="space-y-4">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">ユーザーID <span
                        class="text-red-500">*</span></label>
                <input v-model="form.id"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none bg-gray-100 text-gray-500"
                    readonly>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">表示名 <span class="text-red-500">*</span></label>
                <input v-model="form.display_name"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                    :class="{'border-red-500': errors.display_name}">
                <p v-if="errors.display_name" class="text-xs text-red-500">{{ errors.display_name }}</p>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">メールアドレス <span
                        class="text-red-500">*</span></label>
                <input v-model="form.email" type="email"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                    :class="{'border-red-500': errors.email}">
                <p v-if="errors.email" class="text-xs text-red-500">{{ errors.email }}</p>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">役割 <span class="text-red-500">*</span></label>
                <select v-model="form.role"
                    class="w-full border rounded-md px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <option value="user">一般ユーザー</option>
                    <option value="manager">マネージャー</option>
                    <option value="admin">管理者</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" id="edit_is_display_user" v-model="form.is_display_user"
                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-900" :disabled="form.role !== 'user'">
                <label for="edit_is_display_user" class="text-sm font-medium text-gray-700"
                    :class="{'text-gray-400': form.role !== 'user'}">表示用ユーザー</label>
            </div>
        </div>

        <template #footer>
            <button @click="close"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">キャンセル</button>
            <button @click="save"
                class="px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-800 transition-colors shadow-sm">保存</button>
        </template>
    </BaseModal>
</template>

<script setup>
    import { reactive, watch, toRefs } from 'vue';
    import BaseModal from '../common/BaseModal.vue';

    const props = defineProps(['isOpen', 'user']);
    const emit = defineEmits(['close', 'save']);

    const form = reactive({ id: "", display_name: "", email: "", role: "user", is_display_user: false });
    const errors = reactive({});

    // Khi prop 'user' thay đổi (mở modal), load dữ liệu vào form
    watch(() => props.user, (newUser) => {
        if (newUser) {
            Object.assign(form, {
                id: newUser.id,
                display_name: newUser.display_name,
                email: newUser.email,
                role: newUser.role,
                is_display_user: false // Mock data default
            });
            Object.keys(errors).forEach(key => delete errors[key]);
        }
    }, { immediate: true });

    const close = () => {
        emit('close');
    };

    const validate = () => {
        let isValid = true;
        Object.keys(errors).forEach(key => delete errors[key]);
        if (!form.display_name) { errors.display_name = "表示名は必須です"; isValid = false; }
        if (!form.email) { errors.email = "メールアドレスは必須です"; isValid = false; }
        return isValid;
    };

    const save = () => {
        if (!validate()) return;
        emit('save', { ...form });
    };

    watch(() => form.role, (newRole) => {
        if (newRole !== 'user') form.is_display_user = false;
    });
</script>