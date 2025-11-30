<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity"
    >
        <div
            class="bg-white rounded-xl shadow-2xl w-full max-w-md m-4 animate-in fade-in zoom-in-95 duration-200 overflow-hidden"
        >
            <div
                class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50"
            >
                <h3 class="text-lg font-bold text-gray-900">
                    {{ mode === "add" ? "ユーザー追加" : "ユーザー編集" }}
                </h3>
                <button
                    @click="handleClose"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <X class="h-5 w-5" />
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700"
                        >ユーザー名 <span class="text-red-500">*</span></label
                    >
                    <input
                        v-model="formData.username"
                        type="text"
                        class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[hsl(195,85%,45%)] focus:border-transparent transition-all"
                        placeholder="Ex: user_01"
                    />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700"
                        >氏名 <span class="text-red-500">*</span></label
                    >
                    <input
                        v-model="formData.fullName"
                        type="text"
                        class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[hsl(195,85%,45%)] focus:border-transparent transition-all"
                        placeholder="Ex: 山田 太郎"
                    />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700"
                        >役割</label
                    >
                    <select
                        v-model="formData.role"
                        class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[hsl(195,85%,45%)] transition-all"
                    >
                        <option value="user">一般ユーザー</option>
                        <option value="admin">管理者</option>
                    </select>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="text-sm font-medium text-gray-700"
                        >状態 (有効/無効)</label
                    >
                    <button
                        @click="formData.isActive = !formData.isActive"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                        :class="
                            formData.isActive
                                ? 'bg-[hsl(195,85%,45%)]'
                                : 'bg-gray-200'
                        "
                    >
                        <span
                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"
                            :class="
                                formData.isActive
                                    ? 'translate-x-6'
                                    : 'translate-x-1'
                            "
                        />
                    </button>
                </div>
            </div>

            <div
                class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3"
            >
                <button
                    @click="handleClose"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none"
                >
                    キャンセル
                </button>
                <button
                    @click="handleSubmit"
                    class="px-4 py-2 text-sm font-medium text-white bg-[hsl(195,85%,45%)] rounded-md hover:bg-[hsl(195,85%,40%)] focus:outline-none shadow-sm"
                >
                    保存
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch } from "vue";
import { X } from "lucide-vue-next";

const props = defineProps({
    isOpen: Boolean,
    mode: String, // 'add' | 'edit'
    initialData: Object, // Dữ liệu user cần edit (nếu có)
});

const emit = defineEmits(["close", "save"]);

// State form nội bộ
const formData = reactive({
    id: null,
    username: "",
    fullName: "",
    role: "user",
    isActive: true,
});

// Khi modal mở hoặc initialData thay đổi, cập nhật form
watch(
    () => props.isOpen,
    (newVal) => {
        if (newVal) {
            if (props.mode === "edit" && props.initialData) {
                Object.assign(formData, props.initialData); // Fill data
            } else {
                // Reset form for Add mode
                formData.id = null;
                formData.username = "";
                formData.fullName = "";
                formData.role = "user";
                formData.isActive = true;
            }
        }
    }
);

const handleClose = () => {
    emit("close");
};

const handleSubmit = () => {
    // Validate đơn giản
    if (!formData.username || !formData.fullName) {
        alert("ユーザー名と氏名は必須です。");
        return;
    }

    // Gửi data ra ngoài cho cha xử lý
    emit("save", { ...formData });
};
</script>
