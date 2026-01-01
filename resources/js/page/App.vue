<template>
    <div class="flex h-screen w-full bg-gray-50" @click="handleClickOutside">
        <Sidebar 
            v-if="shouldShowLayout" 
            mode="dashboard" 
            :selectedNode="selectedNode"
            :isCollapsed="isCollapsed" 
            @update:selectedNode="selectedNode = $event"
            @toggle-sidebar="toggleSidebar"
            @tree-loaded="handleTreeData"
        />

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header v-if="shouldShowLayout"
                class="h-16 flex items-center justify-between px-6 bg-white border-b border-gray-200 shrink-0 z-20 shadow-sm">
                
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-4">
                        <button @click="toggleSidebar" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-7 w-7">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-panel-left">
                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                <path d="M9 3v18"></path>
                            </svg>
                            <span class="sr-only">Toggle Sidebar</span>
                        </button>
                    </div>
                    <div><div class="text-sm font-medium">[LOGO]</div></div>
                </div>

                <div class="flex items-center gap-4 relative" ref="dropdownRef">
                    <button @click="toggleDropdown" class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 gap-2 transition-opacity">
                        <User class="h-4 w-4" />
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-semibold">ユーザー: {{ displayName }}</div>
                        </div>
                    </button>

                    <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                        <div v-if="isDropdownOpen" class="absolute right-0 top-full mt-2 min-w-[8rem] origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500">アカウント</p>
                            </div>
                            <button @click="handleLogout" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <p class="flex items-center text-sm font-medium text-gray-900 truncate"><LogOut class="mr-2 h-4 w-4" /><span class="ml-2">ログアウト</span></p>
                            </button>
                        </div>
                    </transition>
                </div>

            </header>

            <main class="flex-1 overflow-auto bg-gray-50 relative">
                <router-view :treeData="treeData"></router-view>
            </main>
        </div>
    </div>
</template>

<script setup>
    import { computed, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { User, LogOut } from 'lucide-vue-next';
    import Sidebar from './components/layout/Sidebar.vue';
    import axios from 'axios';

    const route = useRoute();
    const router = useRouter();
    
    // --- CẬP NHẬT: Logic check hiển thị layout dựa vào Access Token ---
    const shouldShowLayout = computed(() => {
        if (route.path === '/login') return false;
        // Kiểm tra sự tồn tại của token
        return true;
    });

    const selectedNode = ref({
        id: "factory-1",
        name: "Factory 1 - Main Production",
        type: "factory",
    });

    const isCollapsed = ref(window.innerWidth < 768);
    const toggleSidebar = () => {
        isCollapsed.value = !isCollapsed.value;
    };

    const displayName = ref('Loading...');

    const fetchUserProfile = async () => {
        // --- CẬP NHẬT: Check token trước khi gọi API ---
        // const token = localStorage.getItem('access_token');
        if (route.path === '/login') return;

        try {
            const response = await axios.get('/api/get-user-login');
            displayName.value = response.data.user.display_name;
        } catch (error) {
            console.error('Failed to fetch user profile:', error);
            displayName.value = 'Unknown User';
        }
    };

    const treeData = ref([]); // State lưu trữ cây dữ liệu toàn cục

    // Hàm xử lý khi Sidebar gửi dữ liệu lên
    const handleTreeData = (data) => {
        treeData.value = data;
    };

    // Watch path để load user profile khi chuyển trang hoặc reload
    watch(
        () => route.path,
        (newPath) => {
            if (newPath != '/login') {
                fetchUserProfile();
            }
        },
        { immediate: true }
    );

    const isDropdownOpen = ref(false);
    const dropdownRef = ref(null);

    const toggleDropdown = () => {
        isDropdownOpen.value = !isDropdownOpen.value;
    };

    const handleClickOutside = (event) => {
        if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
            isDropdownOpen.value = false;
        }
    };

    const handleLogout = async () => {
        try {
            await axios.post('/api/logout');
        } catch (error) {
            console.error('Logout error', error);
        } finally {
            // Xóa dọn dẹp các key cũ nếu còn
            localStorage.removeItem('isAuthenticated');
            
            isDropdownOpen.value = false;
            router.push('/login');
        }
    };
</script>

<style scoped>
    svg {
        width: 1rem;
    }
</style>