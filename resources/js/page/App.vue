<template>
    <div class="flex h-screen w-full bg-gray-50" @click="handleClickOutside">
        <Sidebar 
            v-if="!isLoginPage" 
            mode="dashboard" 
            :selectedNode="selectedNode"
            :isCollapsed="isCollapsed" 
            @update:selectedNode="selectedNode = $event"
            @toggle-sidebar="toggleSidebar"
        />

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header v-if="!isLoginPage"
                class="h-16 flex items-center justify-between px-6 bg-white border-b border-gray-200 shrink-0 z-20 shadow-sm">
                <div class="flex items-center gap-3">
                    <div
                        class="flex items-center gap-4">
                        <button @click="toggleSidebar"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-7 w-7"
                            data-sidebar="trigger"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-panel-left">
                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                <path d="M9 3v18"></path>
                            </svg><span class="sr-only">Toggle Sidebar</span></button>
                    </div>
                    <div>
                        <div class="text-sm font-medium">[LOGO]</div>
                    </div>
                </div>

                <div class="flex items-center gap-4 relative" ref="dropdownRef">
                    <button 
                        @click="toggleDropdown"
                        class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 gap-2 transition-opacity"
                    >
                        <User className="h-4 w-4" />
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-semibold">ユーザー: {{ displayName }}</div>
                        </div>
                    </button>

                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div v-if="isDropdownOpen" class="absolute right-0 top-full mt-2 min-w-[8rem] origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500">アカウント</p>
                            </div>

                            <button @click="handleLogout"
                                class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                            >
                                <p class="flex items-center text-sm font-medium text-gray-900 truncate"><LogOut className="mr-2 h-4 w-4" /><span class="ml-2">ログアウト</span></p>
                            </button>
                        </div>
                    </transition>
                </div>
            </header>

            <main class="flex-1 overflow-auto bg-gray-50 relative">
                <router-view></router-view>
            </main>
        </div>
    </div>
</template>

<script setup>
    import { computed, ref, onMounted } from 'vue';
    import { useRoute } from 'vue-router';
    import { User, LogOut } from 'lucide-vue-next';
    import Sidebar from './components/layout/Sidebar.vue';

    const route = useRoute();
    const isLoginPage = computed(() => route.path === '/login');
    const userRole = localStorage.getItem('userRole') || 'Guest';

    const selectedNode = ref({
        id: "factory-1",
        name: "Factory 1 - Main Production",
        type: "factory",
    });

    // STATE SIDEBAR
    // Mặc định: Nếu màn hình nhỏ (< 768px) -> Collapsed (Đóng) = true. Ngược lại -> Mở = false (hoặc true tùy ý bạn).
    const isCollapsed = ref(window.innerWidth < 768);

    const toggleSidebar = () => {
        isCollapsed.value = !isCollapsed.value;
    };

    // State lưu tên hiển thị
    const displayName = ref('Loading...');
    // Hàm fetch thông tin user
    const fetchUserProfile = async () => {
        try {
            // Chỉ gọi API nếu không phải trang login và đã có token/session
            if (!isLoginPage.value) {
                const response = await axios.get('/api/get-user-login');
                displayName.value = response.data.user.display_name;
            }
        } catch (error) {
            console.error('Failed to fetch user profile:', error);
            displayName.value = 'Guest';
        }
    };

    // --- LOGIC DROPDOWN ---
    const isDropdownOpen = ref(false);
    const dropdownRef = ref(null);

    const toggleDropdown = () => {
        isDropdownOpen.value = !isDropdownOpen.value;
    };

    // Đóng dropdown khi click ra ngoài
    const handleClickOutside = (event) => {
        if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
            isDropdownOpen.value = false;
        }
    };

    // --- LOGIC LOGOUT ---
    const handleLogout = async () => {
        try {
            // Gọi API Logout (để xóa session trên server)
            await axios.post('/api/logout');
        } catch (error) {
            console.error('Logout error', error);
        } finally {
            // Luôn thực hiện xóa storage và redirect kể cả khi API lỗi
            localStorage.removeItem('isAuthenticated');
            localStorage.removeItem('isPersistent');
            localStorage.removeItem('sessionExpiry');
            localStorage.removeItem('userRole');
            
            isDropdownOpen.value = false;
            router.push('/login');
        }
    };

    // Gọi API khi component được mount
    onMounted(() => {
        fetchUserProfile();
    });
</script>
<style scoped>
    svg {
        width: 1rem;
    }
</style>