<template>
    <div class="flex h-screen w-full bg-gray-50 overflow-hidden">
        <Sidebar
            class="hidden md:flex"
            mode="configuration"
            :activeConfigTab="activeTab"
            @update:activeConfigTab="handleNavigate"
        />

        <div class="flex-1 flex flex-col overflow-hidden relative">
            <ControlPanel mode="configuration" @toggleMode="goToDashboard" />

            <div class="flex-1 p-6 overflow-hidden min-h-0">
                <router-view />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import Sidebar from "../components/dashboard/Sidebar.vue";
import ControlPanel from "../components/dashboard/ControlPanel.vue";

const router = useRouter();
const route = useRoute();

// Xác định tab đang active dựa trên tên route hiện tại
const activeTab = computed(() => route.name);

// Xử lý khi click menu sidebar -> chuyển route
const handleNavigate = (tabId) => {
    router.push({ name: tabId });
};

// Xử lý khi click nút "Graph" -> quay về Dashboard
const goToDashboard = () => {
    router.push({ name: "dashboard" });
};
</script>
