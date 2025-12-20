<template>
    <BaseModal :isOpen="isOpen" title="新規ユーザー追加" @close="close">
        <div class="space-y-4">
            <p class="text-sm text-gray-500">新しいユーザーの情報を入力してください。</p>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    ユーザーID <span class="text-red-500">*</span>
                </label>
                <input 
                    v-model="form.user_id"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 transition-all"
                    :class="{'border-red-500': errors.user_id}" 
                    placeholder="半角英数字のみ (3-50文字)"
                >
                <p v-if="errors.user_id" class="text-xs text-red-500">{{ errors.user_id }}</p>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    表示名 <span class="text-red-500">*</span>
                </label>
                <input 
                    v-model="form.display_name"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 transition-all"
                    :class="{'border-red-500': errors.display_name}"
                >
                <p v-if="errors.display_name" class="text-xs text-red-500">{{ errors.display_name }}</p>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    メールアドレス <span class="text-red-500">*</span>
                </label>
                <input 
                    v-model="form.email" 
                    type="email"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 transition-all"
                    :class="{'border-red-500': errors.email}" 
                    placeholder="example@toyota.com"
                >
                <p v-if="errors.email" class="text-xs text-red-500">{{ errors.email }}</p>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    パスワード <span class="text-red-500">*</span>
                </label>
                <input 
                    v-model="form.password" 
                    type="password"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 transition-all"
                    :class="{'border-red-500': errors.password}"
                >
                <p v-if="errors.password" class="text-xs text-red-500">{{ errors.password }}</p>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    パスワード（確認） <span class="text-red-500">*</span>
                </label>
                <input 
                    v-model="form.password_confirm" 
                    type="password"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 transition-all"
                    :class="{'border-red-500': errors.password_confirm}"
                >
                <p v-if="errors.password_confirm" class="text-xs text-red-500">{{ errors.password_confirm }}</p>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    役割 <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-6 pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="radio" 
                            v-model="form.role" 
                            value="admin"
                            class="w-4 h-4 text-gray-900 border-gray-300 focus:ring-gray-900"
                        >
                        <span class="text-sm text-gray-700">管理者</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input 
                            type="radio" 
                            v-model="form.role" 
                            value="user"
                            class="w-4 h-4 text-gray-900 border-gray-300 focus:ring-gray-900"
                        >
                        <span class="text-sm text-gray-700">一般ユーザー</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <input 
                    type="checkbox" 
                    id="add_is_display_user" 
                    v-model="form.is_display_user"
                    class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 disabled:opacity-50" 
                    :disabled="form.role !== 'user'"
                >
                <label 
                    for="add_is_display_user" 
                    class="text-sm font-medium text-gray-700 cursor-pointer select-none"
                    :class="{'text-gray-400 cursor-not-allowed': form.role !== 'user'}"
                >
                    表示用ユーザー
                </label>
            </div>
        </div>

        <template #footer>
            <button 
                @click="close"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
                キャンセル
            </button>
            <button 
                @click="save"
                class="px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-800 transition-colors shadow-sm"
            >
                追加
            </button>
        </template>
    </BaseModal>
</template>

<script setup>
    import { reactive, watch } from 'vue';
    import BaseModal from '../common/BaseModal.vue';

    const props = defineProps(['isOpen']);
    const emit = defineEmits(['close', 'save']);

    const form = reactive({
        user_id: "", email: "", password: "", password_confirm: "", display_name: "", role: "user", is_display_user: false,
    });
    const errors = reactive({});

    const close = () => {
        emit('close');
        setTimeout(() => {
            Object.assign(form, { user_id: "", email: "", password: "", password_confirm: "", display_name: "", role: "user", is_display_user: false });
            Object.keys(errors).forEach(key => delete errors[key]);
        }, 200);
    };

    const validate = () => {
        let isValid = true;
        Object.keys(errors).forEach(key => delete errors[key]);

        // User ID regex validation
        const userIdRegex = /^[a-zA-Z0-9]+$/;
        if (!form.user_id) {
            errors.user_id = "ユーザーIDは必須です";
            isValid = false;
        } else if (form.user_id.length < 3) {
            errors.user_id = "ユーザーIDは3文字以上で入力してください";
            isValid = false;
        } else if (!userIdRegex.test(form.user_id)) {
            errors.user_id = "ユーザーIDは半角英数字のみ入力可能です";
            isValid = false;
        }

        if (!form.display_name) { errors.display_name = "表示名は必須です"; isValid = false; }
        if (!form.email) { errors.email = "メールアドレスは必須です"; isValid = false; }
        
        // Password validation
        if (!form.password) {
            errors.password = "パスワードは必須です";
            isValid = false;
        } else if (form.password.length < 8) {
            errors.password = "パスワードは8文字以上で入力してください";
            isValid = false;
        }

        if (form.password !== form.password_confirm) { 
            errors.password_confirm = "パスワードが一致しません"; 
            isValid = false; 
        }
        return isValid;
    };

    const save = () => {
        if (!validate()) return;
        emit('save', { ...form });
        close();
    };

    watch(() => form.role, (newRole) => {
        if (newRole !== 'user') form.is_display_user = false;
    });
</script>